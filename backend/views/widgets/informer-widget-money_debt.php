<?php

use common\helpers\PriceHelper;

/* @var $this yii\web\View */
/* @var $priceTotalDebt float */

?>

<div class="col-lg-4 col-md-6 col-xs-12">
    <div class="small-box novostroy-box bg-primary">
        <div class="inner">
            <p class="no-margin">Задолженность:</p>
            <h3><?= PriceHelper::format($priceTotalDebt, false, true, ' ', '', '$') ?></h3>
        </div>
        <div class="icon">
            <i class="fa fa-key"></i>
        </div>
    </div>
</div>
