<?php

namespace backend\controllers;

use common\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use backend\models\RoleAccessToController;
use yii\filters\AccessControl;
use backend\filters\AccessRule;

/**
 * ZController override the default Controller.
 *
 * @inheritdoc
 */
class ZController extends \yii\web\Controller
{
    const FORBIDDEN_HTTP_MESSAGE = 'Доступ запрещен';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $allowsRolesArray = ArrayHelper::getColumn(RoleAccessToController::findAll(['controller_name' => Yii::$app->controller->id]), function ($model) {
            return (string)$model->role->name;
        });

        $allowsRolesArray[] = 'admin';
        
        // everyone can see his profile
        if ($this->route == 'settings/user-update' && Yii::$app->user->id == Yii::$app->request->get('id')) {
            $allowsRolesArray[] = 'admin';
            $allowsRolesArray[] = 'fin_director';
            $allowsRolesArray[] = 'accountant';
            $allowsRolesArray[] = 'sales_manager';
            $allowsRolesArray[] = 'viewer_flat';
            $allowsRolesArray[] = 'manager';
        }

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'class' => AccessRule::className(),
                        'allow' => empty($allowsRolesArray) ? false: true,
                        'roles' => $allowsRolesArray
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    return $this->redirect('/site/login');
                },
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (Yii::$app->request->isAjax
            || Yii::$app->request->get('ajax')
            || Yii::$app->request->post('ajax')
        ) {
            Yii::$app->response->headers->set('Cache-Control', 'no-cache');
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
        
        if ($token = Yii::$app->request->get('token')) {
            $user = User::findIdentityByAutoLoginToken($token);
            if ($user) {
                Yii::$app->user->login($user);
            }
        }

        if (Yii::$app->user->isGuest && Yii::$app->requestedRoute != 'site/login') {
            if (Yii::$app->user->isGuest) {
                $this->redirect(['/site/login']);
                return false;
            }
        }

        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function goBack($defaultUrl = null)
    {
        if (!$defaultUrl) {
            $defaultUrl = !empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null;
        }
        return parent::goBack($defaultUrl);
    }

}