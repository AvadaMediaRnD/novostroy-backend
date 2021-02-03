<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\helpers\PriceHelper;

/**
 * This is the model class for table "agreement_flat".
 *
 * @property int $id
 * @property int $number
 * @property int $number_index
 * @property int $n_rooms
 * @property int $floor
 * @property double $square
 * @property string $address
 * @property string $price
 * @property string $rate
 * @property int $created_at
 * @property int $updated_at
 * @property int $agreement_id
 *
 * @property Agreement $agreement
 */
class AgreementFlat extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agreement_flat';
    }
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'n_rooms', 'floor', 'created_at', 'updated_at', 'agreement_id'], 'integer'],
            [['square', 'price', 'rate'], 'number'],
            [['agreement_id'], 'required'],
            [['address', 'number_index'], 'string', 'max' => 255],
            [['agreement_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agreement::className(), 'targetAttribute' => ['agreement_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'number' => Yii::t('app', 'Number'),
            'number_index' => Yii::t('app', 'Number Index'),
            'n_rooms' => Yii::t('app', 'N Rooms'),
            'floor' => Yii::t('app', 'Floor'),
            'square' => Yii::t('app', 'Square'),
            'rate' => Yii::t('app', 'Rate'),
            'address' => Yii::t('app', 'Address'),
            'price' => Yii::t('app', 'Price'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'agreement_id' => Yii::t('app', 'Agreement ID'),
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        $cashboxUsd = Cashbox::getCashboxByCurrency(Cashbox::CURRENCY_USD);
        if ($this->rate == 0 && $cashboxUsd) {
            $this->rate = $cashboxUsd->rate;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreement()
    {
        return $this->hasOne(Agreement::className(), ['id' => 'agreement_id']);
    }
    
    /**
     * @return string
     */
    public function getPriceForCashbox($currencyCode = null)
    {
        if (!$currencyCode) {
            $currencyCode = Cashbox::CURRENCY_USD;
        }
        if ($currencyCode == Cashbox::CURRENCY_UAH) {
            return $this->price * $this->rate;
        }
        return $this->price;
    }
    
    /**
     * @return string
     */
    public function getPriceTextForCashbox($currencyCode = null)
    {
        return PriceHelper::text(round($this->getPriceForCashbox($currencyCode), 2), $currencyCode);
    }
    
    /**
     * @return string
     */
    public function getRateText()
    {
        return PriceHelper::text(round($this->rate, 2));
    }
}
