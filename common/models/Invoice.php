<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property string $uid
 * @property string $uid_date
 * @property string $price
 * @property string $rate
 * @property string $type
 * @property string $description
 * @property string $company_name
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $article_id
 * @property int $cashbox_id
 * @property int $flat_id
 * @property int $payment_id
 * @property int $client_id
 * @property int $agency_id
 * @property int $rieltor_id
 * @property int $user_id
 *
 * @property Article $article
 * @property Cashbox $cashbox
 * @property Flat $flat
 * @property Payment $payment
 * @property Client $client
 * @property Agency $agency
 * @property Rieltor $rieltor
 * @property User $user
 */
class Invoice extends \common\models\ZModel {

    const STATUS_WAITING = 0;
    const STATUS_COMPLETE = 1;
    const TYPE_INCOME = 'income';
    const TYPE_OUTCOME = 'outcome';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'invoice';
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
            [['uid_date'], 'safe'],
            [['price', 'rate'], 'number'],
            [['status', 'created_at', 'updated_at', 'article_id', 'cashbox_id', 'flat_id', 'payment_id', 'client_id', 'rieltor_id', 'user_id', 'agency_id'], 'integer'],
            [['uid', 'description', 'type', 'company_name'], 'string', 'max' => 255],
            ['type', 'in', 'range' => [static::TYPE_INCOME, static::TYPE_OUTCOME]],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::className(), 'targetAttribute' => ['article_id' => 'id']],
            [['cashbox_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cashbox::className(), 'targetAttribute' => ['cashbox_id' => 'id']],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['payment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payment::className(), 'targetAttribute' => ['payment_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['rieltor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rieltor::className(), 'targetAttribute' => ['rieltor_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'uid' => Yii::t('app', '#'),
            'uid_date' => Yii::t('app', 'Дата'),
            'price' => Yii::t('app', 'Сумма'),
            'rate' => Yii::t('app', 'Курс'),
            'type' => Yii::t('app', 'Приход/расход'),
            'description' => Yii::t('app', 'Примечание'),
            'company_name' => Yii::t('app', 'Название компании'),
            'status' => Yii::t('app', 'Статус'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'article_id' => Yii::t('app', 'Статья'),
            'cashbox_id' => Yii::t('app', 'Касса'),
            'flat_id' => Yii::t('app', 'Помещение'),
            'payment_id' => Yii::t('app', '№ платежа'),
            'client_id' => Yii::t('app', 'Покупатель'),
            'agency_id' => Yii::t('app', 'Агентство'),
            'rieltor_id' => Yii::t('app', 'Риелтор'),
            'user_id' => Yii::t('app', 'Менеджер'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes) {
        if ($insert || isset($changedAttributes['payment_id']) || isset($changedAttributes['price'])) {
            $paymentModel = $this->payment;
            if ($paymentModel) {
                if ($this->status === '0' || $this->status == 0 || $this->status === false) {
                    $paymentModel->price_fact = 0;
                    $paymentModel->price_saldo = 0;
                } else {
                    $paymentModel->price_fact = $this->price;
                    $paymentModel->price_saldo = $paymentModel->price_fact - $paymentModel->price_plan;
                }
                $paymentModel->save();
            }
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    //// LOG
    public function saveLog($str) {
        file_put_contents(
                Yii::getAlias('@frontend/web/upload/invoice_log.txt'),
                "\r\nID:{$this->id} date:" . date('Ymd H:i:s') . ' - ' . $str,
                FILE_APPEND
        );
    }

    //// END LOG

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle() {
        return $this->hasOne(Article::className(), ['id' => 'article_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCashbox() {
        return $this->hasOne(Cashbox::className(), ['id' => 'cashbox_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlat() {
        return $this->hasOne(Flat::className(), ['id' => 'flat_id']);
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
    public function getRieltor() {
        return $this->hasOne(Rieltor::className(), ['id' => 'rieltor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayment() {
        return $this->hasOne(Payment::className(), ['id' => 'payment_id']);
    }

    /**
     * Uid date in print format
     * @return string
     */
    public function getUidDate() {
        return Yii::$app->formatter->asDate($this->uid_date);
    }

    /**
     * @return array
     */
    public static function getStatusOptions() {
        return [
            static::STATUS_COMPLETE => Yii::t('model', 'Проведен'),
            static::STATUS_WAITING => Yii::t('model', 'Запланирован'),
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
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabelHtml($status = null) {
        $status = $status == null ? $this->status : $status;
        $statusLabel = $this->getStatusLabel($status);
        $itemClass = 'text-default';
        if ($status == static::STATUS_COMPLETE) {
            $itemClass = 'text-green';
        } elseif ($status == static::STATUS_WAITING) {
            $itemClass = 'text-red';
        }
        return '<span class="text ' . $itemClass . '">' . $statusLabel . '</span>';
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
    public static function getTypeOptions() {
        return [
            static::TYPE_INCOME => Yii::t('model', 'Приход'),
            static::TYPE_OUTCOME => Yii::t('model', 'Расход'),
        ];
    }

    /**
     * @param null $type
     * @return mixed|null
     */
    public function getTypeLabel($type = null) {
        $type = $type == null ? $this->type : $type;
        $options = static::getTypeOptions();
        return isset($options[$type]) ? $options[$type] : null;
    }

    /**
     * @param null $type
     * @return mixed|null
     */
    public function getTypeLabelHtml($type = null) {
        $type = $type == null ? $this->type : $type;
        $typeLabel = $this->getTypeLabel($type);
        $itemClass = 'text-default';
        if ($type == static::TYPE_INCOME) {
            $itemClass = 'text-green';
        } elseif ($type == static::TYPE_OUTCOME) {
            $itemClass = 'text-red';
        }
        return '<span class="text ' . $itemClass . '">' . $typeLabel . '</span>';
    }

    /**
     * Get counterparty for grid filter
     * @return array
     */
    public static function getCounterpartyOptions() {
        $clientsOptions = ArrayHelper::map(
                        Client::find()->orderBy(['lastname' => SORT_ASC, 'firstname' => SORT_ASC])->all(),
                        'fullname',
                        function ($model) {
                    return 'Покупатель - ' . $model->fullname;
                }
        );
        $agencyOptions = ArrayHelper::map(
                        Agency::find()->orderBy(['name' => SORT_ASC])->all(),
                        'name',
                        function ($model) {
                    return 'Агентство - ' . $model->name;
                }
        );
        $rieltorOptions = ArrayHelper::map(
                        Rieltor::find()->orderBy(['lastname' => SORT_ASC, 'firstname' => SORT_ASC])->all(),
                        'fullname',
                        function ($model) {
                    return 'Риелтор (' . $model->agency->name . ') - ' . $model->fullname;
                }
        );
        $userOptions = ArrayHelper::map(
                        User::find()
                                ->where(['in', 'role', [User::ROLE_ADMIN, User::ROLE_FIN_DIRECTOR, User::ROLE_SALES_MANAGER]])
                                ->orderBy(['lastname' => SORT_ASC, 'firstname' => SORT_ASC])->all(),
                        'fullname',
                        function ($model) {
                    return $model->getRoleLabel() . ' - ' . $model->fullname;
                }
        );

        return array_merge($userOptions, $agencyOptions, $rieltorOptions, $clientsOptions);
    }

    /**
     * Get label value for grid
     * @return string
     */
    public function getCounterpartyLabel() {
        if ($this->client) {
            return 'Покупатель - ' . $this->client->fullname;
        }
        if ($this->rieltor) {
            return 'Риелтор (' . $this->rieltor->agency->name . ') - ' . $this->rieltor->fullname;
        }
        if ($this->agency) {
            return 'Агентство - ' . $this->agency->name;
        }
        if ($this->user) {
            return $this->user->getRoleLabel() . ' - ' . $this->user->fullname;
        }
        return null;
    }

    /**
     * Get total sum of income for cashbox or all
     * @param integer $cashboxId
     * @param string $dateFrom Y-m-d
     * @param string $dateTo Y-m-d
     * @return float
     */
    public static function getTotalIn($cashboxId = null, $dateFrom = null, $dateTo = null) {
        $query = static::find()->joinWith(['cashbox', 'flat'])
                ->where(['type' => static::TYPE_INCOME, 'invoice.status' => static::STATUS_COMPLETE])
                ->andFilterWhere(['cashbox_id' => $cashboxId]);

        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->andWhere(['or', ['in', 'flat.house_id', $houseIds], ['is', 'flat.house_id', null]]);

        if ($dateFrom) {
            $query->andWhere(['>=', 'uid_date', $dateFrom]);
        }
        if ($dateTo) {
            $query->andWhere(['<=', 'uid_date', $dateTo]);
        }

        // query params
        $searchQuery = Yii::$app->request->get('InvoiceSearch');
        if ($searchQuery['article_id']) {
            $query->andWhere(['article_id' => $searchQuery['article_id']]);
        }

        if ($cashboxId) {
            return $query->sum('price');
        }
        return $query->sum(new \yii\db\Expression('`invoice`.`price` * `invoice`.`rate`'));
    }
    
    /**
     * Get total sum of income for cashbox or all
     * @param integer $cashboxId
     * @param string $dateFrom Y-m-d
     * @param string $dateTo Y-m-d
     * @return float
     */
    public static function getTotalInDiagram($cashboxId = null, $dateFrom = null, $dateTo = null) {
        $query = static::find()->joinWith(['cashbox', 'flat'])
                ->where(['type' => static::TYPE_INCOME, 'invoice.status' => static::STATUS_COMPLETE])
                ->andFilterWhere(['cashbox_id' => $cashboxId]);

        $houseIds = ArrayHelper::getColumn(House::find()->where(['like','name','Avinion'])->asArray()->all(),'id');
        $query->andWhere(['in', 'flat.house_id', $houseIds]);

        if ($dateFrom) {
            $query->andWhere(['>=', 'uid_date', $dateFrom]);
        }
        if ($dateTo) {
            $query->andWhere(['<=', 'uid_date', $dateTo]);
        }

        if ($cashboxId) {
            return $query->sum('price');
        }
        return $query->sum(new \yii\db\Expression('`invoice`.`price` * `invoice`.`rate`'));
    }

    /**
     * Get total sum of outcome for cashbox or all
     * @param integer $cashboxId
     * @param string $dateFrom Y-m-d
     * @param string $dateTo Y-m-d
     * @return float
     */
    public static function getTotalOut($cashboxId = null, $dateFrom = null, $dateTo = null) {
        $query = static::find()->joinWith(['cashbox', 'flat'])
                ->where(['type' => static::TYPE_OUTCOME, 'invoice.status' => static::STATUS_COMPLETE])
                ->andFilterWhere(['cashbox_id' => $cashboxId]);

        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->andWhere(['or', ['in', 'flat.house_id', $houseIds], ['is', 'flat.house_id', null]]);

        if ($dateFrom) {
            $query->andWhere(['>=', 'uid_date', $dateFrom]);
        }
        if ($dateTo) {
            $query->andWhere(['<=', 'uid_date', $dateTo]);
        }

        // query params
        $searchQuery = Yii::$app->request->get('InvoiceSearch');
        if ($searchQuery['article_id']) {
            $query->andWhere(['article_id' => $searchQuery['article_id']]);
        }

        if ($cashboxId) {
            return $query->sum('price');
        }
        return $query->sum(new \yii\db\Expression('`invoice`.`price` * `invoice`.`rate`'));
    }
    
    /**
     * Get total sum of income for cashbox or all
     * @param integer $cashboxId
     * @param string $dateFrom Y-m-d
     * @param string $dateTo Y-m-d
     * @return float
     */
    public static function getTotalOutDiagram($cashboxId = null, $dateFrom = null, $dateTo = null) {
        $query = static::find()->joinWith(['cashbox', 'flat'])
                ->where(['type' => static::TYPE_OUTCOME, 'invoice.status' => static::STATUS_COMPLETE])
                ->andFilterWhere(['cashbox_id' => $cashboxId]);

        $houseIds = ArrayHelper::getColumn(House::find()->where(['like','name','Avinion'])->asArray()->all(),'id');
        $query->andWhere(['in', 'flat.house_id', $houseIds]);

        if ($dateFrom) {
            $query->andWhere(['>=', 'uid_date', $dateFrom]);
        }
        if ($dateTo) {
            $query->andWhere(['<=', 'uid_date', $dateTo]);
        }

        if ($cashboxId) {
            return $query->sum('price');
        }
        return $query->sum(new \yii\db\Expression('`invoice`.`price` * `invoice`.`rate`'));
    }

    /**
     * Get balance for cashbox or all
     * @param integer $cashboxId
     * @param string $dateFrom Y-m-d
     * @param string $dateTo Y-m-d
     * @param boolean $allowMinusBalance if we can return < 0
     * @return float
     */
    public static function getTotalBalance($cashboxId = null, $dateFrom = null, $dateTo = null, $allowMinusBalance = false) {
        $balance = static::getTotalIn($cashboxId, $dateFrom, $dateTo) - static::getTotalOut($cashboxId, $dateFrom, $dateTo);
        if (!$allowMinusBalance && $balance < 0) {
            $balance = 0;
        }
        return $balance;
    }

    /**
     * 
     */
    public function generateUid() {
        $this->uid = date('ymd') . sprintf('%05d', static::find()->max('id') + 1);
    }

}
