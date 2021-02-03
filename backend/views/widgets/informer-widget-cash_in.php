<?php

use common\helpers\PriceHelper;

/* @var $this yii\web\View */
/* @var $totalIn float */
/* @var $totalInUah float */
/* @var $totalInUsd float */
/* @var $totalInEur float */

?>

<div class="col-lg-4 col-md-6 col-xs-12">
    <div class="small-box novostroy-box bg-primary">
        <div class="inner">
            <p class="no-margin">Приходы:</p>
            <h3><?= PriceHelper::format($totalIn, false, true) ?></h3>
            <div class="amount">
                <div class="display-inline"><span>UAH:</span> <?= PriceHelper::format($totalInUah, false) ?></div>
                <div class="display-inline"><span>USD:</span> <?= PriceHelper::format($totalInUsd, false) ?></div>
                <div class="display-inline"><span>EUR:</span> <?= PriceHelper::format($totalInEur, false) ?></div>
            </div>
        </div>
        <div class="icon">
            <i class="fa fa-key"></i>
        </div>
    </div>
</div>
