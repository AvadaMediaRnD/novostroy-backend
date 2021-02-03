<?php

namespace backend\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "roles".
 *
 * @property int $id
 * @property string $name
 *
 * @property RolesAccessToController[] $rolesAccessToControllers
 */
class Role extends \common\models\ZModel
{
    public $agency;
    public $base;
    public $contracts;
    public $flats;
    public $objects;
    public $payments;
    public $settings;
    public $statistic;
    public $clients;
    public $application;

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->initRoleAccesses();
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRolesAccessToControllers()
    {
        return $this->hasMany(RolesAccessToController::className(), ['role_id' => 'id']);
    }

    public function initRoleAccesses() {
        $this->agency =  RoleAccessToController::findOne(['controller_name' => 'agency', 'role_id' => $this->id]) ? true : false;
        $this->contracts = RoleAccessToController::findOne(['controller_name' => 'contracts', 'role_id' => $this->id]) ? true : false;
        $this->flats = RoleAccessToController::findOne(['controller_name' => 'flats', 'role_id' => $this->id]) ? true : false;
        $this->objects = RoleAccessToController::findOne(['controller_name' => 'objects', 'role_id' => $this->id]) ? true : false;
        $this->payments = RoleAccessToController::findOne(['controller_name' => 'payments', 'role_id' => $this->id]) ? true : false;
        $this->settings = RoleAccessToController::findOne(['controller_name' => 'settings', 'role_id' => $this->id]) ? true : false;
        $this->statistic = RoleAccessToController::findOne(['controller_name' => 'statistic', 'role_id' => $this->id]) ? true : false;
        $this->clients = RoleAccessToController::findOne(['controller_name' => 'clients', 'role_id' => $this->id]) ? true : false;
        $this->application = RoleAccessToController::findOne(['controller_name' => 'application', 'role_id' => $this->id]) ? true : false;
    }

    static function updateRolesAccesses(\yii\web\Request $request) {

        foreach ($request->post() as $key => $row) {
            if ($key !== "_csrf-backend") {
                RoleAccessToController::deleteAll(['controller_name' => $key]);

                foreach ($row as  $roleString) {
                    $roleArray = explode("_", $roleString);

                    if ($roleArray[0] === 'check' && $roleArray[1] !== User::ROLE_ADMIN) {
                        //if ($key !== 'contracts' || ($roleArray[1] == User::ROLE_FIN_DIRECTOR || $roleArray[1] == User::ROLE_ACCOUNTANT)) {
                            $roleAccessToController = new RoleAccessToController;
                            $roleAccessToController->controller_name = $key;
                            $roleAccessToController->role_id = $roleArray[1];
                            $roleAccessToController->save();
                        //}
                    }
                }
            }
        }
    }
}
