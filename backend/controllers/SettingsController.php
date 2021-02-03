<?php
namespace backend\controllers;

use Yii;
use backend\controllers\ZController as Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\User;
use backend\models\Role;
use backend\models\Cashbox;
use common\models\Cashbox as CashboxBase;
use yii\data\ActiveDataProvider;
use backend\models\UserSearch;
use backend\models\UserForm;
use yii\widgets\ActiveForm;
use backend\models\SystemConfigSearch;
use common\models\SystemConfig;

/**
 * Settings controller
 */
class SettingsController extends ZController
{

    /**
     * Displays settings cash currency page.
     *
     * @return string
     */
    public function actionCashCurrency($id = null)
    {
        $provider = new ActiveDataProvider([
            'query' => Cashbox::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        if (isset($id)) {
            $model = Cashbox::findOne(['id' => $id]);

            if ($model->load(Yii::$app->request->post())) {
                $model->save();
            }
        }

        $state = SystemConfig::getConfigModelByKey('currencySync', true);

        return $this->render('cash-currency',
            [
                'provider' => $provider,
                'models' =>  $provider->getModels(),
                'currencySync' => $state->value_raw,
            ]);
    }

    /**
     * Change user roles access to controllers
     */
    public function actionUpdateRolesAccess() {
        Role::updateRolesAccesses(Yii::$app->request);
        return $this->redirect(['roles']);
    }
    
    /**
     * Change automatic update checkbox. If is checked we update rates with privat api
     */
    public function actionUpdateCurrencyRates() {
        // save checkbox state in config
        $state = Yii::$app->request->post('currencySync');
        $model = SystemConfig::getConfigModelByKey('currencySync', true);
        $model->value_raw = $state ? '1' : '0';

        $model->save();
        // if checkbox is checked update rates with api
        if ($state) {
            CashboxBase::syncRatesOnPrivatbankApi();
        }

        return $this->redirect(['cash-currency']);
    }

    /**
     * Displays settings roles page.
     *
     * @return string
     */
    public function actionRoles()
    {
        $provider = new ActiveDataProvider([
            'query' => Role::find()->where(['<>', 'name', 'default']),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('roles',
            [
                'provider' => $provider,
            ]);
    }

    /**
     * Displays settings users page.
     *
     * @return string
     */
    public function actionUsers()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('users', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUserCreate()
    {
        $model = new User();
        $modelForm = new UserForm();
        $modelForm->load(['UserForm' => $model->attributes]);
        
        if (Yii::$app->request->isAjax && $modelForm->load(Yii::$app->request->post())) {
            return ActiveForm::validate($modelForm);
        }
        
        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->save()) {
            return $this->redirect(['/settings/users']);
        }

        return $this->redirect(['/settings/users']);
    }
    
    /**
     * Update a User model.
     * @param integer $id
     * @return mixed
     */
    public function actionUserUpdate($id)
    {
        $model = User::findOne($id);
        $modelForm = new UserForm();
        $modelForm->load(['UserForm' => $model->attributes]);
        $modelForm->birthdate = Yii::$app->formatter->asDate($modelForm->birthdate ?: date('Y-m-d'));
        
        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->save()) {
            Yii::$app->session->setFlash('success', 'Данные сохранены');
            return $this->redirect(['/settings/user-update', 'id' => $model->id]);
        }

        return $this->render('user-update', [
            'model' => $model,
            'modelForm' => $modelForm,
        ]);
    }
    
    /**
     * Send invite to User.
     * @param integer $id
     * @return mixed
     */
    public function actionUserInvite($id)
    {
        $model = User::findOne($id);
        if ($model) {
            $model->sendInvite();
        }
        
        Yii::$app->session->setFlash('success', 'Приглашение отправлено');
        
        return $this->redirect(['/settings/users']);
    }
    
    /**
     * Activate User.
     * @param integer $id
     * @return mixed
     */
    public function actionUserActivate($id)
    {
        $model = User::findOne($id);
        if ($model) {
            $model->changeStatus(User::STATUS_ACTIVE);
        }
        
        return $this->redirect(['/settings/users']);
    }
    
    /**
     * Disable User.
     * @param integer $id
     * @return mixed
     */
    public function actionUserDisable($id)
    {
        $model = User::findOne($id);
        if ($model) {
            $model->changeStatus(User::STATUS_DISABLED);
        }
        
        return $this->redirect(['/settings/users']);
    }
    
    /**
     * Delete User.
     * @param integer $id
     * @return mixed
     */
    public function actionUserDelete($id)
    {
        $model = User::findOne($id);
        if ($model) {
            $model->delete();
        }
        
        return $this->redirect(['/settings/users']);
    }

    /**
     * Displays settings variables page.
     *
     * @return string
     */
    public function actionVariables()
    {
        $searchModel = new SystemConfigSearch();
        $searchModel->type = SystemConfig::TYPE_VARIABLE;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('variables', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Creates a new SystemConfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionVariableCreate()
    {
        $model = new SystemConfig();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/settings/variables']);
        }

        return $this->render('variable-create', [
            'model' => $model,
        ]);
    }
    
    /**
     * Updates a SystemConfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionVariableUpdate($id)
    {
        $model = SystemConfig::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException();
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/settings/variables']);
        }

        return $this->render('variable-update', [
            'model' => $model,
        ]);
    }
    
    /**
     * Delete SystemConfig.
     * @param integer $id
     * @return mixed
     */
    public function actionVariableDelete($id)
    {
        $model = SystemConfig::findOne($id);
        if ($model) {
            $model->delete();
        }
        
        return $this->redirect(['/settings/variables']);
    }
}
