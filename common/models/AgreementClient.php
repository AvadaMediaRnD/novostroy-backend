<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\helpers\DateHelper;

/**
 * This is the model class for table "agreement_client".
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
 * @property int $agreement_id
 *
 * @property Agreement $agreement
 */
class AgreementClient extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agreement_client';
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
            [['agreement_id'], 'required'],
            [['created_at', 'updated_at', 'agreement_id'], 'integer'],
            [['firstname', 'middlename', 'lastname', 'address', 'inn', 'passport_series', 'passport_number', 'passport_from', 'phone', 'email', 'description', 'birthdate'], 'string', 'max' => 255],
            [['agreement_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agreement::className(), 'targetAttribute' => ['agreement_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'firstname' => Yii::t('app', 'Firstname'),
            'middlename' => Yii::t('app', 'Middlename'),
            'lastname' => Yii::t('app', 'Lastname'),
            'address' => Yii::t('app', 'Address'),
            'inn' => Yii::t('app', 'Inn'),
            'passport_series' => Yii::t('app', 'Passport Series'),
            'passport_number' => Yii::t('app', 'Passport Number'),
            'passport_from' => Yii::t('app', 'Passport From'),
            'phone' => Yii::t('app', 'Phone'),
            'email' => Yii::t('app', 'Email'),
            'birthdate' => Yii::t('app', 'Birthdate'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'agreement_id' => Yii::t('app', 'Agreement ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgreement()
    {
        return $this->hasOne(Agreement::className(), ['id' => 'agreement_id']);
    }
    
    /**
     * @return string
     */
    public function getBirthdateText()
    {
        // return date('d', strtotime($this->birthdate)) . ' ' . DateHelper::getMonthName(date('n', strtotime($this->birthdate)), false, true) . ' ' . date('Y', strtotime($this->birthdate));
        return date('d.m.Y', strtotime($this->birthdate));
    }
}
