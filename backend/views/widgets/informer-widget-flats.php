<?php


/* @var $this yii\web\View */
/* @var $flatsTotal integer */
/* @var $flatsTotalAvailable integer */
/* @var $flatsTotalSold integer */

?>

<div class="col-lg-4 col-md-6 col-xs-12">
    <div class="small-box novostroy-box bg-primary">
        <div class="inner">
            <p class="no-margin">Кол-во помещений:</p>
            <h3><?= $flatsTotal ?></h3>
            <div class="amount">
                <div class="display-inline"><span><?= $flatsTotalSold ?></span> продано</div>
                <div class="display-inline"><span><?= $flatsTotalAvailable ?></span> остаток</div>
            </div>
        </div>
        <div class="icon">
            <i class="fa fa-building"></i>
        </div>
    </div>
</div>

