<?php

namespace api\modules\v1\controllers;

use Yii;
use api\modules\v1\controllers\ZController as Controller;
use common\models\Flat;
use common\models\House;
use common\models\ViewHouseTotal;

class HouseController extends Controller {

    /**
     * @api {get} /house/get-list Get List
     * @apiVersion 1.0.0
     * @apiName Get List
     * @apiGroup House
     *
     * @apiDescription Получить список доступных объектов, включая статистику по объектам + общую статистику.
     *
     * @apiHeader {string} Authorization токен пользователя.
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Bearer TkhUmC6zbiTOEvzzoUlYXKnKZx5IYSfL"
     *  }
     *
     * @apiSuccess {Object[]} houses Список объектов с данными
     * @apiSuccess {integer} houses.id ID объекта
     * @apiSuccess {integer} houses.name Название объекта
     * @apiSuccess {Object} houses.flatTotal Статистика по количеству квартир
     * @apiSuccess {integer} houses.flatTotal.total Всего
     * @apiSuccess {integer} houses.flatTotal.sold Продано
     * @apiSuccess {integer} houses.flatTotal.available Доступно
     * @apiSuccess {Object} houses.squareTotal Статистика по площади
     * @apiSuccess {float} houses.squareTotal.total Всего
     * @apiSuccess {float} houses.squareTotal.sold Продано
     * @apiSuccess {float} houses.squareTotal.available Доступно
     * @apiSuccess {Object} houses.priceTotal Статистика по стоимости квартир
     * @apiSuccess {float} houses.priceTotal.total Всего
     * @apiSuccess {float} houses.priceTotal.sold Продано
     * @apiSuccess {float} houses.priceTotal.available Доступно
     * @apiSuccess {Object} all Данные по всем объектам
     * @apiSuccess {Object} all.flatTotal Статистика по количеству квартир по всем объектам
     * @apiSuccess {integer} all.flatTotal.total Всего
     * @apiSuccess {integer} all.flatTotal.sold Продано
     * @apiSuccess {integer} all.flatTotal.available Доступно
     * @apiSuccess {Object} all.squareTotal Статистика по площади по всем объектам
     * @apiSuccess {float} all.squareTotal.total Всего
     * @apiSuccess {float} all.squareTotal.sold Продано
     * @apiSuccess {float} all.squareTotal.available Доступно
     * @apiSuccess {Object} all.priceTotal Статистика по стоимости квартир по всем объектам
     * @apiSuccess {float} all.priceTotal.total Всего
     * @apiSuccess {float} all.priceTotal.sold Продано
     * @apiSuccess {float} all.priceTotal.available Доступно
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "code": 100200,
     *      "status": "success",
     *      "message": "",
     *      "data": {
     *          "houses": [
     *              {
     *                  "id": 129,
     *                  "name": "ЖК Радостный секция 1",
     *                  "flatTotal": {
     *                      "total": 200,
     *                      "sold": 50,
     *                      "available": 150,
     *                  },
     *                  "squareTotal": {
     *                      "total": 4000,
     *                      "sold": 1000,
     *                      "available": 3000,
     *                  },
     *                  "priceTotal": {
     *                      "total": 4000000,
     *                      "sold": 1000000,
     *                      "available": 3000000,
     *                  },
     *              },
     *              {
     *                  "id": 130,
     *                  "name": "ЖК Радостный секция 2",
     *                  "flatTotal": {
     *                      "total": 200,
     *                      "sold": 50,
     *                      "available": 150,
     *                  },
     *                  "squareTotal": {
     *                      "total": 4000,
     *                      "sold": 1000,
     *                      "available": 3000,
     *                  },
     *                  "priceTotal": {
     *                      "total": 4000000,
     *                      "sold": 1000000,
     *                      "available": 3000000,
     *                  },
     *              },
     *              {
     *                  "id": 131,
     *                  "name": "ЖК Отдельный",
     *                  "flatTotal": {
     *                      "total": 200,
     *                      "sold": 50,
     *                      "available": 150,
     *                  },
     *                  "squareTotal": {
     *                      "total": 4000,
     *                      "sold": 1000,
     *                      "available": 3000,
     *                  },
     *                  "priceTotal": {
     *                      "total": 4000000,
     *                      "sold": 1000000,
     *                      "available": 3000000,
     *                  },
     *              }
     *          ],
     *          "all": {
     *              "flatTotal": {
     *                  "total": 200,
     *                  "sold": 50,
     *                  "available": 150,
     *              },
     *              "squareTotal": {
     *                  "total": 4000,
     *                  "sold": 1000,
     *                  "available": 3000,
     *              },
     *              "priceTotal": {
     *                  "total": 4000000,
     *                  "sold": 1000000,
     *                  "available": 3000000,
     *              },
     *          }
     *      }
     *  }
     *
     * @return array|null
     */
    public function actionGetList() {
        // init data arrays
        $housesData = [];
        $allData = [
            'flatTotal' => [
                'total' => 0,
                'sold' => 0,
                'available' => 0,
            ],
            'squareTotal' => [
                'total' => 0,
                'sold' => 0,
                'available' => 0,
            ],
            'priceTotal' => [
                'total' => 0,
                'sold' => 0,
                'available' => 0,
            ],
        ];
        
        // collect data
        $viewHouseTotals = ViewHouseTotal::find()->all();
        foreach ($viewHouseTotals as $viewHouseTotal) {
            $housesData[] = [
                'id' => $viewHouseTotal->id,
                'name' => $viewHouseTotal->getNameSection(),
                'flatTotal' => [
                    'total' => (int)$viewHouseTotal->flats_total,
                    'sold' => (int)$viewHouseTotal->flats_sold,
                    'available' => (int)$viewHouseTotal->flats_available,
                ],
                'squareTotal' => [
                    'total' => round(floatval($viewHouseTotal->square_total), 2),
                    'sold' => round(floatval($viewHouseTotal->square_sold), 2),
                    'available' => round(floatval($viewHouseTotal->square_available), 2),
                ],
                'priceTotal' => [
                    'total' => round(floatval($viewHouseTotal->price_total), 2),
                    'sold' => round(floatval($viewHouseTotal->price_sold), 2),
                    'available' => round(floatval($viewHouseTotal->price_available), 2),
                ],
            ];
            
            $allData['flatTotal']['total'] += (int)$viewHouseTotal->flats_total;
            $allData['flatTotal']['sold'] += (int)$viewHouseTotal->flats_sold;
            $allData['flatTotal']['available'] += (int)$viewHouseTotal->flats_available;
            $allData['squareTotal']['total'] += round(floatval($viewHouseTotal->square_total), 2);
            $allData['squareTotal']['sold'] += round(floatval($viewHouseTotal->square_sold), 2);
            $allData['squareTotal']['available'] += round(floatval($viewHouseTotal->square_available), 2);
            $allData['priceTotal']['total'] += floatval($viewHouseTotal->price_total);
            $allData['priceTotal']['sold'] += floatval($viewHouseTotal->price_sold);
            $allData['priceTotal']['available'] += floatval($viewHouseTotal->price_available);
            
            // fix price round
            $allData['priceTotal']['total'] = round($allData['priceTotal']['total'], 2);
            $allData['priceTotal']['sold'] = round($allData['priceTotal']['sold'], 2);
            $allData['priceTotal']['available'] = round($allData['priceTotal']['available'], 2);
        }
        
        $response = [
            'code' => static::CODE_SUCCESS,
            'status' => static::STATUS_SUCCESS,
            'message' => '',
            'data' => [
                'houses' => $housesData,
                'all' => $allData
            ],
        ];

        return $response;
    }

}
