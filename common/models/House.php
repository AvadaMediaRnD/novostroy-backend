<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "house".
 *
 * @property int $id
 * @property string $name
 * @property string $section
 * @property string $address
 * @property int $n_floors
 * @property double $commission_agency
 * @property double $commission_agency_type
 * @property double $commission_manager
 * @property double $commission_manager_type
 * @property string $company_name
 * @property int $status
 * @property string $sync_id
 *
 * @property Flat[] $flats
 * @property User[] $users
 */
class House extends \common\models\ZModel
{
    const STATUS_AVAILABLE = 10;
    const STATUS_UNAVAILABLE = 0;
    const COMMISSION_TYPE_PERCENT = 0;
    const COMMISSION_TYPE_VALUE = 1;
    
    public $userIds;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'house';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['n_floors', 'status', 'commission_agency_type', 'commission_manager_type'], 'integer'],
            [['commission_agency', 'commission_manager'], 'number'],
            [['commission_agency', 'commission_manager'], 'default', 'value' => 0],
            [['name', 'section', 'address', 'company_name'], 'string', 'max' => 255],
            [['sync_id'], 'string', 'max' => 16],
            ['name', 'required'],
            ['userIds', 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Дом'),
            'section' => Yii::t('app', 'Секция'),
            'address' => Yii::t('app', 'Адрес'),
            'n_floors' => Yii::t('app', 'Этажей'),
            'commission_agency' => Yii::t('app', 'Комиссия агентства'),
            'commission_manager' => Yii::t('app', 'Комиссия менеджера'),
            'commission_agency_type' => Yii::t('app', 'Тип комиссии агентства'),
            'commission_manager_type' => Yii::t('app', 'Тип комиссии менеджера'),
            'company_name' => Yii::t('app', 'Название компании'),
            'status' => Yii::t('app', 'Статус'),
            'userIds' => Yii::t('app', 'Пользователи'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord){
            do {
                $syncToken = Yii::$app->security->generateRandomString(16);
                $syncIdExists = self::find()->where(['sync_id' => $syncToken])->count();
            } while ($syncIdExists);
            $this->sync_id = $syncToken;
        }
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->userIds !== null) {
            UserHouse::deleteAll(['house_id' => $this->id]);
            if ($this->userIds) {
                foreach ($this->userIds as $id) {
                    $userHouse = new UserHouse(['house_id' => $this->id, 'user_id' => $id]);
                    $userHouse->save();
                }
            }
            // admins always selected
            foreach (User::getOptions([User::ROLE_ADMIN]) as $id => $name) {
                $userHouse = new UserHouse(['house_id' => $this->id, 'user_id' => $id]);
                $userHouse->save();
            }
        }
        
        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlats()
    {
        return $this->hasMany(Flat::className(), ['house_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
            ->viaTable('user_house', ['house_id' => 'id']);
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
        return $this->name . ', ' . $this->section;
    }
    
    /**
     * Get array of model names for filter options
     * @return array
     */
    public function getOptions()
    {
        return \yii\helpers\ArrayHelper::map(static::find()->all(), 'id', function ($model) {
            return $model->getNameSection();
        });
    }
    
    /**
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            static::STATUS_AVAILABLE => Yii::t('model', 'В продаже'),
            static::STATUS_UNAVAILABLE => Yii::t('model', 'Продан'),
        ];
    }

    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabel($status = null)
    {
        $status = $status == null ? $this->status : $status;
        $options = static::getStatusOptions();
        return isset($options[$status]) ? $options[$status] : null;
    }
    
    /**
     * @return array
     */
    public static function getCommissionTypeOptions()
    {
        return [
            static::COMMISSION_TYPE_PERCENT => Yii::t('model', 'Процент'),
            static::COMMISSION_TYPE_VALUE => Yii::t('model', 'Фиксировано'),
        ];
    }
    
    /**
     * @param null $type
     * @return mixed|null
     */
    public function getCommissionAgencyTypeLabel($type = null)
    {
        $type = $type == null ? $this->commission_agency_type : $type;
        $options = static::getCommissionTypeOptions();
        return isset($options[$type]) ? $options[$type] : null;
    }
    
    /**
     * @param null $type
     * @return mixed|null
     */
    public function getCommissionManagerTypeLabel($type = null)
    {
        $type = $type == null ? $this->commission_manager_type : $type;
        $options = static::getCommissionTypeOptions();
        return isset($options[$type]) ? $options[$type] : null;
    }
    
    /**
     * 
     * @return string
     */
    public function getCompanyRegInfo()
    {
        return 'РЕКВИЗИТЫ КОМПАНИИ';
    }
    
}
