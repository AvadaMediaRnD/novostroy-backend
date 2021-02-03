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
use kartik\date\DatePicker;
use common\models\Agreement;
use common\models\AgreementTemplate;

/* @var $this yii\web\View */
/* @var $model \common\models\Agreement */

?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">

            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
                        
                        <div class="form-group field-agreement-id">
                            <textarea name="content" id="ckedit" rows="50"><?= $model->readFileContent() ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-right">
                        <div class="form-group">
                            <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/update', 'id' => $model->id]) ?>" class="btn btn-default margin-bottom-15">Отменить</a>
                            <button type="submit" class="btn btn-success margin-bottom-15">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->registerJs(<<<JS
    
    CKEDITOR.config.language = 'ru';
    CKEDITOR.config.toolbarGroups = [
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
            { name: 'links', groups: [ 'links' ] },
            { name: 'insert', groups: [ 'insert' ] },
            '/',
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
            { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
            { name: 'forms', groups: [ 'forms' ] },
            { name: 'styles', groups: [ 'styles' ] },
            { name: 'colors', groups: [ 'colors' ] },
            { name: 'tools', groups: [ 'tools' ] },
            { name: 'others', groups: [ 'others' ] },
            { name: 'about', groups: [ 'about' ] }
    ];
    CKEDITOR.config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,BidiRtl,BidiLtr,Language,Link,Unlink,Anchor,Image,Flash,Smiley,Iframe,Maximize,ShowBlocks,About';
    CKEDITOR.config.height = '800px';
    
    var ed = CKEDITOR.replace('ckedit');
JS
);
