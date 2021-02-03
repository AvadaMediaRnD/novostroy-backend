<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\House;
use kartik\select2\Select2;
use common\models\Flat;
use common\helpers\PriceHelper;
use common\models\Client;
use common\models\Agency;
use common\models\User;
use common\models\Payment;
use backend\models\PaymentSearch;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\date\DatePicker;
use common\models\SystemConfig;

/* @var $this yii\web\View */
/* @var $model \common\models\SystemConfig */

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body">
                <?php $form = ActiveForm::begin(); ?>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <?= $form->field($model, 'key')->textInput() ?>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <?= $form->field($model, 'value_raw')->textInput() ?>
                        </div>
                        <?php /* <div class="col-xs-12 col-sm-4">
                            <?= $form->field($model, 'type')->widget(Select2::class, [
                                'data' => SystemConfig::getTypeOptions(),
                                'language' => 'ru',
                                'theme' => Select2::THEME_DEFAULT,
                                'options' => [
                                    'placeholder' => 'Выберите...', 
                                    'class' => 'form-control',
                                ],
                                'pluginOptions' => [
                                    'minimumResultsForSearch' => -1, 
                                    'dropdownAutoWidth' => true,
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div> */ ?>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <?= $form->field($model, 'description')->textarea() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <a href="<?= Yii::$app->urlManager->createUrl(['/settings/variables']) ?>" class="btn btn-default">Отменить</a>
                            <button type="submit" class="btn btn-success">Сохранить</button>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

