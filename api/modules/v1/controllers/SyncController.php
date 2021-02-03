<?php


namespace api\modules\v1\controllers;

use api\modules\v1\controllers\ZController as Controller;
use common\models\Agency;
use common\models\Client;
use common\models\House;
use common\models\User;
use Yii;

class SyncController extends Controller
{

    /**
     * @api {post} /sync/get-flat-statuses Get Flat Statuses
     * @apiVersion 1.0.0
     * @apiName Get Flat Statuses
     * @apiGroup Sync
     *
     * @apiDescription Получить статусы квартир.
     *      Возможные варианты статусов:
     *      10 - Активна
     *      8  - Бронь
     *      7  - Резерв
     *      5  - Готовы покупать
     *      2  - Снята с продаж
     *      1  - Продана
     *      0  - Неактивна
     *
     * @apiParam {String} sectionToken  Идентификатор дома/секции в системе Новострой.
     *
     * @apiSuccess {Object[]} flatStatuses Список объектов с данными
     * @apiSuccess {integer} flatStatuses.number Номер квартиры/помещения
     * @apiSuccess {integer} flatStatuses.status Статус продажи/бронирования
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "code": 100200,
     *      "status": "success",
     *      "message": "",
     *      "data": {
     *          "flatStatuses": [
     *              {
     *                  "number": 1,
     *                  "status": 10
     *              },
     *              {
     *                  "number": 2,
     *                  "status": 7
     *              },
     *          ]
     *      }
     *  }
     *
     * @return array|null
     */
    public function actionGetFlatStatuses()
    {
        $sectionToken = Yii::$app->request->post('sectionToken') ?? false;

        if (!$sectionToken){
            return [
                'code' => static::CODE_ERROR_VALIDATION,
                'status' => static::STATUS_ERROR,
                'message' => static::ERROR_MESSAGE_VALIDATION,
                'data' => null,
            ];
        }

        $flatStatuses = [];

        $house = House::findOne(['sync_id' => $sectionToken]);
        if (!$house){
            return [
                'code' => static::RESPONSE_STATUS_ERROR_NOT_FOUND,
                'status' => static::STATUS_ERROR,
                'message' => static::ERROR_MESSAGE_NOT_FOUND,
                'data' => null,
            ];
        }
        $flats = $house->flats;

        foreach ($flats as $flat){
            $clientInfo = [];
            $agencyInfo = [];
            $managerInfo = [];
            $unit_type = $flat['unit_type'];
            $client = $flat->client;
            $agency = Agency::findOne(['id' => $flat['agency_id']]);
            $manager = User::findOne(['id' => $client['user_id']]);
//            var_dump($manager);

            $clientInfo = [
                'client_name' => $client ? $client->getFullname() : "",
                'client_phone' => $client ? $client->phone : "",
                'client_email' => $client ? $client->email : "",
            ];


            $agencyInfo = [
                'agencyName' => $agency ? $agency['name'] : "",
                'agencyPhone' => $agency ? $agency['phone'] : "",
                'agencyEmail' => $agency ? $agency['email'] : "",
            ];


            $managerInfo = [
                'managerName' => $manager ? $manager->getFullname() : "",
                'managerPhone' => $manager ? $manager['phone'] : "",
                'managerEmail' => $manager ? $manager['email'] : "",
            ];

            $flatNumber = $flat['number'];
            if (isset($flat['number_index']) && !empty($flat['number_index'])){
                $flatNumber .= $flat['number_index'];
            }

            $flatStatuses[] = [
                'number' => $flatNumber,
                'status' => $flat['status'],
                'unit_type' => $unit_type,
                'timeStamp' => $flat['updated_at'],
                'clientInfo' => $clientInfo,
                'agencyInfo' => $agencyInfo,
                'managerInfo' => $managerInfo,
            ];
        }

        return [
            'code' => static::CODE_SUCCESS,
            'status' => static::STATUS_SUCCESS,
            'message' => '',
            'data' => [
                'flatStatuses' => $flatStatuses,
            ],
        ];
    }
}