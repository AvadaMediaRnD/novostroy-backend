<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
//use app\assets\AppAsset;
use backend\assets\DashBoardAsset;
use backend\widgets\adminlte\LeftsideWidget;
use yii\helpers\Url;

//AppAsset::register($this);
$asset      = DashBoardAsset::register($this);
$baseUrl    = $asset->baseUrl;
$this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Url::to(['/img/favicon.ico'])]);
?>

<?= $this->render('main_base.php', ['baseUrl' => $baseUrl,'content' => $content,'route' => $this->context->route,'title'=>$this->title, 'mini_title' => 'АП']); ?>

