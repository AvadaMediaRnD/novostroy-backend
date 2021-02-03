<?php

namespace backend\controllers;

use Yii,
    yii\filters\VerbFilter,
    yii\filters\AccessControl;
use backend\controllers\ZController as Controller,
    backend\models\RoleAccessToController;
use common\models\LoginForm,
    common\models\User;

/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        return $this->redirect(['/statistic/index']);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin($token = null) {

        $this->layout = 'login';
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->user->identity->status !== User::STATUS_ACTIVE) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('error', 'Данная запись заблокирована или удалена');
                return $this->redirect(["site/login"]);
            }
            if (Yii::$app->user->identity->hasAccessToController('statistic')) {
                return $this->goHome();
            } else {
                if (Yii::$app->user->identity->status == User::STATUS_ACTIVE) {
                    $controllerAccess = RoleAccessToController::findOne(['role_id' => Yii::$app->user->identity->role]);
                    return $this->redirect(["$controllerAccess->controller_name/index"]);
                } else {
                    Yii::$app->user->logout();
                    Yii::$app->session->setFlash('error', 'Данная запись заблокирована или удалена');
                    return $this->redirect(["site/login"]);
                }
            }
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

}
