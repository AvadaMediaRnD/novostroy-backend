<?php

use yii\helpers\Html,
    yii\helpers\Url;
use common\models\Cashbox;
use backend\widgets\adminlte\AlertSaldo;
?>

<header class="main-header">
    <!-- Logo -->
    <a href="<?= Url::home() ?>" class="logo">
        <span class="logo-mini">
            <img src="<?= Url::to('/admin-lte/dist/img/logo_min.png') ?>" class="img-responsive" alt="logo">
        </span>
        <span class="logo-lg">
            <img src="<?= Url::to('/admin-lte/dist/img/logo_hor.png') ?>" class="img-responsive" alt="">
        </span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Notifications Dropdown Menu -->
                <?= AlertSaldo::widget()  ?>
                <!-- Notifications -->
                <li class="inner-course">
                    <div>
                        <div>Внутренний курс валют:</div>
                        <div>
                            <div><span><?= Cashbox::getCashboxRateByCurrency('EUR'); ?></span> UAH/EUR</div>
                            <div><span><?= Cashbox::getCashboxRateByCurrency('USD'); ?></span> UAH/USD</div>
                        </div>
                    </div>
                </li>
                <!-- User Account -->
                <li class="dropdown user user-menu">
                    <?php $userImage = (isset(Yii::$app->user->identity->image) && !empty(trim(Yii::$app->user->identity->image))) ? Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => Yii::$app->user->identity->image, 'w' => 160, 'h' => 160, 'fit' => 'crop']) : '/image/noavatar-160x160.png'; ?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $userImage ?>" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?= Yii::$app->user->identity->getRoleLabel() ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= $userImage ?>" class="img-circle" alt="User Image">
                            <p><?= Yii::$app->user->identity->getRoleLabel() ?></p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?= Yii::$app->urlManager->createUrl(['/settings/user-update', 'id' => Yii::$app->user->id]) ?>" class="btn btn-default btn-flat">Профиль</a>
                            </div>
                            <div class="pull-right">
                                <?=
                                Html::a(
                                        'Выход',
                                        ['/site/logout'],
                                        ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                )
                                ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>


