<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use backend\widgets\SelectPajaxPageSizeWidget;
use kartik\daterange\DateRangePicker;
use common\models\Agreement;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AgreementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Шаблоны договоров';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Договора'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <?php ActiveForm::begin(['action' => ['/contracts/create'], 'method' => 'get']) ?>
            <div class="box box-default contract-templates">
                <div class="box-body">
                    <?php foreach ($dataProvider->models as $template) { ?>
                        <div class="form-group">
                            <div>
                                <?= Html::radio('template_id', $template->is_default ? true : false, ['value' => $template->id, 'id' => 'template' . $template->id]) ?> <label for="template<?= $template->id ?>"><?= $template->name ?><?= $template->is_default ? ' (ОСНОВНОЙ)' : '' ?></label>
                            </div>
                            <div>
                                <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/template-copy', 'id' => $template->id]) ?>">Копировать шаблон</a>
                                <?php if (!$template->is_default) { ?>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/template-set-default', 'id' => $template->id]) ?>">Назначить шаблоном по умолчанию</a>
                                <?php } ?>
                                <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/template-delete', 'id' => $template->id]) ?>" onclick="if (!confirm('Удалить этот элемент?')) return false;">Удалить</a>
                                <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/template-update', 'id' => $template->id]) ?>">Редактировать</a>
                                <?php if ($template->file) { ?>
                                    <a target="_blank" href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl($template->file) ?>">Скачать шаблон</a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group margin-top-15">
                        <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/template-create']) ?>" class="btn btn-default margin-bottom-15">Создать шаблон</a>
                        <button type="submit" class="btn btn-default margin-bottom-15">Выбрать</button>
                    </div>
                </div>
            </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
