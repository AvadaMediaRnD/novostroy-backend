<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "view_cashbox".
 *
 * @property int $id
 * @property string $name
 * @property string $currency
 * @property string $rate
 * @property int $is_default
 * @property string $price_income
 * @property string $price_outcome
 * @property string $price
 */
class ViewCashbox extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'view_cashbox';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'is_default'], 'integer'],
            [['rate', 'price_income', 'price_outcome', 'price'], 'number'],
            [['name', 'currency'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'currency' => Yii::t('app', 'Currency'),
            'rate' => Yii::t('app', 'Rate'),
            'is_default' => Yii::t('app', 'Is Default'),
            'price_income' => Yii::t('app', 'Price Income'),
            'price_outcome' => Yii::t('app', 'Price Outcome'),
            'price' => Yii::t('app', 'Price'),
        ];
    }
}
