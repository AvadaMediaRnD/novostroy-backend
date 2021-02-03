<?php
use yii\widgets\Breadcrumbs;
use yii\widgets\AlertLte;
use yii\helpers\Html;

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1><?= $this->title ?></h1>
                    <?=
                    Breadcrumbs::widget([
            'tag' => 'ol',
            'homeLink'=>[
                'label' => 'Главная',
                'template' => "<li><a href='/'><i class='fa fa-home'></i>Главная</a></li>\n", // template for this link only
            ],
            'options' => ['class' => 'breadcrumb'],
            //'itemTemplate' => "<li><i>{link}</i></li>\n", // template for all links
            'links' => [
                [
                    'label' => 'Post Category',
                    'url' => ['post-category/view', 'id' => 10],
                    'template' => "<li><i class='fa fa-home'></i>{link}</li>\n", // template for this link only
                ],
            ],
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) ?>
    </section>

    <!-- Main content -->
    <section class="content">
        <?= \common\widgets\Alert::widget() ?>
                    <?= $content ?>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
