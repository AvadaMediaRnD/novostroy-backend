<?php

use common\helpers\PriceHelper;

/* @var $this yii\web\View */
/* @var $totalBalance float */
/* @var $totalBalanceUah float */
/* @var $totalBalanceUsd float */
/* @var $totalBalanceEur float */

?>

<div class="col-lg-4 col-md-6 col-xs-12">
    <div class="small-box novostroy-box bg-primary">
        <div class="inner">
            <p class="no-margin">Фактическое состояние Кассы:</p>
            <h3><?= PriceHelper::format($totalBalance, false, true) ?></h3>
            <div class="amount">
                <div class="display-inline"><span>UAH:</span> <?= PriceHelper::format($totalBalanceUah, false) ?></div>
                <div class="display-inline"><span>USD:</span> <?= PriceHelper::format($totalBalanceUsd, false) ?></div>
                <div class="display-inline"><span>EUR:</span> <?= PriceHelper::format($totalBalanceEur, false) ?></div>
            </div>
        </div>
        <div class="icon">
            <i class="fa fa-building"></i>
        </div>
    </div>
</div>
