<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property string $type
 * @property string $name
 *
 * @property Invoice[] $invoices
 */
class Article extends \common\models\ZModel
{
    const TYPE_INCOME = 'income';
    const TYPE_OUTCOME = 'outcome';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['article_id' => 'id']);
    }
    
    /**
     * Get options array for selectors
     * @return array
     */
    public static function getOptions()
    {
        return \yii\helpers\ArrayHelper::map(static::find()->all(), 'id', 'name');
    }
    
    /**
     * @return array
     */
    public static function getTypeOptions()
    {
        return [
            static::TYPE_INCOME => Yii::t('model', 'Приход'),
            static::TYPE_OUTCOME => Yii::t('model', 'Расход'),
        ];
    }
    
    /**
     * 
     * @return int
     */
    public static function getIdPaymentFlat()
    {
        return 1;
    }
    
    /**
     * 
     * @return int
     */
    public static function getIdCommissionManager()
    {
        return 4;
    }
    
    /**
     * 
     * @return int
     */
    public static function getIdCommissionAgency()
    {
        return 3;
    }
    
    /**
     * 
     * @return int
     */
    public static function getIdInitialPayment()
    {
        return 8;
    }
    
    /**
     * 
     * @return int
     */
    public static function getIdPaydeFlat()
    {
        return 9;
    }
    
    /**
     * 
     * @return int
     */
    public static function getIdReserveFlat()
    {
        return 10;
    }
}
