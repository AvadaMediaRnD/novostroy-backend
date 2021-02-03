<?php

use common\helpers\PriceHelper;
use common\models\User;

/* @var $this yii\web\View */
/* @var $squareTotal integer */
/* @var $squareTotalAvailable integer */
/* @var $squareTotalSold integer */

?>

<div class="col-lg-4 col-md-6 col-xs-12">
    <div class="small-box novostroy-box bg-primary">
        <div class="inner">
            <p class="no-margin">Общая пл. помещений</p>
            <h3><?= $squareTotal ?> м<sup>2</sup></h3>
            <div class="amount">
                <div class="display-inline"><span><?= $squareTotalSold ?></span> м<sup>2</sup> продано</div>
                <div class="display-inline"><span><?= $squareTotalAvailable ?></span> м<sup>2</sup> остаток</div>
            </div>
        </div>
        <div class="icon">
            <i class="fa fa-key"></i>
        </div>
    </div>
</div>

