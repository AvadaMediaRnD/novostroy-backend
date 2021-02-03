<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property int $pay_number
 * @property int $is_price_left
 * @property string $pay_date
 * @property string $price_plan
 * @property string $price_fact
 * @property string $price_saldo
 * @property int $created_at
 * @property int $updated_at
 * @property int $flat_id
 *
 * @property Invoice[] $invoices
 * @property Flat $flat
 */
class Payment extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
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
            ['pay_number', 'required'],
            [['pay_number', 'is_price_left', 'created_at', 'updated_at', 'flat_id'], 'integer'],
            [['pay_date'], 'safe'],
            [['price_plan', 'price_fact', 'price_saldo'], 'number'],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pay_number' => Yii::t('app', '№ платежа'),
            'pay_date' => Yii::t('app', 'Дата платежа (не позже)'),
            'price_plan' => Yii::t('app', 'План'),
            'price_fact' => Yii::t('app', 'Факт'),
            'price_saldo' => Yii::t('app', 'Сальдо'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'flat_id' => Yii::t('app', 'Квартира'),
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert) {
        if (!$this->price_plan) {
            $this->price_plan = 0;
        }
        if (!$this->price_fact) {
            $this->price_fact = 0;
        }
        if (!$this->price_saldo) {
            $this->price_saldo = 0;
        }
        return parent::beforeSave($insert);
    }
    
    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes) {
        if ($this->flat_id && !$this->is_price_left) {
            $paymentForPriceLeft = $this->flat->getPayments()->andWhere(['is_price_left' => 1])->one();
            if ($paymentForPriceLeft) {
                $flatPriceSell = $this->flat->getPriceSell();
                $pricePlan = $this->flat->getPayments()->andWhere(['is_price_left' => 0])->sum('price_plan');
                $priceFact = $this->flat->getPayments()->andWhere(['is_price_left' => 1])->sum('price_fact');
                $priceLeft = $flatPriceSell - $pricePlan; 
                $priceLeft = $priceLeft > 0 ? $priceLeft : 0;
                $paymentForPriceLeft->price_plan = $priceLeft;
                $paymentForPriceLeft->price_saldo = $priceLeft - $priceFact;
                $paymentForPriceLeft->save();
            }
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete() 
    {
        foreach ($this->invoices as $invoice) {
            $invoice->delete();
        }
        return parent::delete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['payment_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlat()
    {
        return $this->hasOne(Flat::className(), ['id' => 'flat_id']);
    }
    
    /**
     * @return string
     */
    public function getPayDate()
    {
        return Yii::$app->formatter->asDate($this->pay_date);
    }
}
