<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

?>

<!-- Modal -->
<div class="modal fade" id="modal<?= $model->id ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                'action' => ['settings/cash-currency', 'id' => $model->id],
                'id' => $model->id,
                /*
                  'fieldConfig' => [
                  'options' => [
                  'tag' => false,
                  ],
                  ],
                 */
            ]);
            ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Редактирование кассы</h4>
            </div>
            <div class="modal-body">
                <?= $form->field($model, 'name', ['options' => ['class' => 'form-group']])->textInput(['class' => 'form-control', 'id' => 'inputNameModal' . $model->id])->label($model->getAttributeLabel('name'), ['for' => 'inputNameModal' . $model->id]) ?>
                <?= $form->field($model, 'rate', ['options' => ['class' => 'form-group']])->textInput(['class' => 'form-control', 'readonly' => $state == 1 ? true : false, 'id' => 'inputRateModal' . $model->id])->label($model->getAttributeLabel('rate'), ['for' => 'inputRateModal' . $model->id]) ?>
                <?php // echo $form->field($model, 'is_default', ['template' => '<div class="checkbox">{input}</div>'])->checkbox()->label(false)  ?>
            </div>
            <div class="modal-footer text-center">
            <?= Html::button('Отмена', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
            <?= Html::submitButton('Cохранить', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<!-- Modal -->
