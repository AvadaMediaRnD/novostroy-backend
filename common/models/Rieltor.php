<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "rieltor".
 *
 * @property int $id
 * @property string $firstname
 * @property string $middlename
 * @property string $lastname
 * @property string $phone
 * @property string $email
 * @property int $is_director
 * @property int $agency_id
 *
 * @property Agency $agency
 */
class Rieltor extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rieltor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_director', 'agency_id'], 'integer'],
            [['agency_id'], 'required'],
            [['firstname', 'middlename', 'lastname', 'phone', 'email'], 'string', 'max' => 255],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agency::className(), 'targetAttribute' => ['agency_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'firstname' => Yii::t('app', 'Имя'),
            'middlename' => Yii::t('app', 'Отчество'),
            'lastname' => Yii::t('app', 'Фамилия'),
            'phone' => Yii::t('app', 'Телефон'),
            'email' => Yii::t('app', 'Email'),
            'is_director' => Yii::t('app', 'Директор'),
            'agency_id' => Yii::t('app', 'Агентство'),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes) {
        // verify only 1 is director
        if ($this->is_director) {
            static::updateAll(['is_director' => 0], ['and', ['agency_id' => $this->agency_id], ['!=', 'id', $this->id]]);
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id']);
    }
    
    /**
     * @return string
     */
    public function getFullname()
    {
        $nameParts = [];
        if ($this->lastname) {
            $nameParts[] = $this->lastname;
        }
        if ($this->firstname) {
            $nameParts[] = $this->firstname;
        }
        if ($this->middlename) {
            $nameParts[] = $this->middlename;
        }
        return $nameParts ? implode(' ', $nameParts) : null;
    }
}
