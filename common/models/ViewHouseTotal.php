<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "view_house_total".
 *
 * @property int $id
 * @property string $name
 * @property string $section
 * @property int $flats_total
 * @property string $flats_available
 * @property string $flats_sold
 * @property double $square_total
 * @property double $square_available
 * @property double $square_sold
 * @property double $price_total
 * @property double $price_available
 * @property double $price_sold
 */
class ViewHouseTotal extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'view_house_total';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'flats_total'], 'integer'],
            [['name', 'section'], 'string'],
            [['flats_available', 'flats_sold', 'square_total', 'square_available', 'square_sold', 'price_total', 'price_available', 'price_sold'], 'number'],
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
            'section' => Yii::t('app', 'Section'),
            'flats_total' => Yii::t('app', 'Flats Total'),
            'flats_available' => Yii::t('app', 'Flats Available'),
            'flats_sold' => Yii::t('app', 'Flats Sold'),
            'square_total' => Yii::t('app', 'Square Total'),
            'square_available' => Yii::t('app', 'Square Available'),
            'square_sold' => Yii::t('app', 'Square Sold'),
            'price_total' => Yii::t('app', 'Price Total'),
            'price_available' => Yii::t('app', 'Price Available'),
            'price_sold' => Yii::t('app', 'Price Sold'),
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
}
