<?php

namespace api\modules\v1\controllers;

use common\models\User;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

class ZController extends Controller {

    // http codes
    const RESPONSE_STATUS_SUCCESS = 200;
    const RESPONSE_STATUS_ERROR_DEFAULT = 400;
    const RESPONSE_STATUS_ERROR_UNAUTHORIZED = 401;
    const RESPONSE_STATUS_ERROR_FORBIDDEN = 403;
    const RESPONSE_STATUS_ERROR_NOT_FOUND = 404;
    const RESPONSE_STATUS_ERROR_VALIDATION = 440;

    // custom error codes. Any coincidences are occasional
    const CODE_SUCCESS = 100200;
    const CODE_ERROR_DEFAULT = 100400;
    const CODE_ERROR_UNAUTHORIZED = 100401;
    const CODE_ERROR_FORBIDDEN = 100403;
    const CODE_ERROR_VALIDATION = 100440;

    // some general error messages
    const ERROR_MESSAGE_VALIDATION = 'Validation failed';
    const ERROR_MESSAGE_UNAUTHORIZED = 'Authorization required';
    const ERROR_MESSAGE_FORBIDDEN = 'Access not allowed';
    const ERROR_MESSAGE_NOT_FOUND = 'Object not found';

    const STATUS_ERROR = 'error';
    const STATUS_SUCCESS = 'success';

    protected $actionsAllowUnauthorized = ['test', 'get-flat-statuses'];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'except' => $this->actionsAllowUnauthorized,
            'authMethods' => [
                [
                    'class' => HttpBasicAuth::className(),
                    'auth' => function ($username, $password) {
                        $user = User::findByUsername($username);
                        if ($user === null || !$user->validatePassword($password)) {
                            return null;
                        }
                        if ($user->role != User::ROLE_ADMIN) {
                            throw new \yii\web\ForbiddenHttpException();
                        }
                        return $user;
                    }
                ],
                HttpBearerAuth::className(),
            ],
        ];

        return $behaviors;
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            // log request
            $log = "\n----------------\n".date('Y-m-d H:i:s')."\nIP:".Yii::$app->request->getUserIP()."\n"
                . Yii::$app->request->absoluteUrl."\n"
                . print_r(Yii::$app->request->get(), true)."\n"
                . print_r(Yii::$app->request->post(), true)."\n"
                . print_r(Yii::$app->request->getHeaders()->toArray(), true)."\n";
            file_put_contents(Yii::getAlias('@app/web/apilog.txt'), $log, FILE_APPEND);
        }
        
        return true;
    }
}
