<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Agreement;
use common\models\AgreementTemplate;
use kartik\select2\Select2;
use common\models\SystemConfig;

/* @var $this yii\web\View */
/* @var $model \common\models\AgreementTemplate */
/* @var $modelForm backend\models\AgreementTemplateForm */
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'name' => 'compForm']]); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= $model->isNewRecord ? 'Новый шаблон' : 'Редактирование шаблона'; ?></h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <?= $form->field($modelForm, 'name')->textInput() ?>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <?= $form->field($modelForm, 'file')->fileInput() ?>
                    </div>
                </div>
                <?php /*
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">Содержимое:</h3>
                            </div>
                            <div class="box-body pad">
                                <?php echo $form->field($modelForm, 'content')->textarea(['id' => 'ckedit'])->label(false) ?>
                            </div>
                        </div>
                    </div>
                </div>
                */ ?>
                <div class="row">
                    <div class="col-xs-12 text-right">
                        <div class="form-group">
                            <a href="<?= Yii::$app->urlManager->createUrl(['/contracts/template-index']) ?>" class="btn btn-default margin-bottom-15">Отменить</a>
                            <button type="submit" class="btn btn-success margin-bottom-15">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php 

$varsSelectJsValue = '';
foreach (SystemConfig::getOptions(SystemConfig::TYPE_VARIABLE) as $k => $v) {
    $varsSelectJsValue .= "[ '$v', '$k' ],";
}

$this->registerJs(<<<JS
    $('#textBox').on('blur', function () {
        var val = $(this).html();
        $('#agreementtemplateform-content').val(val);
    });
    
    $('#textBox').on('mousedown', function() {
        caretOffset = getSelectionCharacterOffsetWithin( document.getElementById("textBox") );
        console.log("Selection offsets: " + caretOffset.start + ", " + caretOffset.end);
    });
    
    $('#variableSelect').on('change', function () {
        var key = $(this).val();
        console.log(key);
    
        var content = $('#textBox').html();
        contentUpdated = content.substr(0, caretOffset.start) + key + content.substr(caretOffset.start, content.length);
        console.log(contentUpdated);
    });
    
    
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
    
    var varsSelect = [ $varsSelectJsValue ];
    var ed = CKEDITOR.replace('ckedit', {
        customValues: { varsSelect: varsSelect },
    });
    
    CKEDITOR.plugins.add( 'abbr', {
        icons: 'abbr',
        init: function( editor ) {
            editor.addCommand( 'abbr', new CKEDITOR.dialogCommand( 'abbrDialog' ) );
            editor.ui.addButton( 'Abbr', {
                label: 'Вставить переменную',
                command: 'abbr',
                toolbar: 'insert'
            });

            CKEDITOR.dialog.add( 'abbrDialog', this.path + 'dialogs/abbr.js' );
        }
    });
    ed.addCommand( 'abbr', new CKEDITOR.dialogCommand( 'abbrDialog' ) );
    ed.ui.addButton( 'Abbr', {
        label: 'Insert Abbreviation',
        command: 'abbr',
        toolbar: 'insert'
    });
    CKEDITOR.config.extraPlugins = 'abbr';
    
    //
    CKEDITOR.config.font_names = ''
    
JS
);

