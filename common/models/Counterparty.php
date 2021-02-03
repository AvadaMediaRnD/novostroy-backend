<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "counterparty".
 *
 * @property int $id
 * @property string $table_name
 * @property int $table_id
 *
 * @property Invoice[] $invoices
 */
class Counterparty extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'counterparty';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['table_id'], 'integer'],
            [['table_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'table_name' => Yii::t('app', 'Table Name'),
            'table_id' => Yii::t('app', 'Table ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['counterparty_id' => 'id']);
    }
}
