<?php

use common\helpers\PriceHelper;

/* @var $this yii\web\View */
/* @var $totalOut float */
/* @var $totalOutUah float */
/* @var $totalOutUsd float */
/* @var $totalOutEur float */

?>

<div class="col-lg-4 col-md-6 col-xs-12">
    <div class="small-box novostroy-box bg-green">
        <div class="inner">
            <p class="no-margin">Расходы:</p>
            <h3><?= PriceHelper::format($totalOut, false, true) ?></h3>
            <div class="amount">
                <div class="display-inline"><span>UAH:</span> <?= PriceHelper::format($totalOutUah, false) ?></div>
                <div class="display-inline"><span>USD:</span> <?= PriceHelper::format($totalOutUsd, false) ?></div>
                <div class="display-inline"><span>EUR:</span> <?= PriceHelper::format($totalOutEur, false) ?></div>
            </div>
        </div>
        <div class="icon">
            <i class="fa fa-users"></i>
        </div>
    </div>
</div>
