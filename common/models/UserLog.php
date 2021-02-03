<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_log".
 *
 * @property int $id
 * @property string $event
 * @property string $object_class
 * @property int $object_id
 * @property string $old_attributes
 * @property string $message
 * @property int $created_at
 * @property int $updated_at
 * @property int $user_id
 *
 * @property User $user
 */
class UserLog extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_log';
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
            [['object_id', 'created_at', 'updated_at', 'user_id'], 'integer'],
            [['old_attributes', 'message'], 'string'],
            [['created_at', 'updated_at', 'user_id'], 'required'],
            [['event', 'object_class'], 'string', 'max' => 255],
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
            'event' => Yii::t('app', 'Event'),
            'object_class' => Yii::t('app', 'Object Class'),
            'object_id' => Yii::t('app', 'Object ID'),
            'old_attributes' => Yii::t('app', 'Old Attributes'),
            'message' => Yii::t('app', 'Message'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
