<?php

use yii\helpers\Html,
    yii\helpers\Url;
?>

<li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell-o"></i>
        <span class="label label-warning"><?= count($notifications) ?></span>
    </a>
    <ul class="dropdown-menu">
        <li class="header">У вас <?= count($notifications) ?> уведомлений</li>
        <li>
            <ul class="menu">
                <?php foreach ($notifications as $notification) { ?>
                <li>
                    <a href="<?= Url::to(['/flats/update', 'id' => $notification->id]) ?>">
                        <i class="fa fa-warning text-yellow"></i>
                        <span class="text-red">
                            <strong><?= Yii::$app->formatter->asDecimal($notification->price_saldo_total, 2) . '$' ?></strong>
                        </span>
                        <i class="fa fa-angle-double-right text-center"></i><?= Yii::t('app', 'Помещение № ') . $notification->number ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </li>
    </ul>
</li>







