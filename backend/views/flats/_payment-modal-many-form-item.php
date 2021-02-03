<?php

use yii\helpers\Html;
use kartik\date\DatePicker;


/* @var $flatModel \common\models\Flat */
/* @var $model \common\models\Payment */
/* @var $k integer */

?>

<div class="row payment-many-item">
    <div class="col-xs-2">
        <div class="form-group field-inputPayNumberModal">
            <?= Html::activeTextInput($model, "[$k]pay_number", ['type' => 'number', 'min' => 1, 'step' => 1, 'class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group field-inputDateModal">
            <?= DatePicker::widget([
                'model' => $model,
                'attribute' => "[$k]pay_date",
                'id' => "paymentDate$k",
                'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                'removeButton' => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd.mm.yyyy'
                ],
                'options' => ['id' => "paymentDate$k", 'class' => 'form-control pay-date'],
            ]) ?>
        </div>
    </div>
    <div class="col-xs-4">
        <div class="form-group field-inputPricePlanModal">
            <?= Html::activeTextInput($model, "[$k]price_plan", ['class' => 'form-control pay-price_plan']) ?>
        </div>
    </div>
    <?php if ($k > 0) { ?>
    <div class="col-xs-2">
        <button type="button" class="btn btn-default delete-payment-many-item"><i class="fa fa-trash"></i></button>
    </div>
    <?php } ?>
</div>
