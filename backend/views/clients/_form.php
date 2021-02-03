<?php

use yii\helpers\Html,
    yii\widgets\ActiveForm,
    yii\grid\GridView,
    yii\widgets\Pjax;
use kartik\date\DatePicker;
use common\helpers\PriceHelper,
    common\models\Payment,
    common\models\Invoice;
use backend\models\FlatSearch;

/* @var $this yii\web\View */
/* @var $model \common\models\Client */

$searchModel = new FlatSearch();
$searchModel->client_id = $model->id;
$searchModel->searchStatus = [\common\models\Flat::STATUS_SOLD, \common\models\Flat::STATUS_RESERVED];
$agreementDataProvider = $searchModel->search(Yii::$app->request->queryParams);
?>

<?php $form = ActiveForm::begin(); ?>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Описание</a></li>
                        <?php if ($model->isNewRecord) { ?>
                            <li class="disabled"><a href="#!" data-toggle="tooltip" data-placement="top" title="Для просмотра договоров сохраните покупателя" aria-expanded="false">Договора</a></li>
                        <?php } else { ?>
                            <li><a href="#tab_2" data-toggle="tab" aria-expanded="false">Покупки (<?= $agreementDataProvider->getCount() ?>)</a></li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active clearfix" id="tab_1">

                            <div class="row">
                                <div class="col-xs-12">
                                    <h4 class="box-title" style="margin-bottom:15px;">Реквизиты покупателя:</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($model, 'firstname')->textInput() ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($model, 'lastname')->textInput() ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($model, 'middlename')->textInput() ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($model, 'address')->textInput() ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?=
                                    $form->field($model, 'birthdate')->widget(DatePicker::className(), [
                                        'type' => DatePicker::TYPE_INPUT,
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd.mm.yyyy'
                                        ]
                                    ])
                                    ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($model, 'inn')->textInput() ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4 class="box-title" style="margin-bottom:15px;">Паспортные данные:</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($model, 'passport_series')->textInput() ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($model, 'passport_number')->textInput() ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($model, 'passport_from')->textInput() ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4 class="box-title" style="margin-bottom:15px;">Контакты:</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($model, 'phone')->textInput() ?>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <?= $form->field($model, 'email')->textInput() ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="box">
                                        <div class="box-header">
                                            <h3 class="box-title">Примечание:</h3>
                                            <!-- tools box -->
                                            <!--                                                <div class="pull-right box-tools">
                                                                                                <button type="button" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                                                                                                    <i class="fa fa-minus"></i></button>
                                                                                                <button type="button" class="btn btn-default btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                                                                                                    <i class="fa fa-times"></i></button>
                                                                                            </div>-->
                                            <!-- /. tools -->
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body pad">

                                            <?= $form->field($model, 'description', ['enableClientValidation' => false])->textarea(['rows' => 8, 'class' => 'compose-textarea editor-init form-control'])->label(false) ?>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane clearfix" id="tab_2">
                            <?php if (!$model->isNewRecord) { ?>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="box">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Покупки</h3>
                                            </div>

                                            <?php Pjax::begin(['id' => 'agreements']) ?>
                                            <?php
                                            echo GridView::widget([
                                                'dataProvider' => $agreementDataProvider,
                                                'filterModel' => null,
                                                'showFooter' => true,
                                                'tableOptions' => ['class' => 'table table-bordered table-hover table-striped table-nowrap'],
                                                'layout' => "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                                                'pager' => [
                                                    'class' => 'yii\widgets\LinkPager',
                                                    'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                                                ],
                                                'footerRowOptions' => ['style' => 'font-weight: bold'],
                                                'columns' => [
                                                    [
                                                        'attribute' => 'house',
                                                        'label' => 'Дом (секция)',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->house ? $model->house->getNameSection() : null;
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'number',
                                                        'label' => '№ кв.',
                                                        'enableSorting' => false,
                                                        'format' => 'html',
                                                        'value' => function ($model) {
                                                            if ($model->id) {
                                                                return Html::a($model->number, ['/flats/update', 'id' => $model->id]);
                                                            }
                                                            return $model->number;
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'status',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->getStatusLabel();
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'square',
                                                        'label' => 'Площадь (м<sup>2</sup>)',
                                                        'encodeLabel' => false,
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->square;
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'price',
                                                        'label' => 'Цена (у.е.)',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return PriceHelper::format($model->price, false);
                                                        }
                                                    ],
                                                    [
                                                        'label' => 'Цена продажи (у.е.)',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            $sum = (isset($model->square) && !empty($model->square)) ? PriceHelper::format($model->price_sell_m * $model->square, false) : PriceHelper::format($model->price_sell_m, false);

                                                            return $sum;
                                                        }
                                                    ],
                                                    [
                                                        'label' => 'План (у.е.)',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return PriceHelper::format(Payment::find()->joinWith('invoices', false)->where(['payment.flat_id' => $model->id])->sum('payment.price_plan'), false);
                                                        }
                                                    ],
                                                    [
                                                        'label' => 'Факт (у.е.)',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return PriceHelper::format(Payment::find()->joinWith('invoices', false)->where(['payment.flat_id' => $model->id])->andWhere(['invoice.status' => Invoice::STATUS_COMPLETE])->sum('payment.price_fact'), false);
                                                        }
                                                    ],
                                                    [
                                                        'label' => 'Остаток (у.е.)',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return PriceHelper::format(Payment::find()->joinWith('invoices', false)->where(['payment.flat_id' => $model->id])->sum('payment.price_plan') - Payment::find()->joinWith('invoices', false)->where(['payment.flat_id' => $model->id])->andWhere(['invoice.status' => Invoice::STATUS_COMPLETE])->sum('payment.price_fact'), false);
                                                        }
                                                    ],
                                                    [
                                                        'label' => 'Задолженность (у.е.)',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return Yii::$app->formatter->asDecimal(Payment::find()->joinWith('invoices', false)->where(['payment.flat_id' => $model->id])->sum('payment.price_saldo') * -1, 2);
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'agency_id',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->agency->name ?? '';
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'user_id',
                                                        'label' => 'Менеджер',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->client->user->fullname ?? '';
                                                        }
                                                    ],
                                                ],
                                            ]);
                                            ?>
                                            <?php Pjax::end() ?>

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <div class="form-group">
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/clients/index']) ?>" class="btn btn-default">Отменить</a>
                                    <button type="submit" class="btn btn-success">Сохранить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$this->registerJs(<<<JS
    $(function () {
        //Add text editor
        function addTextEditor() {
            $("textarea.compose-textarea.editor-init").wysihtml5({
                locale: 'ru-RU',
                toolbar: {
                    "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
                    "emphasis": true, //Italics, bold, etc. Default true
                    "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                    "html": false, //Button which allows you to edit the generated HTML. Default false
                    "link": false, //Button to insert a link. Default true
                    "image": false, //Button to insert an image. Default true,
                    "color": false, //Button to change color of font
                    "blockquote": false, //Blockquote
                    "fa": true,
                    "size": 'none' //default: none, other options are xs, sm, lg
                }
            }).removeClass('editor-init');
        }
        
        addTextEditor();
    
    });
    
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
JS
);
