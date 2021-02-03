<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html,
    yii\helpers\Url;
//use app\assets\AppAsset;
use backend\assets\AdminLteAsset;
use backend\widgets\adminlte\LeftsideWidget;

//AppAsset::register($this);
$asset = AdminLteAsset::register($this);
$baseUrl = $asset->baseUrl;
$this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Url::to(['/admin-lte/dist/img/favicon.png'])]);
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
    <body class="hold-transition skin-black-light sidebar-mini fixed">
        <?php $this->beginBody() ?>

        <div class="wrapper">
            <?= $this->render('header', ['baserUrl' => $baseUrl, 'title' => $this->title, 'mini_title' => 'АП', 'feedbackCount' => 0, 'newOrdersCount' => 0]) ?>
            <?= LeftsideWidget::widget(['route' => $this->context->route]); ?>
            <?= $this->render('content', ['content' => $content]) ?>
            <?= $this->render('footer', ['baserUrl' => $baseUrl]) ?>
            <?= $this->render('rightside', ['baserUrl' => $baseUrl]) ?>
        </div>

        <!--footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; My Company <?//= date('Y') ?></p>
        
                <p class="pull-right"><?//= Yii::powered() ?></p>
            </div>
        </footer-->

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
