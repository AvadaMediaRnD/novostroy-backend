<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use common\helpers\PriceHelper;

/**
 * This is the model class for table "flat".
 *
 * @property int $id
 * @property string $unit_type
 * @property int $number
 * @property string $number_index
 * @property int $n_rooms
 * @property int $floor
 * @property double $square
 * @property string $price_m
 * @property string $price_sell_m
 * @property string $price_discount_m
 * @property string $price_paid_init
 * @property double $price_paid_out
 * @property double $commission_agency
 * @property double $commission_agency_type
 * @property double $commission_manager
 * @property double $commission_manager_type
 * @property string $description
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $house_id
 * @property int $client_id
 * @property int $agency_id
 *
 * @property Agreement $agreement
 * @property Client $client
 * @property Agency $agency
 * @property House $house
 * @property Invoice[] $invoices
 * @property Payment[] $payments
 */
class Flat extends \common\models\ZModel {

    const STATUS_AVAILABLE = 10;
    const STATUS_BOOKED = 8;
    const STATUS_RESERVED = 7;
    const STATUS_READY_BUY = 5;
    const STATUS_NOT_SELL = 2;
    const STATUS_SOLD = 1;
    const STATUS_UNAVAILABLE = 0;
    const COMMISSION_TYPE_PERCENT = 0;
    const COMMISSION_TYPE_VALUE = 1;
    const TYPE_FLAT = 'flat';
    const TYPE_OFFICE = 'office';
    const TYPE_PARKING = 'parking';
    const TYPE_CAR_PLACE = 'car_place';
    const TYPE_COMMERCIAL = 'commercial';
    const TYPE_STORAGE = 'storage';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'flat';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['number', 'n_rooms', 'floor', 'status', 'created_at', 'updated_at', 'house_id', 'client_id', 'commission_agency_type', 'commission_manager_type'], 'integer'],
            [['square', 'price_m', 'price_sell_m', 'price_discount_m', 'price_paid_init', 'commission_agency', 'commission_manager', 'price_paid_out'], 'number'],
            [['description'], 'string'],
            [['unit_type', 'number_index'], 'string', 'max' => 255],
            ['unit_type', 'default', 'value' => static::TYPE_FLAT],
            [['house_id', 'number', 'status'], 'required'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['house_id'], 'exist', 'skipOnError' => true, 'targetClass' => House::className(), 'targetAttribute' => ['house_id' => 'id']],
            ['number', 'unique', 'targetAttribute' => ['number', 'unit_type', 'house_id'], 'on' => 'create'],
            ['price_paid_init', 'default', 'value' => 0.0000],
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['import_update'] = ['price_paid_init'];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'unit_type' => Yii::t('app', 'Тип помещения'),
            'number' => Yii::t('app', '№'),
            'number_index' => Yii::t('app', 'Индекс'),
            'n_rooms' => Yii::t('app', 'Кол-во комнат'),
            'floor' => Yii::t('app', '№ этажа'),
            'square' => Yii::t('app', 'Площадь (м2)'),
            'price_m' => Yii::t('app', 'Цена за м2 (USD)'),
            'price_sell_m' => Yii::t('app', 'Цена продажи за м2 (USD)'),
            'price_discount_m' => Yii::t('app', 'Скидка за м2 (USD)'),
            'price_paid_init' => Yii::t('app', 'Изначально уплаченная сумма (USD)'),
            'price_paid_out' => Yii::t('app', 'Остаток суммы (USD)'),
            'commission_agency' => Yii::t('app', 'Комиссия агентства'),
            'commission_manager' => Yii::t('app', 'Комиссия менеджера'),
            'commission_agency_type' => Yii::t('app', 'Тип комиссии агентства'),
            'commission_manager_type' => Yii::t('app', 'Тип комиссии менеджера'),
            'description' => Yii::t('app', 'Описание'),
            'status' => Yii::t('app', 'Статус'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'house_id' => Yii::t('app', 'Дом (секция)'),
            'client_id' => Yii::t('app', 'Покупатель'),
            'agency_id' => Yii::t('app', 'Агентство'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert) {
        if ($this->price_discount_m === null) {
            $this->price_discount_m = $this->price_m - $this->price_sell_m;
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes) {
        if (($insert && $this->price_paid_init) || (isset($changedAttributes['price_paid_init']) && $this->scenario !== 'import_update')) {
            $currencyUsd = Cashbox::getCashboxByCurrency(Cashbox::CURRENCY_USD);

            // invoice
            $invoiceModel = Invoice::findOne(['type' => Article::TYPE_INCOME, 'flat_id' => $this->id, 'article_id' => Article::getIdPaydeFlat()]);

            if ($invoiceModel === null) {
                $invoiceModel = Invoice::findOne(['type' => Article::TYPE_INCOME, 'flat_id' => $this->id, 'article_id' => Article::getIdInitialPayment()]);
            }

            if ($invoiceModel === null) {
                $invoiceModel = Invoice::findOne(['type' => Article::TYPE_INCOME, 'flat_id' => $this->id, 'article_id' => Article::getIdReserveFlat()]);
            }

            // Set pay status
            if (isset($this->status)) {

                $status = (int) $this->status;
                $article = Article::getIdPaydeFlat();

                switch ($status) {
                    case self::STATUS_RESERVED: $invStatus = Invoice::STATUS_COMPLETE;
                        $invStatusDesc = 'Резерв';
                        $article = Article::getIdReserveFlat();
                        break;
                    case self::STATUS_SOLD: $invStatus = Invoice::STATUS_COMPLETE;
                        $invStatusDesc = 'Продажа';
                        $article = Article::getIdPaydeFlat();
                        break;
                    default : $invStatus = Invoice::STATUS_WAITING;
                        $invStatusDesc = 'Продажа';
                        $article = Article::getIdPaydeFlat();
                        break;
                }
            } else {
                $invStatus = Invoice::STATUS_WAITING;
                $invStatusDesc = 'Продажа';
                $article = Article::getIdPaydeFlat();
            }

            // delete invoice and payment if price_paid_init is 0
            if ($invoiceModel !== null && $this->price_paid_init == 0) {
                $paymentModel = $invoiceModel->payment;
                $invoiceModel->delete();
                if (isset($paymentModel) && $paymentModel->price_plan == 0) {
                    $paymentModel->delete();
                }
            } elseif ($invoiceModel === null && $this->price_paid_init != 0) {
                if (!isset($invoiceModel)) {
                    $invoiceModel = new Invoice();
                    $invoiceModel->type = Article::TYPE_INCOME;
                    $invoiceModel->flat_id = $this->id;
                    $invoiceModel->article_id = $article;
                    $invoiceModel->cashbox_id = $currencyUsd->id;
                    $invoiceModel->client_id = $this->client_id;
                    $invoiceModel->agency_id = $this->agency_id;
                    $invoiceModel->user_id = $this->client->user_id ?? Yii::$app->user->id;
                    $invoiceModel->uid_date = date('Y-m-d');
                    $invoiceModel->generateUid();
                    $invoiceModel->description = $invStatusDesc;
                    $invoiceModel->status = $invStatus;
                } else {
                    $invoiceModel->article_id = $article;
                    $invoiceModel->description = $invStatusDesc;
                    $invoiceModel->updated_at = time();
                    $invoiceModel->save();
                }

                // payment
                $paymentModel = $invoiceModel->payment ?: new Payment();
                if ($paymentModel->isNewRecord) {
                    $paymentModel->pay_number = 1;
                    $paymentModel->pay_date = date('Y-m-d');
                    $paymentModel->flat_id = $this->id;
                }
                $paymentModel->price_plan = $this->price_paid_init;
                $paymentModel->price_fact = $this->price_paid_init;
                $paymentModel->price_saldo = 0-$paymentModel->price_saldo ?? 0;
                $paymentModel->save();

                // incoive continue
                $invoiceModel->price = $this->price_paid_init;
                $invoiceModel->rate = $currencyUsd->rate;
                $invoiceModel->payment_id = $paymentModel->id;
                //// LOG
                if ($invoiceModel->isNewRecord) {
                    $invoiceModel->saveLog(__METHOD__ . ' flat:' . $this->id);
                }
                //// END LOG
                $invoiceModel->save();
            } elseif ($invoiceModel !== null && $this->price_paid_init != 0) {
                $invoiceModel->article_id = Article::getIdPaydeFlat();
                $invoiceModel->description = $invStatusDesc;
                $invoiceModel->updated_at = time();
                $invoiceModel->save();
            }
        }

        // agency changed. update agreement
        if (isset($changedAttributes['agency_id'])) {
            $agreement = $this->agreement;
            if ($agreement) {
                $agreement->agency_id = $this->agency_id;
                //$agreement->save(false);
            }
        }

        // update payments list
        if ($insert || !$insert) {
            /*
              $payments = $this->payments;
              $sum = array_sum(ArrayHelper::getColumn($payments, 'price_plan'));
              $priceSell = $this->getPriceSell();

              if ($sum != $priceSell) {
              // need update
              $priceLeftPayment = $this->getPayments()->where(['is_price_left' => 1])->one();
              if ($priceLeftPayment == null) {
              $priceLeftPayment = new Payment();
              $priceLeftPayment->pay_number = $this->getPayments()->max('pay_number') + 1;
              $priceLeftPayment->is_price_left = 1;
              $priceLeftPayment->pay_date = date('Y-m-d');
              $priceLeftPayment->flat_id = $this->id;
              }

              $priceLeftPayment->price_plan = $priceSell - $sum;
              $priceLeftPayment->save();
              }
             * 
             */
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreement() {
        return $this->hasOne(Agreement::className(), ['flat_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient() {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgency() {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHouse() {
        return $this->hasOne(House::className(), ['id' => 'house_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices() {
        return $this->hasMany(Invoice::className(), ['flat_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments() {
        return $this->hasMany(Payment::className(), ['flat_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComplatedPayments() {
        return $this->hasMany(Payment::className(), ['flat_id' => 'id'])->joinWith('invoices',false)->where(['invoice.status' => Invoice::STATUS_COMPLETE]);
    }

    /**
     * @return array
     */
    public static function getStatusOptions() {
        return [
            static::STATUS_AVAILABLE => Yii::t('model', 'Активна'),
            static::STATUS_BOOKED => Yii::t('model', 'Бронь'),
            static::STATUS_RESERVED => Yii::t('model', 'Резерв'),
            static::STATUS_READY_BUY => Yii::t('model', 'Готовы покупать'),
            static::STATUS_NOT_SELL => Yii::t('model', 'Снята с продаж'),
            static::STATUS_SOLD => Yii::t('model', 'Продана'),
            static::STATUS_UNAVAILABLE => Yii::t('model', 'Неактивна'),
        ];
    }

    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabel($status = null) {
        $status = $status == null ? $this->status : $status;
        $options = static::getStatusOptions();
        return isset($options[$status]) ? $options[$status] : null;
    }

    /**
     * Get status value by input label
     * @param type $label
     * @return type
     */
    public static function getStatusByLabel($label) {
        return array_search($label, static::getStatusOptions());
    }

    /**
     * @return array
     */
    public static function getCommissionTypeOptions() {
        return [
            static::COMMISSION_TYPE_PERCENT => Yii::t('model', 'Процент'),
            static::COMMISSION_TYPE_VALUE => Yii::t('model', 'Фиксировано'),
        ];
    }

    /**
     * @param null $type
     * @return mixed|null
     */
    public function getCommissionAgencyTypeLabel($type = null) {
        $type = $type == null ? $this->commission_agency_type : $type;
        $options = static::getCommissionTypeOptions();
        return isset($options[$type]) ? $options[$type] : null;
    }

    /**
     * @param null $type
     * @return mixed|null
     */
    public function getCommissionManagerTypeLabel($type = null) {
        $type = $type == null ? $this->commission_manager_type : $type;
        $options = static::getCommissionTypeOptions();
        return isset($options[$type]) ? $options[$type] : null;
    }

    /**
     * @return array
     */
    public static function getUnitTypeOptions() {
        return [
            static::TYPE_FLAT => Yii::t('model', 'Квартира'),
            static::TYPE_OFFICE => Yii::t('model', 'Офис'),
            static::TYPE_PARKING => Yii::t('model', 'Паркинг'),
            static::TYPE_CAR_PLACE => Yii::t('model', 'Машино-место'),
            static::TYPE_COMMERCIAL => Yii::t('model', 'Коммерческое'),
            static::TYPE_STORAGE => Yii::t('model', 'Кладовка'),
        ];
    }

    /**
     * @param null $unit_type
     * @return mixed|null
     */
    public function getUnitTypeLabel($unit_type = null) {
        $unit_type = $unit_type == null ? $this->unit_type : $unit_type;
        $options = static::getUnitTypeOptions();
        return isset($options[$unit_type]) ? $options[$unit_type] : null;
    }

    /**
     * Get unit type value by input label
     * @param type $label
     * @return type
     */
    public static function getUnitTypeByLabel($label) {
        return array_search($label, static::getUnitTypeOptions());
    }

    /**
     * 
     * @return float
     */
    public function getPrice() {
        if ($this->unit_type == static::TYPE_CAR_PLACE || $this->unit_type == static::TYPE_PARKING) {
            return $this->price_m;
        }
        return $this->square * $this->price_m;
    }

    /**
     * 
     * @return float
     */
    public function getPriceSell() {
        if ($this->unit_type == static::TYPE_CAR_PLACE || $this->unit_type == static::TYPE_PARKING) {
            if (!isset($this->price_sell_m)) {
                return 0.00;
            }
            return $this->price_sell_m;
        }
        if (!isset($this->square) || empty(trim($this->square))) {
            $this->square = 0;
        }
        if (isset($this->price_sell_m)) {
            $pricem = floatval($this->price_sell_m);
        } else {
            $pricem = 0.00;
        }

        return $this->square * $pricem;
    }

    /**
     * 
     * @return float
     */
    public function getPriceSellFlats() {
        if ($this->unit_type !== static::TYPE_CAR_PLACE || $this->unit_type !== static::TYPE_PARKING) {

            if (!isset($this->square) || empty(trim($this->square))) {
                $this->square = 0;
            }
            if (isset($this->price_sell_m)) {
                $pricem = floatval($this->price_sell_m);
            } else {
                $pricem = 0.00;
            }
        }

        return $this->square * $pricem;
    }

    /**
     * 
     * @return float
     */
    public function getPriceRest() {

        if (isset($this->price_paid_out)) {
            $pricem = floatval($this->price_paid_out);
        } else {
            $pricem = 0.00;
        }

        return $pricem;
    }

    /**
     * 
     * @return float
     */
    public function getPriceDiscount() {
        if ($this->unit_type == static::TYPE_CAR_PLACE || $this->unit_type == static::TYPE_PARKING) {
            return $this->price_discount_m;
        }
        return $this->square * $this->price_discount_m;
    }

    /**
     * 
     * @return float
     */
    public function getPaymentBefore() {
        $value = $this->getPayments()->where(['<=', 'pay_date', date('Y-m-d')])->sum(new \yii\db\Expression('price_fact - price_plan'));
        return $value > 0 ? floatval($value) : 0;
    }

    /**
     * 
     * @return float
     */
    public function getPaymentFact() {
        return floatval($this->getPayments()->sum('price_fact'));
    }
    
    /**
     * 
     * @return float
     */
    public function getPaymentFactComplated() {
        return floatval($this->getComplatedPayments()->sum('price_fact'));
    }
    
    
    /**
     * 
     * @return float
     */
    public function getPaymentPlan() {
        return floatval($this->getPayments()->sum('price_plan'));
    }

    /**
     * 
     * @return float
     */
    public function getPaymentLeft() {
        return floatval($this->getPayments()->sum(new \yii\db\Expression('`price_plan` - `price_fact`')));
    }
    
    /**
     * 
     * @return float
     */
    public function getPaymentLeftComplated() {
        return $this->getPaymentPlan() - $this->getPaymentFactComplated();
    }

    /**
     * Count total value of commission for manager
     * @return float
     */
    public function getCommissionManagerPrice() {
        if ($this->commission_manager_type == static::COMMISSION_TYPE_PERCENT) {
            return round($this->getPriceSell() * ($this->commission_manager / 100), 2);
        }
        return $this->commission_manager;
    }

    /**
     * Count total value of commission for agency
     * @return float
     */
    public function getCommissionAgencyPrice() {
        if ($this->commission_agency_type == static::COMMISSION_TYPE_PERCENT) {
            return round($this->getPriceSell() * ($this->commission_agency / 100), 2);
        }
        return $this->commission_agency;
    }

    /**
     * Check if current flat has agreement and it is signed
     * @return boolean
     */
    public function hasAgreementSigned() {
        return $this->getAgreement()->exists() && $this->agreement->status == Agreement::STATUS_SIGNED;
    }

    /**
     * Get string for number/number_index
     * @return string
     */
    public function getNumberWithIndex() {
        if (!$this->number_index || !strlen($this->number_index)) {
            return $this->number;
        }
        return $this->number . '/' . $this->number_index;
    }

    /**
     * 
     * @return string
     */
    public function getBuildNumber() {
        return 'НОМЕР СТРОЙ';
    }

    /**
     * 
     * @param boolean $isFullPath
     * @param boolean $isForAgreement
     * @return string
     */
    public function getFloorFlatImg($isFullPath = false, $isForAgreement = false) {
        $path = '/upload/House/plan/' . $this->floor . '.jpg';

        $path = '/upload/placeholder.jpg';

        if ($isFullPath || $isForAgreement) {
            $fullPath = Yii::getAlias('@frontend/web' . $path);
            if (!$isForAgreement) {
                return $fullPath;
            }

            if (file_exists($fullPath) && is_readable($fullPath)) {
                $imgFile = file_get_contents($fullPath);
                return $imgFile;
            } else {
                return null;
            }
        }
        return $path;
    }

    /**
     * @param Agreement $agreementModel
     * @return string
     */
    public function getPaymentPlanForAgreement($agreementModel) {
        $cashboxUsd = Cashbox::getCashboxByCurrency(Cashbox::CURRENCY_USD);

        $html = '<table border="1" style="border:1px solid #00000a; width:500px">
<tbody>';
        $html .= '
<tr>
    <td style="border-color:#00000a">
        <span style="font-size:10px"><span style="font-family:freesans">№ <strong>платежів п/п</strong></span></span>
    </td>
    <td style="border-color:#00000a">
        <span style="font-size:10px"><span style="font-family:freesans"><strong>Сума чергового платежу в доларах США в еквіваленті гривні України</strong></span></span>
        <span style="font-size:10px"><span style="font-family:freesans"><strong>/$/</strong></span></span>
    </td>
    <td colspan="3" style="border-color:#00000a">
        <span style="font-size:10px"><span style="font-family:freesans"><strong>Сума чергового платежу в національній валюті</strong></span></span>
        <span style="font-size:10px"><span style="font-family:freesans"><strong>/ГРН/</strong></span></span>
    </td>
    <td style="border-color:#00000a">
        <span style="font-size:10px"><span style="font-family:freesans"><strong>Дата чергового платежу</strong></span></span>
    </td>
    <td colspan="2" style="border-color:#00000a">
        <span style="font-size:10px"><span style="font-family:freesans"><strong>У день підписання договору </strong></span></span>
        <span style="font-size:10px"><span style="font-family:freesans"><span style="color:#000000">№<strong>-</strong><span style="color:#000000"><strong>' . $agreementModel->uid . ', від ' . $agreementModel->getUidDate() . ' року</strong></span></span></span></span>
        <span style="font-size:10px"><span style="font-family:freesans"><strong>курс продажі доларів США на міжбанківському валютному ринку України складає:</strong></span></span>
        <span style="font-size:10px"><span style="font-family:freesans"><strong>' . PriceHelper::rateText($cashboxUsd->rate) . ' за 1 (один) долар США</strong></span></span>
        &nbsp;
    </td>
</tr>';

        // loop
        $paymentPricePlanTotal = 0;
        $paymentPricePlanUahTotal = 0;
        foreach ($this->payments as $payment) {
            $paymentPricePlan = round($payment->price_plan, 2);
            $paymentPricePlanUah = round($cashboxUsd->rate * $payment->price_plan, 2);
            $paymentPricePlanTotal += $paymentPricePlan;
            $paymentPricePlanUahTotal += $paymentPricePlanUah;
            $html .= '
<tr>
    <td style="border-color:#00000a">
        <span style="font-size:10px"><span style="font-family:freesans">' . $payment->pay_number . '</span></span>
    </td>
    <td style="border-color:#00000a">
        <span style="font-size:10px"><span style="font-family:freesans">' . $paymentPricePlan . '</span></span>
    </td>
    <td colspan="3" style="border-color:#00000a">
        <span style="font-size:10px"><span style="font-family:freesans">' . $paymentPricePlanUah . '</span></span>
    </td>
    <td style="border-color:#00000a">
        <span style="font-size:10px"><span style="font-family:freesans">' . date('d.m.Y', strtotime($payment->pay_date)) . '</span></span>
    </td>
    <td colspan="2" style="border-color:#00000a">
        &nbsp;
    </td>
</tr>';
        }
        // end loop

        $html .= '
<tr>
    <td style="border-color:#00000a">
        <span style="font-size:10px"><span style="font-family:freesans"><strong>Всього:</strong></span></span>
    </td>
    <td style="border-color:#00000a">
        <span style="font-size:10px"><span style="font-family:freesans"><strong>' . $paymentPricePlanTotal . '</strong></span></span>
    </td>
    <td colspan="3" style="border-color:#00000a">
        <span style="font-size:10px"><span style="font-family:freesans"><strong>' . $paymentPricePlanUahTotal . '</strong></span></span>
    </td>
    <td style="border-color:#00000a">
        &nbsp;
    </td>
    <td colspan="2" style="border-color:#00000a">
        &nbsp;
    </td>
</tr>';
        $html .= '</tbody>
</table>';

        return $html;
    }

    /**
     * Create report file for sales in period
     * @param string $dateFrom
     * @param string $dateTo
     */
    public function getReportFile($dateFrom = '', $dateTo = '') {
        // date range default
        if (!$dateFrom) {
            $dateFrom = date('Y-01-01');
        }
        if (!$dateTo) {
            $dateTo = date('Y-m-d');
        }

        $dirSub = '/upload/Flat/report';
        $dir = Yii::getAlias('@frontend/web' . $dirSub);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $reportName = 'report-' . date('Ymd', strtotime($dateFrom)) . '-' . date('Ymd', strtotime($dateTo));
        $pathNoExt = $dir . '/' . $reportName . '.';
        $path = $pathNoExt . 'xls';
        $pathWeb = $dirSub . '/' . $reportName . '.' . 'xls';

        $xls = new \PHPExcel();

        $months = []; // [2018-01, 2018-02, 2018-03, ... , 2019-01]
        for ($y = date('Y', strtotime($dateFrom)); $y <= date('Y', strtotime($dateTo)); $y++) {
            $mStart = ($y == date('Y', strtotime($dateFrom)) ? date('n', strtotime($dateFrom)) : 1);
            $mEnd = ($y == date('Y', strtotime($dateTo)) ? date('n', strtotime($dateTo)) : 12);
            for ($m = $mStart; $m <= $mEnd; $m++) {
                $months[] = $y . '-' . sprintf('%02d', $m);
            }
        }

        // Houses
        $houseModels = House::find()
                ->groupBy('name')
                ->orderBy(['name' => SORT_ASC])
                ->all();

        foreach ($houseModels as $k => $houseModel) {
            $row = 1;
            $rowShift = 0;

            if ($k > 0) {
                $xls->createSheet($k);
            }
            $xls->setActiveSheetIndex($k);
            $sheet = $xls->getActiveSheet();
            $sheet->setTitle($houseModel->name);

            $houseReportTitle = $houseModel->name . ' - отчет';
            if ($dateFrom) {
                $houseReportTitle .= ' c ' . date('d.m.Y', strtotime($dateFrom));
            }
            if ($dateTo) {
                $houseReportTitle .= ' по ' . date('d.m.Y', strtotime($dateTo));
            }

            $sheet->setCellValue('A' . ($row + $rowShift), $houseReportTitle);
            $sheet->mergeCells('A' . ($row + $rowShift) . ':I' . ($row + $rowShift));
            $sheet->getStyle('A' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . ($row + $rowShift))->getFont()->setBold(true);
            $sheet->getRowDimension(($row + $rowShift))->setRowHeight(40);

            $sectionModels = House::find()
                    ->where(['name' => $houseModel->name])
                    ->orderBy(['`section` + 0' => SORT_ASC])
                    ->all();
            $sectionIds = \yii\helpers\ArrayHelper::getColumn($sectionModels, 'id');
            // empty model for total stats
            $sectionModels[] = new House();
            $sectionModels[] = new House(['name' => static::TYPE_PARKING]);
            $sectionModels[] = new House(['name' => static::TYPE_CAR_PLACE]);

            foreach ($sectionModels as $j => $sectionModel) {
                $rowShift += 1;
                if ($sectionModel->isNewRecord) {
                    if ($sectionModel->name == static::TYPE_PARKING) {
                        $sectionReportTitle = 'ПАРКИНГИ';
                    } elseif ($sectionModel->name == static::TYPE_CAR_PLACE) {
                        $sectionReportTitle = 'МАШИНО-МЕСТА';
                    } else {
                        $sectionReportTitle = 'ОБЩИЕ ДАННЫЕ';
                    }
                } else {
                    $sectionReportTitle = 'Секция: ' . $sectionModel->section;
                }
                $sheet->setCellValue('A' . ($row + $rowShift), $sectionReportTitle);
                $sheet->mergeCells('A' . ($row + $rowShift) . ':I' . ($row + $rowShift));
                $sheet->getStyle('A' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A' . ($row + $rowShift))->getFont()->setBold(true);
                $sheet->getRowDimension(($row + $rowShift))->setRowHeight(40);

                $rowShift += 1;
                $sheet->setCellValue('A' . ($row + $rowShift), 'Общая площадь');
                $sheet->setCellValue('B' . ($row + $rowShift), 'Кв.м/Продано');
                $sheet->mergeCells('B' . ($row + $rowShift) . ':C' . ($row + $rowShift));
                $sheet->setCellValue('D' . ($row + $rowShift), 'Прод. за период');
                $sheet->setCellValue('E' . ($row + $rowShift), 'Кв.м/В наличии');
                $sheet->mergeCells('E' . ($row + $rowShift) . ':F' . ($row + $rowShift));
                $sheet->setCellValue('G' . ($row + $rowShift), 'Общая стоимость');
                $sheet->setCellValue('H' . ($row + $rowShift), 'Внесено');
                $sheet->setCellValue('I' . ($row + $rowShift), 'Долг');

                $sheet->getStyle('A' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//                $maxFloorQuery = Flat::find();
//                if ($sectionModel->isNewRecord) {
//                    $maxFloorQuery->where(['in', 'house_id', $sectionIds]);
//                } else {
//                    $maxFloorQuery->where(['house_id' => $sectionModel->id]);
//                }
//                $maxFloor = $maxFloorQuery->max('floor');
//                for ($floor = 1; $floor <= $maxFloor; $floor++) {
//                    $pricePaidFloorQuery = Payment::find()->joinWith('flat')->where(['floor' => $floor]);
//                    if ($sectionModel->isNewRecord) {
//                        $pricePaidFloorQuery->andWhere(['in', 'house_id', $sectionIds]);
//                    } else {
//                        $pricePaidFloorQuery->andWhere(['house_id' => $sectionModel->id]);
//                    }
//                    $pricePaidFloor = $pricePaidFloorQuery->sum('price_fact');
//                    $sheet->setCellValue(static::getXlsColFromNumber(8+$floor).($row+$rowShift+1), $pricePaidFloor);
//                    $sheet->setCellValue(static::getXlsColFromNumber(8+$floor).($row+$rowShift), $floor);
//                }

                foreach ($months as $k => $month) {
                    $pricePaidMonthQuery = Payment::find()->joinWith(['flat', 'invoices'])->where(['between', 'invoice.uid_date', $month . '-01', $month . '-' . date('t', strtotime($month . '-01'))]);
                    $pricePaidMonthQuery->andWhere(['invoice.status' => Invoice::STATUS_COMPLETE, 'invoice.type' => Invoice::TYPE_INCOME]);
                    if ($sectionModel->isNewRecord) {
                        $pricePaidMonthQuery->andWhere(['in', 'flat.house_id', $sectionIds]);
                    } else {
                        $pricePaidMonthQuery->andWhere(['flat.house_id' => $sectionModel->id]);
                    }

                    if ($sectionModel->isNewRecord && $sectionModel->name == static::TYPE_PARKING) {
                        $pricePaidMonthQuery->andWhere(['flat.unit_type' => static::TYPE_PARKING]);
                    } elseif ($sectionModel->isNewRecord && $sectionModel->name == static::TYPE_CAR_PLACE) {
                        $pricePaidMonthQuery->andWhere(['flat.unit_type' => static::TYPE_CAR_PLACE]);
                    } else {
                        $pricePaidMonthQuery->andWhere(['not in', 'flat.unit_type', [static::TYPE_PARKING, static::TYPE_CAR_PLACE]]);
                    }

                    $pricePaidMonth = $pricePaidMonthQuery->sum('price_fact');
                    $sheet->setCellValue(static::getXlsColFromNumber(9 + $k + 1) . ($row + $rowShift + 1), $pricePaidMonth ? round($pricePaidMonth, 0) : 0);
                    $sheet->setCellValue(static::getXlsColFromNumber(9 + $k + 1) . ($row + $rowShift), $month);

                    $sheet->getStyle(static::getXlsColFromNumber(9 + $k + 1) . ($row + $rowShift + 1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle(static::getXlsColFromNumber(9 + $k + 1) . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $sheet->getColumnDimension(static::getXlsColFromNumber(9 + $k + 1))->setWidth(12);
                }

                // flat
                $queryTotal = Flat::find()->joinWith(['payments', 'invoices']);
                $queryTotal->andWhere(['invoice.status' => Invoice::STATUS_COMPLETE, 'invoice.type' => Invoice::TYPE_INCOME]);

                if ($sectionModel->isNewRecord) {
                    $queryTotal->where(['in', 'flat.house_id', $sectionIds]);
                } else {
                    $queryTotal->where(['flat.house_id' => $sectionModel->id]);
                }

                if ($sectionModel->isNewRecord && $sectionModel->name == static::TYPE_PARKING) {
                    $queryTotal->andWhere(['flat.unit_type' => static::TYPE_PARKING]);
                } elseif ($sectionModel->isNewRecord && $sectionModel->name == static::TYPE_CAR_PLACE) {
                    $queryTotal->andWhere(['flat.unit_type' => static::TYPE_CAR_PLACE]);
                } else {
                    $queryTotal->andWhere(['not in', 'flat.unit_type', [static::TYPE_PARKING, static::TYPE_CAR_PLACE]]);
                }

                $queryTotalAlltime = clone $queryTotal;

                // date range condition
                $cond = ['or', ['is_price_left' => 1]];

                $queryTotalAlltime->andWhere($cond);
                $queryTotalAlltime->distinct();

                $condDateFrom = '';
                $condDateTo = '';
                if ($dateFrom) {
                    $condDateFrom = ['>=', 'payment.pay_date', date('Y-m-d', strtotime($dateFrom))];
                    $queryTotal->andWhere(['>=', 'payment.pay_date', date('Y-m-d', strtotime($dateFrom))]);
                }
                if ($dateTo) {
                    $condDateTo = ['<=', 'payment.pay_date', date('Y-m-d', strtotime($dateTo))];
                    $queryTotal->andWhere(['<=', 'payment.pay_date', date('Y-m-d', strtotime($dateTo))]);
                }
                if ($condDateFrom || $condDateTo) {
                    if ($condDateFrom && $condDateTo) {
                        $cond[] = ['and', $condDateFrom, $condDateTo];
                    } elseif ($condDateFrom) {
                        $cond[] = $condDateFrom;
                    } elseif ($condDateTo) {
                        $cond[] = $condDateTo;
                    }
                }

                $queryTotal->andWhere($cond);
                $queryTotal->distinct();

//                $queryTotal->select([
//                    new \yii\db\Expression('SUM(IF( flat.status IN ("'.static::STATUS_NOT_SELL.'","'.static::STATUS_SOLD.'","'.static::STATUS_UNAVAILABLE.'"), flat.square, 0 )) as `square_sold`'),
//                    new \yii\db\Expression('SUM(IF( flat.status IN ("'.static::STATUS_NOT_SELL.'","'.static::STATUS_SOLD.'","'.static::STATUS_UNAVAILABLE.'"), 1, 0 )) as `count_sold`'),
//                    new \yii\db\Expression('SUM(IF( flat.status IN ("'.static::STATUS_AVAILABLE.'","'.static::STATUS_BOOKED.'","'.static::STATUS_RESERVED.'","'.static::STATUS_READY_BUY.'"), flat.square, 0 )) as `square_available`'),
//                    new \yii\db\Expression('SUM(IF( flat.status IN ("'.static::STATUS_AVAILABLE.'","'.static::STATUS_BOOKED.'","'.static::STATUS_RESERVED.'","'.static::STATUS_READY_BUY.'"), 1, 0 )) as `count_available`'),
//                    new \yii\db\Expression('SUM(flat.square) as `square_total`'),
//                    new \yii\db\Expression('SUM(flat.square * flat.price_sell_m) as `price_total`'),
//                ]);
//                
//                if ($sectionModel->name == static::TYPE_PARKING) {
//                    print($queryTotal->createCommand()->rawSql . '<br/><br/>');
//                }
//                
//                $dataFlat = $queryTotal->asArray()->one();

                $dataFlat = [
                    'square_sold' => 0,
                    'count_sold' => 0,
                    'count_sold_period' => 0,
                    'square_available' => 0,
                    'count_available' => 0,
                    'square_total' => 0,
                    'price_total' => 0,
                    'price_paid' => 0,
                    'price_left' => 0,
                ];
                foreach ($queryTotal->each() as $flat) {
//                    $dataFlat['price_total'] += $flat->getPriceSell();
//                    $dataFlat['price_paid'] += $flat->getPaymentFact();
//                    $dataFlat['price_left'] += $flat->getPaymentLeft();
                    if (in_array($flat->status, [static::STATUS_SOLD])) {
                        $dataFlat['count_sold_period'] += 1;
                    }
                }
//                foreach ($queryTotalAlltime->each() as $flat) {
//                    if (in_array($flat->status, [static::STATUS_NOT_SELL, static::STATUS_SOLD, static::STATUS_UNAVAILABLE])) {
//                        $dataFlat['square_sold'] += $flat->square;
//                        $dataFlat['count_sold'] += 1;
//                    } elseif (in_array($flat->status, [static::STATUS_AVAILABLE, static::STATUS_BOOKED, static::STATUS_RESERVED, static::STATUS_READY_BUY])) {
//                        $dataFlat['square_available'] += $flat->square;
//                        $dataFlat['count_available'] += 1;
//                    }
//                    $dataFlat['square_total'] += $flat->square;
//                }

                /*                 * *** */
                $queryFlatsTotal = Flat::find();
                if ($sectionModel->isNewRecord) {
                    $queryFlatsTotal->where(['in', 'flat.house_id', $sectionIds]);
                } else {
                    $queryFlatsTotal->where(['flat.house_id' => $sectionModel->id]);
                }

                if ($sectionModel->name == static::TYPE_PARKING) {
                    $queryFlatsTotal->andWhere(['flat.unit_type' => static::TYPE_PARKING]);
                } elseif ($sectionModel->name == static::TYPE_CAR_PLACE) {
                    $queryFlatsTotal->andWhere(['flat.unit_type' => static::TYPE_CAR_PLACE]);
                } else {
                    $queryFlatsTotal->andWhere(['not in', 'flat.unit_type', [static::TYPE_PARKING, static::TYPE_CAR_PLACE]]);
                }

                foreach ($queryFlatsTotal->each() as $flat) {
                    if (in_array($flat->status, [static::STATUS_SOLD])) {
                        $dataFlat['square_sold'] += $flat->square;
                        $dataFlat['count_sold'] += 1;
                        
                        $dataFlat['price_total'] += $flat->getPaymentPlan();
                        $dataFlat['price_paid'] += $flat->getPaymentFactComplated();
                        $dataFlat['price_left'] += $flat->getPaymentLeftComplated();
                        
                    } elseif (in_array($flat->status, [static::STATUS_AVAILABLE, static::STATUS_BOOKED])) {
                        $dataFlat['square_available'] += $flat->square;
                        $dataFlat['count_available'] += 1;
                    }

                    $dataFlat['square_total'] += $flat->square;
                }
                /*                 * *** */

                // payment
                $queryTotal = Payment::find()->joinWith('flat');
                if ($sectionModel->isNewRecord) {
                    $queryTotal->where(['in', 'flat.house_id', $sectionIds]);
                } else {
                    $queryTotal->where(['flat.house_id' => $sectionModel->id]);
                }

                if ($sectionModel->name == static::TYPE_PARKING) {
                    $queryTotal->andWhere(['flat.unit_type' => static::TYPE_PARKING]);
                } elseif ($sectionModel->name == static::TYPE_CAR_PLACE) {
                    $queryTotal->andWhere(['flat.unit_type' => static::TYPE_CAR_PLACE]);
                } else {
                    $queryTotal->andWhere(['not in', 'flat.unit_type', [static::TYPE_PARKING, static::TYPE_CAR_PLACE]]);
                }

                $queryTotal->andWhere($cond);
                $queryTotal->distinct();

//                $queryTotal->select([
//                    new \yii\db\Expression('SUM(payment.price_plan) as `price_total`'),
//                    new \yii\db\Expression('SUM(payment.price_fact) as `price_paid`'),
//                    new \yii\db\Expression('SUM( IF(payment.is_price_left, payment.price_plan, 0) ) as `price_left`'),
//                ]);
//                $dataPayment = $queryTotal->asArray()->one();
//                $dataPayment = [
//                    'price_total' => 0,
//                    'price_paid' => 0,
//                    'price_left' => 0,
//                ];
//                foreach ($queryTotal->each() as $payment) {
//                    $dataPayment['price_total'] += $payment->price_plan;
//                    $dataPayment['price_paid'] += $payment->price_fact;
//                    $dataPayment['price_left'] += $payment->is_price_left ? $payment->price_plan : 0;
//                }

                $rowShift += 1;
                $sheet->setCellValue('A' . ($row + $rowShift), round($dataFlat['square_total'], 2));
                $sheet->setCellValue('B' . ($row + $rowShift), round($dataFlat['square_sold'], 2));
                $sheet->setCellValue('C' . ($row + $rowShift), round($dataFlat['count_sold'], 2));
                $sheet->setCellValue('D' . ($row + $rowShift), round($dataFlat['count_sold_period'], 2));
                $sheet->setCellValue('E' . ($row + $rowShift), round($dataFlat['square_available'], 2));
                $sheet->setCellValue('F' . ($row + $rowShift), round($dataFlat['count_available'], 2));
                $sheet->setCellValue('G' . ($row + $rowShift), round($dataFlat['price_total'], 0));
                $sheet->setCellValue('H' . ($row + $rowShift), round($dataFlat['price_paid'], 0));
                $sheet->setCellValue('I' . ($row + $rowShift), round($dataFlat['price_left'], 0));

                $sheet->getStyle('A' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I' . ($row + $rowShift))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }

            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('D')->setWidth(15);
            // $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);

            // 
            $row += $rowShift;
        }

        $objWriter = new \PHPExcel_Writer_Excel5($xls);
        $objWriter->save($path);

        return $pathWeb;
    }

    protected static function getXlsColFromNumber($num) {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return static::getXlsColFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }

}
