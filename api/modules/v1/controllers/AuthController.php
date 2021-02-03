<?php

namespace api\modules\v1\controllers;

use Yii;
use api\modules\v1\controllers\ZController as Controller;

class AuthController extends Controller {
    
    protected function verbs() {
        return [
            'login' => ['POST'],
        ];
    }

    /**
     * @api {post} /auth/login Login
     * @apiVersion 1.0.0
     * @apiName Login
     * @apiGroup Auth
     *
     * @apiDescription Логин пользователя, в header необходимо передать через basic авторизацию логин:пароль в base64
     *
     * @apiHeader {string} Authorization Логин и пароль в base64.
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YWRtaW5AYWRtaW4uY29tOjExMTExMQ=="
     *  }
     *
     * @apiSuccess {string} access_token Токен пользователя.
     * @apiSuccess {string} web_url Ссылка на кабинет пользователя.
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "code": 100200,
     *      "status": "success",
     *      "message": "",
     *      "data": {
     *          "access_token": "TkhUmC6zbiTOEvzzoUlYXKnKZx5IYSfL",
     *          "web_url": "http://{domain}/site/login?token=TkhUmC6zbiTOEvzzoUlYXKnKZx5IYSfL"
     *      }
     *  }
     *
     * @return array|null
     */
    public function actionLogin() {
        /* @var  $user \common\models\User */
        $user = Yii::$app->user->identity;
        $response = [
            'code' => static::CODE_SUCCESS,
            'status' => static::STATUS_SUCCESS,
            'message' => '',
            'data' => [
                'access_token' => $user->getAuthKey(),
                'web_url' => Yii::$app->urlManagerBackend->createAbsoluteUrl(['site/login', 'token' => $user->getAuthKey()]),
            ],
        ];
        return $response;
    }
    
}
