<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cashbox".
 *
 * @property int $id
 * @property string $name
 * @property string $currency
 * @property string $rate
 * @property int $is_default
 *
 * @property Invoice[] $invoices
 */
class Cashbox extends \common\models\ZModel {

    const CURRENCY_USD = 'USD';
    const CURRENCY_EUR = 'EUR';
    const CURRENCY_UAH = 'UAH';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'cashbox';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['rate'], 'number'],
            [['is_default'], 'integer'],
            [['name', 'currency'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'currency' => Yii::t('app', 'Currency'),
            'rate' => Yii::t('app', 'Rate'),
            'is_default' => Yii::t('app', 'Is Default'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices() {
        return $this->hasMany(Invoice::className(), ['cashbox_id' => 'id']);
    }

    /**
     * Get options array for selectors
     * @return array
     */
    public static function getOptions() {
        return \yii\helpers\ArrayHelper::map(static::find()->all(), 'id', 'name');
    }

    /**
     * Get cashbox object by name of currency
     * @param string $currency
     * @return static
     */
    public static function getCashboxByCurrency($currency = '') {
        return static::find()->where(['currency' => $currency])->one();
    }

    public static function getCashboxRateByCurrency($currency = '') {
        $model = static::find()->where(['currency' => $currency])->one();
        return $model->rate;
    }

    /**
     * @return boolean
     */
    public static function syncRatesOnPrivatbankApi() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, 'https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5');
        $data = curl_exec($ch);
        $rates = json_decode($data, true);
        curl_close($ch);

        foreach ($rates as $currency) {

            if (($model = self::find()->where(['currency' => $currency['ccy']])->one()) !== null) :
                $model->rate = $currency['sale'];
                $model->save();
            endif;
        }

        return true;
    }

}
