<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "agency".
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $description
 * @property int $status
 *
 * @property Agreement[] $agreements
 * @property Client[] $clients
 * @property Rieltor[] $rieltors
 * @property Rieltor $rieltorDirector
 * @property Invoice[] $invoices
 */
class Agency extends \common\models\ZModel
{
    const STATUS_ACTIVE = 10;
    const STATUS_DISABLED = 0;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['name', 'phone', 'email'], 'string', 'max' => 255],
            ['description', 'string'],
            [['name', 'status'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Название'),
            'phone' => Yii::t('app', 'Телефон'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Статус'),
            'description' => Yii::t('app', 'Описание'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreements()
    {
        return $this->hasMany(Agreement::className(), ['agency_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClients()
    {
        return $this->hasMany(Client::className(), ['agency_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRieltors()
    {
        return $this->hasMany(Rieltor::className(), ['agency_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRieltorDirector()
    {
        return $this->hasOne(Rieltor::className(), ['agency_id' => 'id'])
            ->onCondition(['is_director' => 1]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['agency_id' => 'id']);
    }
    
    /**
     * Get array of model names for filter options
     * @return array
     */
    public function getOptions()
    {
        return \yii\helpers\ArrayHelper::map(static::find()->all(), 'id', 'name');
    }
    
    /**
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            static::STATUS_ACTIVE => Yii::t('model', 'Активирован'),
            static::STATUS_DISABLED => Yii::t('model', 'Не активирован'),
        ];
    }

    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabel($status = null)
    {
        $status = $status == null ? $this->status : $status;
        $options = static::getStatusOptions();
        return isset($options[$status]) ? $options[$status] : null;
    }
    
    /**
     * Get status value by input label
     * @param type $label
     * @return type
     */
    public static function getStatusByLabel($label)
    {
        return array_search($label, static::getStatusOptions());
    }
    
    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabelHtml($status = null)
    {
        $status = $status == null ? $this->status : $status;
        $statusLabel = $this->getStatusLabel($status);
        $itemClass = 'text-default';
        if ($status == static::STATUS_ACTIVE) {
            $itemClass = 'text-green';
        } elseif ($status == static::STATUS_DISABLED) {
            $itemClass = 'text-red';
        }
        return '<span class="'.$itemClass.'">'.$statusLabel.'</span>';
    }
    
    /**
     * Create commission invoice model for flat. If commission exists it can be updated
     * @param Flat $flat
     * @param Cashbox $cashbox if not set usd will be used
     * @param boolean $updateIfExists
     * @return Invoice|null
     */
    public function createCommissionForFlat($flat, $cashbox = null, $updateIfExists = false)
    {
        if (!$cashbox) {
            $cashbox = Cashbox::getCashboxByCurrency(Cashbox::CURRENCY_USD);
        }
        $query = Invoice::find()->where([
            'agency_id' => $this->id, 
            'flat_id' => $flat->id,
            'type' => Invoice::TYPE_OUTCOME,
            'article_id' => Article::getIdCommissionAgency()
        ]);
        $commissionAgencyExists = $query->exists();
        if (!$commissionAgencyExists) {
            $invoice = new Invoice();
            $invoice->agency_id = $this->id;
            $invoice->flat_id = $flat->id;
            $invoice->generateUid();
            $invoice->uid_date = date('Y-m-d', $invoice->uid_date ? strtotime($invoice->uid_date) : time());
            $invoice->status = Invoice::STATUS_COMPLETE;
            $invoice->type = Invoice::TYPE_OUTCOME;
            $invoice->article_id = Article::getIdCommissionAgency();
            $invoice->cashbox_id = $cashbox->id;
            $invoice->rate = $cashbox->rate;
            $invoice->price = $flat->getCommissionAgencyPrice();
            $invoice->description = 'Выплата комиссионных по квартире';
            if ($invoice->save()) {
                //// LOG
                $invoice->saveLog(__METHOD__ . ' ');
                //// END LOG
                return $invoice;
            }
        } elseif ($updateIfExists) {
            $invoice = $query->one();
            $invoice->uid_date = date('Y-m-d', $invoice->uid_date ? strtotime($invoice->uid_date) : time());
            $invoice->cashbox_id = $cashbox->id;
            $invoice->rate = $cashbox->rate;
            $invoice->price = $flat->getCommissionAgencyPrice();
            if ($invoice->save()) {
                return $invoice;
            }
        }
        return null;
    }
}
