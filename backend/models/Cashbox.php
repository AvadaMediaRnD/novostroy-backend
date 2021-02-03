<?php

namespace backend\models;

use Yii;

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
class Cashbox extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cashbox';
    }

    private $updateRate = false;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rate'], 'number'],
            [['is_default'], 'integer'],
            [['name', /*'currency'*/], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => 'Название кассы',
            'currency' => 'Валюта',
            'rate' => 'Курс',
            'is_default' => 'Основная',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->is_default == 1 && $this->rate !== 1) {
                $this->updateAll(['is_default' => 0], ['<>', 'id' , $this->id]);
                $this->updateRate = true;
            }

            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes){
       parent::afterSave($insert, $changedAttributes);

       if ($this->is_default && $this->rate !== 1 && $this->updateRate) {
            $cashboxes = Cashbox::find()->all();
            $this->updateRate = false;

//            foreach ($cashboxes as $cashbox) {
//                Yii::$app->db->createCommand()
//                    ->update(self::tableName(),
//                        ['rate'=> round($cashbox->rate / $this->rate, 4)], //columns and values
//                        ['id'=>$cashbox->id]) //condition, similar to where()
//                    ->execute();
//            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['cashbox_id' => 'id']);
    }
}
