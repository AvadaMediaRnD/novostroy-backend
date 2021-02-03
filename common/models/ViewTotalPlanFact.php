<?php

namespace common\models;

use Yii;
use common\helpers\DateHelper;

/**
 * This is the model class for table "view_total_plan_fact".
 *
 * @property int $house_id
 * @property int $year
 * @property int $month
 * @property string $year_month
 * @property string $price_plan_total
 * @property string $price_fact_total
 * @property string $price_saldo_total
 * @property string $price_debt_total
 */
class ViewTotalPlanFact extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'view_total_plan_fact';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['house_id', 'year', 'month'], 'integer'],
            [['price_plan_total', 'price_fact_total', 'price_saldo_total', 'price_debt_total'], 'number'],
            [['year_month'], 'string', 'max' => 7],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'house_id' => Yii::t('app', 'House ID'),
            'year' => Yii::t('app', 'Year'),
            'month' => Yii::t('app', 'Month'),
            'year_month' => Yii::t('app', 'Year Month'),
            'price_plan_total' => Yii::t('app', 'Price Plan Total'),
            'price_fact_total' => Yii::t('app', 'Price Fact Total'),
            'price_saldo_total' => Yii::t('app', 'Price Saldo Total'),
            'price_debt_total' => Yii::t('app', 'Price Debt Total'),
        ];
    }
    
    /**
     * Get month name by value
     * @return string
     */
    public function getMonthName($short = false)
    {
        return DateHelper::getMonthName($this->month, $short);
    }
}
