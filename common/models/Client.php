<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "client".
 *
 * @property int $id
 * @property string $firstname
 * @property string $middlename
 * @property string $lastname
 * @property string $address
 * @property string $birthdate
 * @property string $inn
 * @property string $passport_series
 * @property string $passport_number
 * @property string $passport_from
 * @property string $phone
 * @property string $email
 * @property string $description
 * @property int $created_at
 * @property int $updated_at
 * @property int $agency_id
 * @property int $user_id
 *
 * @property Agreement[] $agreements
 * @property Agency $agency
 * @property User $user
 * @property Flat[] $flats
 */
class Client extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client';
    }
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'agency_id', 'user_id'], 'integer'],
            [['firstname', 'middlename', 'lastname', 'address', 'inn', 'passport_series', 'passport_number', 'passport_from', 'phone', 'email', 'birthdate'], 'string', 'max' => 255],
            ['description', 'string'],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'address' => Yii::t('app', 'Адрес'),
            'inn' => Yii::t('app', 'ИНН'),
            'passport_series' => Yii::t('app', 'Серия'),
            'passport_number' => Yii::t('app', 'Номер'),
            'passport_from' => Yii::t('app', 'Когда и кем выдан'),
            'phone' => Yii::t('app', 'Телефон'),
            'email' => Yii::t('app', 'Email'),
            'birthdate' => Yii::t('app', 'Дата рождения'),
            'description' => Yii::t('app', 'Примечание'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'agency_id' => Yii::t('app', 'Ответственное агенство'),
            'user_id' => Yii::t('app', 'Ответственный менеджер'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreements()
    {
        return $this->hasMany(Agreement::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(Agency::className(), ['id' => 'agency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlats()
    {
        return $this->hasMany(Flat::className(), ['client_id' => 'id']);
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
    
    /**
     * @return array
     */
    public function getOptions()
    {
        return \yii\helpers\ArrayHelper::map(static::find()->orderBy(['created_at' => SORT_DESC])->all(), 'id', 'fullname');
    }
}
