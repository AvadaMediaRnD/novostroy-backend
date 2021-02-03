<?php

namespace backend\controllers;

use Yii,
    yii\helpers\ArrayHelper,
    yii\data\Pagination;
use backend\models\ViewTotalFlatSearch,
    backend\controllers\ZController as Controller;
use common\models\ViewTotalPlanFact,
    common\models\ViewHouseTotal,
    common\models\House,
    common\models\Flat,
    common\models\Payment,
    common\helpers\DateHelper;

/**
 * Statistic controller
 */
class StatisticController extends Controller {

    /**
     * Displays statistic index page.
     *
     * @param string $date_range
     * @param integer|null $house_id
     * @return string
     */
    public function actionIndex($date_range = '', $house_id = null, $house_name = null) {

        $ymFrom = '2020-01';
        $ymTo = '2020-12';
        if ($date_range) {
            $rangeParts = explode(' - ', $date_range);
            $dateFrom = $rangeParts[0];
            $dateTo = $rangeParts[1];
            $ymFrom = date('Y-m', strtotime($dateFrom));
            $ymTo = date('Y-m', strtotime($dateTo));
        }

        $sections = Yii::$app->request->get('sections');

        $idFilterArray = null;
        if ($house_name || $sections) {
            $filterArrayQuery = House::find()->select('id');
            if ($house_name) {
                $filterArrayQuery->andWhere(['name' => $house_name]);
            }
            if ($sections) {
                $filterArrayQuery->andWhere(['in', 'section', $sections]);
            }
            $idFilterArray = ArrayHelper::getColumn($filterArrayQuery->asArray()->all(), 'id');
        }

        $query = ViewTotalPlanFact::find();
        if (!$idFilterArray || count($idFilterArray) > 1) {
            $query->select([
                'house_id',
                'year',
                'month',
                'year_month',
                new \yii\db\Expression('SUM(`price_plan_total`) AS `price_plan_total`'),
                new \yii\db\Expression('SUM(`price_fact_total`) AS `price_fact_total`'),
                new \yii\db\Expression('SUM(`price_saldo_total`) AS `price_saldo_total`'),
                new \yii\db\Expression('SUM(`price_debt_total`) AS `price_debt_total`')
            ]);
        }

        //$query->andFilterWhere(['house_id' => $house_id]);
        if ($idFilterArray) {
            $query->andFilterWhere(['in', 'house_id', $idFilterArray]);
        }

        $fullQuery = clone $query;

        $query->andFilterWhere(['>=', 'year_month', $ymFrom]);
        $query->andFilterWhere(['<=', 'year_month', $ymTo]);

        $query->groupBy('year_month');

        $fullQuery->andFilterWhere(['>=', 'year_month', '2018-06']);
        $fullQuery->andFilterWhere(['<=', 'year_month', $ymTo]);

        $fullQuery->groupBy('year_month');

        $fullModel = $fullQuery->all();
        $startFactTotal = 0.00;
        foreach ($fullModel as $k => $fmodel) {
            if ($fmodel->year_month < $ymFrom) {
                $startFactTotal += $fmodel->price_fact_total;
            }
        }

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 0]);

        $models = $query->offset($pages->offset)->limit($pages->limit)->all();

        // houses selector
        $houses = House::find()->all();

        // chart
        $chartLabels = [];
        $chartDataPlan = [];
        $chartDataFact = [];
        $chartDataDebt = [];
        foreach ($models as $model) {
            $chartLabels[] = $model->getMonthName(true) . ', ' . $model->year;
            $chartDataPlan[] = floatval($model->price_plan_total);
            $chartDataFact[] = floatval($model->price_fact_total);
            $chartDataDebt[] = floatval($model->price_debt_total > 0 ? $model->price_debt_total : 0);
        }

        // informers
        $houseTotalsQuery = ViewHouseTotal::find()->select([
            new \yii\db\Expression('SUM(`flats_total`) AS `flats_total`'),
            new \yii\db\Expression('SUM(`flats_available`) AS `flats_available`'),
            new \yii\db\Expression('SUM(`flats_sold`) AS `flats_sold`'),
            new \yii\db\Expression('SUM(`square_total`) AS `square_total`'),
            new \yii\db\Expression('SUM(`square_available`) AS `square_available`'),
            new \yii\db\Expression('SUM(`square_sold`) AS `square_sold`'),
            new \yii\db\Expression('SUM(`price_total`) AS `price_total`'),
            new \yii\db\Expression('SUM(`price_available`) AS `price_available`'),
            new \yii\db\Expression('SUM(`price_sold`) AS `price_sold`'),
            new \yii\db\Expression('SUM(`price_sold_park`) AS `price_sold_park`'),
        ]);

        if ($idFilterArray) {
            $houseTotalsQuery->andFilterWhere(['in', 'id', $idFilterArray]);
        }

        $houseTotals = $houseTotalsQuery->asArray()->one();

        $flatsTotal = $houseTotals['flats_total'];
        $flatsTotalAvailable = $houseTotals['flats_available'];
        $flatsTotalSold = $houseTotals['flats_sold'];
        $squareTotal = round($houseTotals['square_total']);
        $priceTotal = round($houseTotals['price_sold'] + $houseTotals['price_sold_park']);
        $squareTotalAvailable = round($houseTotals['square_available']);
        $squareTotalSold = round($houseTotals['square_sold']);
        $priceTotalPlan = $countQuery->sum('price_plan_total');
        $priceTotalFact = $countQuery->sum('price_fact_total');

        $priceTotalRemain = $countQuery->sum('price_debt_total'); // $houseTotals['price_available'];

        return $this->render('index', [
                    'houseNames' => ArrayHelper::map(House::find()->select('name')->distinct()->asArray()->all(), 'name', 'name'),
                    'selectedHouseName' => $house_name,
                    'sectionsNames' => $house_name ? ArrayHelper::map(House::find()->select('section')->where(['like', 'name', $house_name])->distinct()->asArray()->all(), 'section', 'section') : [],
                    'sections' => $sections,
                    'models' => $models,
                    'houseId' => $house_id,
                    'houses' => $houses,
                    'pages' => $pages,
                    'chartLabels' => json_encode($chartLabels),
                    'chartDataPlan' => json_encode($chartDataPlan),
                    'chartDataFact' => json_encode($chartDataFact),
                    'chartDataDebt' => json_encode($chartDataDebt),
                    'flatsTotal' => $flatsTotal,
                    'flatsTotalAvailable' => $flatsTotalAvailable,
                    'flatsTotalSold' => $flatsTotalSold,
                    'squareTotal' => $squareTotal,
                    'priceTotal' => $priceTotal,
                    'squareTotalAvailable' => $squareTotalAvailable,
                    'squareTotalSold' => $squareTotalSold,
                    'priceTotalPlan' => $priceTotalPlan,
                    'priceTotalFact' => $priceTotalFact,
                    'priceTotalRemain' => $priceTotalRemain,
                    'startFactTotal' => $startFactTotal
        ]);
    }

    /**
     * Displays statistic apartment page.
     *
     * @return string
     */
    public function actionApartment() {
        $searchModel = new ViewTotalFlatSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $query = ViewTotalPlanFact::find();
        $query->select([
            'house_id',
            'year',
            'month',
            'year_month',
            new \yii\db\Expression('SUM(`price_plan_total`) AS `price_plan_total`'),
            new \yii\db\Expression('SUM(`price_fact_total`) AS `price_fact_total`'),
            new \yii\db\Expression('SUM(`price_saldo_total`) AS `price_saldo_total`'),
            new \yii\db\Expression('SUM(`price_debt_total`) AS `price_debt_total`')
        ]);
        $query->groupBy('year_month');

        $countQuery = clone $query;

        // informers
        $houseTotalsQuery = ViewHouseTotal::find()->select([
            new \yii\db\Expression('SUM(`flats_total`) AS `flats_total`'),
            new \yii\db\Expression('SUM(`flats_available`) AS `flats_available`'),
            new \yii\db\Expression('SUM(`flats_sold`) AS `flats_sold`'),
            new \yii\db\Expression('SUM(`square_total`) AS `square_total`'),
            new \yii\db\Expression('SUM(`square_available`) AS `square_available`'),
            new \yii\db\Expression('SUM(`square_sold`) AS `square_sold`'),
            new \yii\db\Expression('SUM(`price_total`) AS `price_total`'),
            new \yii\db\Expression('SUM(`price_available`) AS `price_available`'),
            new \yii\db\Expression('SUM(`price_sold`) AS `price_sold`'),
        ]);
        $houseTotals = $houseTotalsQuery->asArray()->one();

        $flatsTotal = $houseTotals['flats_total'];
        $flatsTotalAvailable = $houseTotals['flats_available'];
        $flatsTotalSold = $houseTotals['flats_sold'];
        $priceTotalPlan = $countQuery->sum('price_plan_total');
        $priceTotalFact = $countQuery->sum('price_fact_total');
        $priceTotalRemain = $countQuery->sum('price_debt_total'); // $houseTotals['price_available'];
        $priceTotalDebt = $priceTotalPlan - $priceTotalFact;

        return $this->render('apartment', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'flatsTotal' => $flatsTotal,
                    'flatsTotalAvailable' => $flatsTotalAvailable,
                    'flatsTotalSold' => $flatsTotalSold,
                    'priceTotalPlan' => $priceTotalPlan,
                    'priceTotalFact' => $priceTotalFact,
                    'priceTotalRemain' => $priceTotalRemain,
                    'priceTotalDebt' => $priceTotalDebt,
        ]);
    }

    /**
     * Displays statistic per month page.
     *
     * @param string $date_range
     * @param integer|null $house_id
     * @return string
     */
    public function actionMonth($date_range = '', $house_id = null, $house_name = null) {
        $ymdFrom = '';
        $ymdTo = '';

        $date = new \DateTime('now');
        $date->modify('last day of this month');

        if (!isset($date_range) || empty($date_range)) {
            $date_range = date('01.m.Y') . ' - ' . $date->format('d.m.Y');
        }
        if ($date_range) {
            $rangeParts = explode(' - ', $date_range);
            $dateFrom = $rangeParts[0];
            $dateTo = $rangeParts[1];
            $ymdFrom = date('Y-m-01', strtotime($dateFrom));
            $ymdTo = date('Y-m-t', strtotime($dateTo));
        }

        $sections = Yii::$app->request->get('sections');

        $idFilterArray = null;
        if ($house_name || $sections) {
            $filterArrayQuery = House::find()->select('id');
            if ($house_name) {
                $filterArrayQuery->andWhere(['name' => $house_name]);
            }
            if ($sections) {
                $filterArrayQuery->andWhere(['in', 'section', $sections]);
            }
            $idFilterArray = ArrayHelper::getColumn($filterArrayQuery->asArray()->all(), 'id');
        }

        $query = Flat::find()->joinWith('payments')->andWhere(['is not', 'payment.id', null]);
        if ($ymdFrom) {
            $query->onCondition(['>=', 'payment.pay_date', $ymdFrom]);
        }
        if ($ymdTo) {
            $query->onCondition(['<=', 'payment.pay_date', $ymdTo]);
        }

        //$query->andFilterWhere(['house_id' => $house_id]);
        if ($idFilterArray) {
            $query->andFilterWhere(['in', 'house_id', $idFilterArray]);
        }

        $query->distinct();

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $models = $query->offset($pages->offset)->limit($pages->limit)->all();

        // months list
        $months = [];
        $payDateQuery = Payment::find();
        $payDateQuery->andFilterWhere(['>=', 'pay_date', $ymdFrom]);
        $payDateQuery->andFilterWhere(['<=', 'pay_date', $ymdTo]);
        $firstPayDate = $payDateQuery->min('pay_date');
        $lastPayDate = $payDateQuery->max('pay_date');

        $current = strtotime($firstPayDate);
        $end = strtotime($lastPayDate);
        while ($current <= $end) {
            $next = date('Y-m', $current);
            $nextString = DateHelper::getMonthName(date('m', $current)) . ', ' . date('Y', $current);
            $current = strtotime($next . " +1 month");
            $months[$next] = $nextString;
        }

        // houses selector
        $houses = House::find()->all();

        return $this->render('per-month', [
                    'houseNames' => ArrayHelper::map(House::find()->select('name')->distinct()->asArray()->all(), 'name', 'name'),
                    'selectedHouseName' => $house_name,
                    'sectionsNames' => $house_name ? ArrayHelper::map(House::find()->select('section')->where(['like', 'name', $house_name])->distinct()->asArray()->all(), 'section', 'section') : [],
                    'sections' => $sections,
                    'models' => $models,
                    'months' => $months,
                    'houseId' => $house_id,
                    'houses' => $houses,
                    'pages' => $pages,
        ]);
    }

}
