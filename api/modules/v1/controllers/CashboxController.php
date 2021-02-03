<?php

namespace api\modules\v1\controllers;

use Yii;
use api\modules\v1\controllers\ZController as Controller;
use common\models\ViewCashbox;
use common\models\Cashbox;
use common\models\Invoice;
use common\models\Article;

class CashboxController extends Controller {

    /**
     * @api {get} /cashbox/get Get
     * @apiVersion 1.0.0
     * @apiName Get
     * @apiGroup Cashbox
     *
     * @apiDescription Получение информации о состоянии касс.
     *
     * @apiHeader {string} Authorization токен пользователя.
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Bearer TkhUmC6zbiTOEvzzoUlYXKnKZx5IYSfL"
     *  }
     *
     * @apiSuccess {Object} cashbox Данные по кассам
     * @apiSuccess {float} cashbox.usd Касса USD
     * @apiSuccess {float} cashbox.eur Касса EUR
     * @apiSuccess {float} cashbox.uah Касса UAH
     * @apiSuccess {float} cashbox.total Общая сумма в кассах, в пересчете в USD
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "code": 100200,
     *      "status": "success",
     *      "message": "",
     *      "data": {
     *          "cashbox": {
     *              "usd": 50000.5,
     *              "eur": 30000,
     *              "uah": 120000,
     *              "total": 100000.5,
     *          }
     *      }
     *  }
     *
     * @return array|null
     */
    public function actionGet() {
        $cashboxUsd = ViewCashbox::find()->where(['currency' => Cashbox::CURRENCY_USD])->one();
        $cashboxEur = ViewCashbox::find()->where(['currency' => Cashbox::CURRENCY_EUR])->one();
        $cashboxUah = ViewCashbox::find()->where(['currency' => Cashbox::CURRENCY_UAH])->one();
        $balanceUsd = floatval($cashboxUsd ? $cashboxUsd->price : 0);
        $balanceEur = floatval($cashboxEur ? $cashboxEur->price : 0);
        $balanceUah = floatval($cashboxUah ? $cashboxUah->price : 0);
        // TODO use rates
        $balanceTotal = $balanceUsd 
            + $balanceEur 
            + $balanceUah;
        
        $response = [
            'code' => static::CODE_SUCCESS,
            'status' => static::STATUS_SUCCESS,
            'message' => '',
            'data' => [
                'cashbox' => [
                    'usd' => round($balanceUsd, 2),
                    'eur' => round($balanceEur, 2),
                    'uah' => round($balanceUah, 2),
                    'total' => round($balanceTotal, 2),
                ],
            ],
        ];

        return $response;
    }

    /**
     * @api {get} /cashbox/get-in-out?from=:from&to=:to Get In Out
     * @apiVersion 1.0.0
     * @apiName Get In Out
     * @apiGroup Cashbox
     * 
     * @apiParam {integer} from Дата период с, в формате yyyy-MM-dd (2018-01-30).
     * @apiParam {integer} from Дата период до, в формате yyyy-MM-dd (2018-07-31).
     *
     * @apiDescription Получить данные по приходам/расходам за указанный период. Если диапазон не указан, то возвращается за "сегодня". Если нужно выбрать 1 день, то from и to передаем одинаковую дату.
     *
     * @apiHeader {string} Authorization токен пользователя.
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Bearer TkhUmC6zbiTOEvzzoUlYXKnKZx5IYSfL"
     *  }
     *
     * @apiSuccess {Object} income Данные по приходам
     * @apiSuccess {float} income.usd Данные по приходам USD
     * @apiSuccess {float} income.eur Данные по приходам EUR
     * @apiSuccess {float} income.uah Данные по приходам UAH
     * @apiSuccess {float} income.total Суммарные данные по приходам, USD
     * @apiSuccess {Object} outcome Данные по расходам
     * @apiSuccess {float} outcome.usd Данные по расходам USD
     * @apiSuccess {float} outcome.eur Данные по расходам EUR
     * @apiSuccess {float} outcome.uah Данные по расходам UAH
     * @apiSuccess {float} outcome.total Суммарные данные по расходам, USD
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "code": 100200,
     *      "status": "success",
     *      "message": "",
     *      "data": {
     *          "income": {
     *              "usd": 50000,
     *              "eur": 30000,
     *              "uah": 1200000,
     *              "total": 100000,
     *          }
     *          "outcome": {
     *              "usd": 50000,
     *              "eur": 30000,
     *              "uah": 1200000,
     *              "total": 100000,
     *          }
     *      }
     *  }
     *
     * @param string $from
     * @param string $to
     * @return array|null
     */
    public function actionGetInOut($from = '', $to = '') {
        // cashboxes
        $cashboxUsd = Cashbox::find()->where(['currency' => Cashbox::CURRENCY_USD])->one();
        $cashboxEur = Cashbox::find()->where(['currency' => Cashbox::CURRENCY_EUR])->one();
        $cashboxUah = Cashbox::find()->where(['currency' => Cashbox::CURRENCY_UAH])->one();

        // default today
        if (!$from && !$to) {
            $from = date('Y-m-d');
            $to = date('Y-m-d');
        }
        
        $invoicesIncomeQuery = Invoice::find()
            ->joinWith(['article', 'cashbox'])
            ->where(['article.type' => Article::TYPE_INCOME]);
        $invoicesOutcomeQuery = Invoice::find()
            ->joinWith(['article', 'cashbox'])
            ->where(['article.type' => Article::TYPE_OUTCOME]);
        
        if ($from) {
            $invoicesIncomeQuery->andWhere(['>=', 'uid_date', $from]);
            $invoicesOutcomeQuery->andWhere(['>=', 'uid_date', $from]);
        }
        if ($to) {
            $invoicesIncomeQuery->andWhere(['<=', 'uid_date', $to]);
            $invoicesOutcomeQuery->andWhere(['<=', 'uid_date', $to]);
        }
        
        $invoicesIncome = $invoicesIncomeQuery->all();
        $invoicesOutcome = $invoicesOutcomeQuery->all();
        
        // summary
        $income = [
            $cashboxUsd->id => 0,
            $cashboxEur->id => 0,
            $cashboxUah->id => 0,
            'total' => 0,
        ];
        $outcome = [
            $cashboxUsd->id => 0,
            $cashboxEur->id => 0,
            $cashboxUah->id => 0,
            'total' => 0,
        ];
        foreach ($invoicesIncome as $invoice) {
            $income[$invoice->cashbox_id] += $invoice->price;
        }
        foreach ($invoicesOutcome as $invoice) {
            $outcome[$invoice->cashbox_id] += $invoice->price;
        }
        // TODO use rates
        $income['total'] = $income[$cashboxUsd->id]
            + $income[$cashboxEur->id]
            + $income[$cashboxUah->id];
        $outcome['total'] = $outcome[$cashboxUsd->id]
            + $outcome[$cashboxEur->id]
            + $outcome[$cashboxUah->id];
        
        $response = [
            'code' => static::CODE_SUCCESS,
            'status' => static::STATUS_SUCCESS,
            'message' => '',
            'data' => [
                'income' => [
                    'usd' => round($income[$cashboxUsd->id], 2),
                    'eur' => round($income[$cashboxEur->id], 2),
                    'uah' => round($income[$cashboxUah->id], 2),
                    'total' => round($income['total'], 2),
                ],
                'outcome' => [
                    'usd' => round($outcome[$cashboxUsd->id], 2),
                    'eur' => round($outcome[$cashboxEur->id], 2),
                    'uah' => round($outcome[$cashboxUah->id], 2),
                    'total' => round($outcome['total'], 2),
                ],
            ],
        ];

        return $response;
    }

}
