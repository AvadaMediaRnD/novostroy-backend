<?php

namespace common\models;

use Yii;
use common\helpers\PriceHelper;

/**
 * This is the model class for table "view_total_flat".
 *
 * @property int $id
 * @property int $number
 * @property int $n_rooms
 * @property int $floor
 * @property double $square
 * @property string $price_m
 * @property string $price_sell_m
 * @property string $price_discount_m
 * @property double $commission_agency
 * @property double $commission_manager
 * @property string $description
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $house_id
 * @property int $client_id
 * @property string $name
 * @property string $section
 * @property double $price
 * @property double $price_sell
 * @property double $price_discount
 * @property string $price_plan
 * @property string $price_fact
 * @property string $price_left
 * @property string $price_debt
 * @property int $sell_status
 * @property string $uid
 * @property string $client_firstname
 * @property string $client_middlename
 * @property string $client_lastname
 * @property string $phone
 * @property string $email
 * @property string $user_firstname
 * @property string $user_middlename
 * @property string $user_lastname
 * @property string $agency_id
 * @property string $agency_name
 */
class ViewTotalFlat extends \common\models\ZModel
{   
    const STATUS_AVAILABLE = 10;
    const STATUS_BOOKED = 8;
    const STATUS_RESERVED = 7;
    const STATUS_READY_BUY = 5;
    const STATUS_NOT_SELL = 2;
    const STATUS_SOLD = 1;
    const STATUS_UNAVAILABLE = 0;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'view_total_flat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'number', 'n_rooms', 'floor', 'status', 'sell_status', 'created_at', 'updated_at', 'house_id', 'client_id', 'agency_id'], 'integer'],
            [['square', 'price_m', 'price_sell_m', 'price_discount_m', 'commission_agency', 'commission_manager', 'price', 'price_sell', 'price_discount', 'price_plan', 'price_fact', 'price_left', 'price_debt'], 'number'],
            [['description'], 'string'],
            [['created_at', 'updated_at', 'house_id'], 'required'],
            [['name', 'section', 'uid', 'client_firstname', 'client_middlename', 'client_lastname', 'phone', 'email', 'user_firstname', 'user_middlename', 'user_lastname', 'agency_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'number' => Yii::t('app', '№'),
            'n_rooms' => Yii::t('app', 'N Rooms'),
            'floor' => Yii::t('app', 'Floor'),
            'square' => Yii::t('app', 'Площадь'),
            'price_m' => Yii::t('app', 'Цена за кв.м'),
            'price_sell_m' => Yii::t('app', 'Продажи за кв.м'),
            'price_discount_m' => Yii::t('app', 'Price Discount M'),
            'commission_agency' => Yii::t('app', 'Commission Agency'),
            'commission_manager' => Yii::t('app', 'Commission Manager'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'house_id' => Yii::t('app', 'Дом (Секция)'),
            'client_id' => Yii::t('app', 'Client ID'),
            'name' => Yii::t('app', 'Name'),
            'section' => Yii::t('app', 'Section'),
            'price' => Yii::t('app', 'Цена'),
            'price_sell' => Yii::t('app', 'Price Sell'),
            'price_discount' => Yii::t('app', 'Скидка'),
            'price_plan' => Yii::t('app', 'План продаж'),
            'price_fact' => Yii::t('app', 'Факт'),
            'price_left' => Yii::t('app', 'Остаток'),
            'price_debt' => Yii::t('app', 'Долг'),
            'sell_status' => Yii::t('app', 'Статус'),
            'uid' => Yii::t('app', '№ договора'),
            'client_firstname' => Yii::t('app', 'Client Firstname'),
            'client_middlename' => Yii::t('app', 'Client Middlename'),
            'client_lastname' => Yii::t('app', 'Client Lastname'),
            'phone' => Yii::t('app', 'Тел. клиента'),
            'email' => Yii::t('app', 'Email клиента'),
            'user_firstname' => Yii::t('app', 'User Firstname'),
            'user_middlename' => Yii::t('app', 'User Middlename'),
            'user_lastname' => Yii::t('app', 'User Lastname'),
            'agency_name' => Yii::t('app', 'Agency Name'),
            'agency_id' => Yii::t('app', 'Агенство'),
        ];
    }
    
    /**
     * Get string name + section
     * @return string
     */
    public function getNameSection()
    {
        if (!$this->section) {
            return $this->name;
        }
        return $this->name . ' ' . $this->section;
    }
    
    /**
     * @return array
     */
    public static function getSellStatusOptions()
    {
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
    public function getSellStatusLabel($sellStatus = null)
    {
        $sellStatus = $sellStatus == null ? $this->sell_status : $sellStatus;
        $options = static::getSellStatusOptions();
        return isset($options[$sellStatus]) ? $options[$sellStatus] : null;
    }
    
    /**
     * @return string
     */
    public function getClientFullname()
    {
        $nameParts = [];
        if ($this->client_lastname) {
            $nameParts[] = $this->client_lastname;
        }
        if ($this->client_middlename) {
            $nameParts[] = $this->client_middlename;
        }
        if ($this->client_firstname) {
            $nameParts[] = $this->client_firstname;
        }
        return $nameParts ? implode(' ', $nameParts) : null;
    }
    
    /**
     * @return string
     */
    public function getUserFullname()
    {
        $nameParts = [];
        if ($this->user_lastname) {
            $nameParts[] = $this->user_lastname;
        }
        if ($this->user_middlename) {
            $nameParts[] = $this->user_middlename;
        }
        if ($this->user_firstname) {
            $nameParts[] = $this->user_firstname;
        }
        return $nameParts ? implode(' ', $nameParts) : null;
    }
    
    /**
     * Get formatted value to display
     * @return string
     */
    public function getPriceFormatted()
    {
        return PriceHelper::format($this->price, false, false);
    }
    
    /**
     * Get formatted value to display
     * @return string
     */
    public function getPriceMFormatted()
    {
        return PriceHelper::format($this->price_m, false, false);
    }
    
    /**
     * Get formatted value to display
     * @return string
     */
    public function getPricePlanFormatted()
    {
        return PriceHelper::format($this->price_plan, false, false);
    }
    
    /**
     * Get formatted value to display
     * @return string
     */
    public function getPriceSellMFormatted()
    {
        return PriceHelper::format($this->price_sell_m, false, false);
    }
    
    /**
     * Get formatted value to display
     * @return string
     */
    public function getPriceFactFormatted()
    {
        return PriceHelper::format($this->price_fact, false, false);
    }
    
    /**
     * Get formatted value to display
     * @return string
     */
    public function getPriceLeftFormatted()
    {
        return PriceHelper::format($this->price_left, false, false);
    }
    
    /**
     * Get formatted value to display
     * @return string
     */
    public function getPriceDebtFormatted()
    {
        return PriceHelper::format($this->price_debt, false, false);
    }
    
    /**
     * Get formatted value to display
     * @return string
     */
    public function getPriceDiscountFormatted()
    {
        return PriceHelper::format($this->price_discount, false, false);
    }
}
