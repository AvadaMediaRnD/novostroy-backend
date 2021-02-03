<?php

namespace common\models;

use Yii;
use common\helpers\PriceHelper;

/**
 * This is the model class for table "view_total_flat".
 *
 * @property int $id
 * @property int $number
 * @property string $unit_type
 * @property double $price_saldo_total
 */
class ViewNotification extends \common\models\ZModel {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'view_notification';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey() {
        return ["id"];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['number'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['id' => 'id']],
            [['price_saldo_total'], 'number'],
            [['unit_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'number' => Yii::t('app', '№'),
            'unit_type' => Yii::t('app', 'Тип помещения'),
            'price_saldo_total' => Yii::t('app', 'Задолженность'),
        ];
    }

    /**
     * Get formatted value to display
     * @return string
     */
    public function getSaldoFormatted() {
        return PriceHelper::format($this->price_saldo_total, false, false);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlat() {
        return $this->hasOne(Flat::className(), ['id' => 'id']);
    }

}
