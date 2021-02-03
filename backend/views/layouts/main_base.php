<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
//use app\assets\AppAsset;
use backend\assets\DashBoardAsset;
use backend\widgets\adminlte\LeftsideWidget;
use backend\models\feedback\Feedback;
use backend\models\order\Order;
use backend\models\order\OrderStatus;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<?php $this->beginBody() ?>
<div class="wrapper">
    <?= $this->render('header.php', ['baserUrl' => $baseUrl, 'title'=>$this->title, 'mini_title' => 'АП', 'feedbackCount' => Feedback::getFeedbackCount(0), 'newOrdersCount'=> Order::getOrdersCount(OrderStatus::ISNEW) ]) ?>
    <?= LeftsideWidget::widget(['route' => $route]); ?>
    <?= $this->render('content.php', ['content' => $content]) ?>
    <?= $this->render('footer.php', ['baseUrl' => $baseUrl]) ?>
    <?= $this->render('rightside.php', ['baseUrl' => $baseUrl]) ?>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
