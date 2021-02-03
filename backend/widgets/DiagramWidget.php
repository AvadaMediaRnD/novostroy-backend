<?php

namespace backend\widgets;

use yii\base\Widget;
use common\models\Invoice;
use common\models\Flat;
use common\models\Payment;
use backend\models\FlatSearch;
use common\models\ViewTotalPlanFact;
use Yii;

class DiagramWidget extends Widget
{
    const W_FLATS = 'flats';
    const W_SQUARE = 'square';
    const W_MONEY = 'money';
    const W_DEBT = 'money_debt';
    const W_CASH_STATE = 'cash_state';
    const W_CASH_IN = 'cash_in';
    const W_CASH_OUT = 'cash_out';
    
    public $items = [];
    public $roles = [];
    public $notRoles = [];
    public $filter = [];
    
    public function run()
    {
        if ($this->roles && !in_array(Yii::$app->user->identity->role, $this->roles)) {
            return false;
        }
        
        if ($this->notRoles && in_array(Yii::$app->user->identity->role, $this->notRoles)) {
            return false;
        }
        
        if (!$this->items) {
            return false;
        }
        
        $content = '';
        foreach ($this->items as $item) {
            $viewFile = '@backend/views/widgets/informer-widget-' . $item;
            if (!file_exists(Yii::getAlias($viewFile . '.php'))) {
                continue;
            }

            $content .= $this->render($viewFile, $this->getViewParams($item));
        }
        
        return $content;
    }
    
    /**
     * Return params for widget defined in $widgetItem
     * @param string $widgetItem
     * @return array
     */
    protected function getViewParams(string $widgetItem): array
    {
        switch ($widgetItem) {
            case self::W_FLATS: {
                return $this->getParamsFlats();
            }
            case self::W_SQUARE: {
                return $this->getParamsSquare();
            }
            case self::W_MONEY: {
                return $this->getParamsMoney();
            }
            case self::W_DEBT: {
                return $this->getParamsMoneyDebt();
            }
            case self::W_CASH_STATE: {
                return $this->getParamsCashState();
            }
            case self::W_CASH_IN: {
                return $this->getParamsCashIn();
            }
            case self::W_CASH_OUT: {
                return $this->getParamsCashOut();
            }
            default: {
                return [];
            }
        }
    }
    
    protected function getParamsFlats(): array
    {
        $filter = $this->filter;
        if (isset($filter['status'])) {
            unset($filter['status']);
        }
        $searchModel = new FlatSearch();
        $dataProvider = $searchModel->search(['FlatSearch' => $filter]);
        
        $queryAvailable = clone $dataProvider->query;
        $querySold = clone $dataProvider->query;
        
        $queryAvailable->andWhere(['in', 'flat.status', [Flat::STATUS_AVAILABLE, Flat::STATUS_BOOKED, Flat::STATUS_RESERVED, Flat::STATUS_READY_BUY]]);
        $querySold->andWhere(['in', 'flat.status', [Flat::STATUS_SOLD, Flat::STATUS_NOT_SELL, Flat::STATUS_UNAVAILABLE]]);
        
        $flatsTotal = $dataProvider->query->count();
        $flatsTotalAvailable = $queryAvailable->count();
        $flatsTotalSold = $querySold->count();
        
        return [
            'flatsTotal' => $flatsTotal,
            'flatsTotalAvailable' => $flatsTotalAvailable,
            'flatsTotalSold' => $flatsTotalSold,
        ];
    }
    
    protected function getParamsSquare(): array
    {
        $filter = $this->filter;
        if (isset($filter['status'])) {
            unset($filter['status']);
        }
        
        $searchModel = new FlatSearch();
        $dataProvider = $searchModel->search(['FlatSearch' => $filter]);
        
        $queryAvailable = clone $dataProvider->query;
        $querySold = clone $dataProvider->query;
        
        $queryAvailable->andWhere(['in', 'flat.status', [Flat::STATUS_AVAILABLE, Flat::STATUS_BOOKED, Flat::STATUS_RESERVED, Flat::STATUS_READY_BUY]]);
        $querySold->andWhere(['in', 'flat.status', [Flat::STATUS_SOLD, Flat::STATUS_NOT_SELL, Flat::STATUS_UNAVAILABLE]]);
        
        $squareTotal = round($dataProvider->query->sum('flat.square'));
        $squareTotalAvailable = round($queryAvailable->sum('flat.square'));
        $squareTotalSold = round($querySold->sum('flat.square'));
        
        return [
            'squareTotal' => $squareTotal,
            'squareTotalAvailable' => $squareTotalAvailable,
            'squareTotalSold' => $squareTotalSold,
        ];
    }
    
    protected function getParamsMoney(): array
    {
        $filter = $this->filter;
        if (isset($filter['status'])) {
            unset($filter['status']);
        }
        
        $searchModel = new FlatSearch();
        $dataProvider = $searchModel->search(['FlatSearch' => $filter]);
        
        $dataProviderFlat = clone $dataProvider;
        $dataProviderNotFlat = clone $dataProvider;
        
        
        $notFlatType = [Flat::TYPE_CAR_PLACE,Flat::TYPE_PARKING];
        $pricePlanFlat = $dataProviderFlat->query->andFilterWhere(['not in','unit_type', $notFlatType])->sum('price_sell_m * square');
        
        $pricePlanNotFlat = $dataProviderNotFlat->query->andFilterWhere(['in','unit_type', $notFlatType])->sum('price_sell_m');
        $priceTotalPlan = $pricePlanFlat + $pricePlanNotFlat;

        $dataProvider->query->joinWith('payments');
        $pricePayTotalPlan = $dataProvider->query->sum('payment.price_plan');
        
        $priceTotalFact = $dataProvider->query->sum('payment.price_fact');
        //$priceTotalRemain = round($dataProvider->query->sum('(payment.price_plan - payment.price_fact)'));
        
        $priceTotalRemain = $priceTotalPlan - $priceTotalFact;
        
        return [
            'priceTotalPlan' => round($priceTotalPlan),
            'priceTotalFact' => round($priceTotalFact),
            'priceTotalRemain' => round($priceTotalRemain),
            'pricePayTotalPlan' => round($pricePayTotalPlan),
        ];
    }
    
    protected function getParamsMoneyDebt(): array
    {
        $query = ViewTotalPlanFact::find();
        $priceTotalDebt = $query->sum('price_plan_total - price_fact_total');

        return [
            'priceTotalDebt' => $priceTotalDebt,
        ];
    }
    
    protected function getParamsCashState(): array
    {
        $dateFrom = $this->filter['dateFrom'];
        $dateTo = $this->filter['dateTo'];
        $totalBalance = Invoice::getTotalBalance(null, $dateFrom, $dateTo);
        $totalBalanceUah = Invoice::getTotalBalance(3, $dateFrom, $dateTo);
        $totalBalanceUsd = Invoice::getTotalBalance(1, $dateFrom, $dateTo);
        $totalBalanceEur = Invoice::getTotalBalance(2, $dateFrom, $dateTo);
        
        return [
            'totalBalance' => $totalBalance,
            'totalBalanceUah' => $totalBalanceUah,
            'totalBalanceUsd' => $totalBalanceUsd,
            'totalBalanceEur' => $totalBalanceEur,
        ];
    }
    
    protected function getParamsCashIn(): array
    {
        $dateFrom = $this->filter['dateFrom'];
        $dateTo = $this->filter['dateTo'];
        $totalIn = Invoice::getTotalIn(null, $dateFrom, $dateTo);
        $totalInUah = Invoice::getTotalIn(3, $dateFrom, $dateTo);
        $totalInUsd = Invoice::getTotalIn(1, $dateFrom, $dateTo);
        $totalInEur = Invoice::getTotalIn(2, $dateFrom, $dateTo);
        
        return [
            'totalIn' => $totalIn,
            'totalInUah' => $totalInUah,
            'totalInUsd' => $totalInUsd,
            'totalInEur' => $totalInEur,
        ];
    }
    
    protected function getParamsCashOut(): array
    {
        $dateFrom = $this->filter['dateFrom'];
        $dateTo = $this->filter['dateTo'];
        $totalOut = Invoice::getTotalOut(null, $dateFrom, $dateTo);
        $totalOutUah = Invoice::getTotalOut(3, $dateFrom, $dateTo);
        $totalOutUsd = Invoice::getTotalOut(1, $dateFrom, $dateTo);
        $totalOutEur = Invoice::getTotalOut(2, $dateFrom, $dateTo);
        
        return [
            'totalOut' => $totalOut,
            'totalOutUah' => $totalOutUah,
            'totalOutUsd' => $totalOutUsd,
            'totalOutEur' => $totalOutEur,
        ];
    }
}