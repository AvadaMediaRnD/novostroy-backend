<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "roles_access_to_controller".
 *
 * @property int $id
 * @property int $role_id
 * @property string $controller_name
 *
 * @property Role $role
 */
class RoleAccessToController extends \common\models\ZModel
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'roles_access_to_controller';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id'], 'required'],
            [['role_id'], 'integer'],
            [['controller_name'], 'string', 'max' => 255],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],
        ];
    }




    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'role_id' => Yii::t('app', 'Role ID'),
            'controller_name' => Yii::t('app', 'Controller Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }
}
