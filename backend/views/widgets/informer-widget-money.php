<?php

use common\helpers\PriceHelper;

/* @var $this yii\web\View */
/* @var $priceTotalPlan float */
/* @var $priceTotalFact float */
/* @var $priceTotalRemain float */

?>

<div class="col-lg-4 col-md-6 col-xs-12">
    <div class="small-box novostroy-box bg-green">
        <div class="inner">
            <p class="no-margin">Деньги (факт + остаток)</p>
            <h3><?= PriceHelper::format($priceTotalPlan, false, true, ' ', '', '$') ?></h3>
            <div class="amount no-margin">
                <div class="display-inline"><label class="display-block">План:</label><span><?= PriceHelper::format($priceTotalPlan, false, true, ' ', '', '$') ?></span></div>
                <div class="display-inline"><label class="display-block">Факт:</label><span><?= PriceHelper::format($priceTotalFact, false, true, ' ', '', '$') ?></span></div>
                <div class="display-inline"><label class="display-block">Остаток:</label><span><?= PriceHelper::format($priceTotalRemain, false, true, ' ', '', '$') ?></span></div>
            </div>
        </div>
        <div class="icon">
            <i class="fa fa-users"></i>
        </div>
    </div>
</div>
