<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\select2\Select2;
use common\models\Flat;
use common\helpers\PriceHelper;
use common\models\Agency;
use common\models\Payment;
use common\models\Rieltor;
use common\models\Invoice;
use common\models\Article;
use backend\models\RieltorSearch;
use backend\models\InvoiceSearch;
use backend\models\FlatSearch;

/* @var $this yii\web\View */
/* @var $model \common\models\Agency */

$agreementSearchModel = new FlatSearch();
$agreementSearchModel->agency_id = $model->id;
$agreementSearchModel->searchStatus = [Flat::STATUS_SOLD, Flat::STATUS_RESERVED];
$agreementDataProvider = $agreementSearchModel->search(Yii::$app->request->queryParams);

$rieltorSearchModel = new RieltorSearch();
$rieltorSearchModel->agency_id = $model->id;
$rieltorDataProvider = $rieltorSearchModel->search(Yii::$app->request->queryParams);

$invoiceSearchModel = new InvoiceSearch();
$invoiceSearchModel->searchAgencyIdWithRieltors = $model->id;
$invoiceDataProvider = $invoiceSearchModel->search(Yii::$app->request->queryParams);
$invoiceDataProvider->query
        ->andWhere(['type' => Invoice::TYPE_OUTCOME])
        ->andWhere(['article_id' => Article::getIdCommissionAgency()]);
$invoicePriceSum = Invoice::find()->where(['agency_id' => $model->id])
        ->andWhere(['type' => Invoice::TYPE_OUTCOME])
        ->andWhere(['article_id' => Article::getIdCommissionAgency()])
        ->sum('price')
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
                            <li class="disabled"><a href="#!" data-toggle="tooltip" data-placement="top" title="Для просмотра договоров сохраните агентство" aria-expanded="false">Договора</a></li>
                            <li class="disabled"><a href="#!" data-toggle="tooltip" data-placement="top" title="Для просмотра комиссионных сохраните агентство" aria-expanded="false">Комиссионные</a></li>
                        <?php } else { ?>
                            <li><a href="#tab_2" data-toggle="tab" aria-expanded="false">Продажи (<?= $agreementDataProvider->getCount() ?>)</a></li>
                            <li><a href="#tab_3" data-toggle="tab" aria-expanded="false">Комиссионные (<?= $invoiceDataProvider->query->count() ?>)</a></li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active clearfix" id="tab_1">
                            <div class="row">
                                <div class="col-xs-12 col-md-8 col-lg-6">
                                    <?= $form->field($model, 'name')->textInput() ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-8 col-lg-6">
                                    <?= $form->field($model, 'phone')->textInput() ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-8 col-lg-6">
                                    <?= $form->field($model, 'email')->textInput() ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-md-8 col-lg-6">
                                    <?=
                                    $form->field($model, 'status')->widget(Select2::class, [
                                        'data' => Agency::getStatusOptions(),
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
                                    ])
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="box">
                                        <div class="box-header">
                                            <h3 class="box-title">Описание:</h3>
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
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="box">
                                        <?php if ($model->isNewRecord) { ?>
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Риелтора:</h3>
                                            </div>
                                            <div class="box-body">
                                                <p>Для добавления риелторов сохраните агентство</p>
                                            </div>
                                        <?php } else { ?>
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Риелтора:</h3>
                                                <div class="box-tools">
                                                    <a href="#!" class="btn btn-default btn-sm" data-toggle="modal" data-target="#modal-rieltor">
                                                        <span class="hidden-xs">Добавить</span><i class="fa fa-money visible-xs" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </div>

                                            <?php Pjax::begin(['id' => 'rieltors']) ?>
                                            <?php
                                            echo GridView::widget([
                                                'dataProvider' => $rieltorDataProvider,
                                                'filterModel' => null,
                                                'showFooter' => true,
                                                'tableOptions' => ['class' => 'table table-bordered table-hover table-striped table-nowrap'],
                                                'layout' => "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                                                'pager' => [
                                                    'class' => 'yii\widgets\LinkPager',
                                                    'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                                                ],
                                                'columns' => [
                                                    [
                                                        'attribute' => 'searchFullname',
                                                        'label' => 'ФИО',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->fullname;
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'phone',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->phone . '';
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'email',
                                                        'enableSorting' => false,
                                                        'format' => 'email',
                                                        'value' => function ($model) {
                                                            return $model->email . '';
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'is_director',
                                                        'enableSorting' => false,
                                                        'format' => 'raw',
                                                        'value' => function ($model) {
                                                            return Html::checkbox('is_director' . $model->id, boolval($model->is_director), ['disabled' => true]);
                                                        }
                                                    ],
                                                    [
                                                        'class' => 'yii\grid\ActionColumn',
                                                        'template' => '<div class="btn-group pull-right">{update} {delete}</div>',
                                                        'headerOptions' => ['style' => 'width: 120px; min-width: 120px'],
                                                        'buttons' => [
                                                            'update' => function ($url, $model, $key) {
                                                                return Html::a('<i class="fa fa-pencil"></i>',
                                                                                '#!',
                                                                                ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'modal', 'data-target' => '#modal-rieltor', 'data-rieltor_id' => $model->id]
                                                                );
                                                            },
                                                            'delete' => function ($url, $model, $key) {
                                                                return Html::a('<i class="fa fa-trash"></i>',
                                                                                '#!',
                                                                                ['class' => 'btn btn-default btn-sm delete-rieltor', 'data-toggle' => 'tooltip', 'data-rieltor_id' => $model->id]
                                                                );
                                                            },
                                                        ]
                                                    ],
                                                ],
                                            ]);
                                            ?>
                                            <?php Pjax::end() ?>

                                        <?php } ?>
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
                                                <h3 class="box-title">Продажи</h3>
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
                                                            $price = (isset($model->square) && !empty($model->square)) ? PriceHelper::format($model->price_sell_m * $model->square, false) : PriceHelper::format($model->price_sell_m, false);
                                                            return $price;
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
                                                            return Yii::$app->formatter->asDecimal(floatval(Payment::find()->joinWith('invoices', false)->where(['payment.flat_id' => $model->id])->andWhere(['invoice.status' => Invoice::STATUS_COMPLETE])->sum('payment.price_saldo')) * -1,2);
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'client_id',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->client->fullname ?? '';
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
                        <div class="tab-pane clearfix" id="tab_3">
                            <?php if (!$model->isNewRecord) { ?>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="box">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Комиссионные</h3>
                                            </div>

                                            <?php Pjax::begin(['id' => 'invoices']) ?>
                                            <?php
                                            echo GridView::widget([
                                                'dataProvider' => $invoiceDataProvider,
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
                                                        'attribute' => 'searchNumber',
                                                        'label' => '№ кв.',
                                                        'enableSorting' => false,
                                                        'format' => 'html',
                                                        'value' => function ($model) {
                                                            if ($model->flat_id) {
                                                                return Html::a($model->flat['number'], ['/flats/update', 'id' => $model->flat_id]);
                                                            }
                                                            return $model->flat['number'];
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'searchHouse',
                                                        'label' => 'Дом (секция)',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->flat['house'] ? $model->flat['house']->getNameSection() : null;
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'searchSquare',
                                                        'label' => 'Площадь (м<sup>2</sup>)',
                                                        'encodeLabel' => false,
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->flat['square'];
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'searchFlatPrice',
                                                        'label' => 'Стоимость (у.е.)',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return PriceHelper::format($model->flat['price'], false);
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
                                                        'attribute' => 'searchCommission',
                                                        'label' => 'Комиссия',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->flat['commission_agency'] . ($model->flat['commission_agency_type'] == Flat::COMMISSION_TYPE_PERCENT ? '%' : '');
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'searchPrice',
                                                        'label' => 'Сумма',
                                                        'enableSorting' => false,
                                                        'footer' => PriceHelper::format($invoicePriceSum),
                                                        'value' => function ($model) {
                                                            return PriceHelper::format($model->price);
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'uid_date',
                                                        'label' => 'Дата оплаты',
                                                        'enableSorting' => false,
                                                        'value' => function ($model) {
                                                            return $model->getUidDate();
                                                        }
                                                    ],
                                                    [
                                                        'class' => 'yii\grid\ActionColumn',
                                                        'template' => '<div class="btn-group pull-right">{update} {delete}</div>',
                                                        'headerOptions' => ['style' => 'width: 120px; min-width: 120px'],
                                                        'buttons' => [
                                                            'update' => function ($url, $model, $key) {
                                                                return Html::a('<i class="fa fa-pencil"></i>',
                                                                                ['/payments/update', 'id' => $model->id],
                                                                                ['class' => 'btn btn-default btn-sm']
                                                                );
                                                            },
                                                            'delete' => function ($url, $model, $key) {
                                                                return Html::a('<i class="fa fa-trash"></i>',
                                                                                '#!',
                                                                                ['class' => 'btn btn-default btn-sm delete-invoice', 'data-toggle' => 'tooltip', 'data-invoice_id' => $model->id]
                                                                );
                                                            },
                                                        ]
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
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/agency/index']) ?>" class="btn btn-default">Отменить</a>
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
    $(document).ready(function() {
        $('#pjax-rieltor-modal').on('pjax:end', function() {
            console.log('form submitted');
            $('#modal-rieltor').modal('hide');
            $('body').removeClass('modal-open').css('padding-right', '');
            $.pjax.reload({container: '#rieltors'}); //Reload GridView
        });
    });
JS
);
?>
<?php Pjax::begin(['id' => 'pjax-rieltor-modal', 'enablePushState' => false]) ?>
<!-- Modal -->
<div class="modal fade" id="modal-rieltor" tabindex="-1" role="dialog" data-backdrop="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?=
            $this->render('_rieltor-modal-form', [
                'model' => new Rieltor(),
                'agencyModel' => $model,
            ]);
            ?>
        </div>
    </div>
</div>
<!-- Modal -->
<?php Pjax::end() ?>

<?php
$urlAjaxGetRieltorForm = Yii::$app->urlManager->createUrl(['/agency/ajax-get-rieltor-form', 'agency_id' => $model->id, 'rieltor_id' => '']);
$urlAjaxDeleteRieltor = Yii::$app->urlManager->createUrl(['/agency/delete-rieltor', 'id' => '']);
$urlAjaxDeleteInvoice = Yii::$app->urlManager->createUrl(['/payments/ajax-delete', 'id' => '']);
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
    
    $(document).on('show.bs.modal', '#modal-rieltor', function(e) {
        if (e.namespace === 'bs.modal') {
            var modalContent = $(this).find('.modal-content');
            var invoker = $(e.relatedTarget);
            var rieltorId = invoker.attr('data-rieltor_id') || 0;
            modalContent.html('<div class="modal-body">загрузка...</div>');
            $.get('{$urlAjaxGetRieltorForm}'+rieltorId, function(data) {
                modalContent.html(data);
            });
        }
    });
            
    $(document).on('click', '.delete-rieltor', function(e) {
        e.preventDefault();
        console.log('deleting');    
            
        if (confirm("Вы уверены, что хотите удалить этот элемент?")) {
            var rieltorId = $(this).attr('data-rieltor_id') || 0;
            $.post('{$urlAjaxDeleteRieltor}'+rieltorId, function(data) {
                $.pjax.reload({container: "#rieltors"}); //Reload GridView
            });
        } 
        return false;
    });
            
    $(document).on('click', '.delete-invoice', function(e) {
        e.preventDefault();
        console.log('deleting');    
            
        if (confirm("Вы уверены, что хотите удалить этот элемент?")) {
            var invoiceId = $(this).attr('data-invoice_id') || 0;
            $.post('{$urlAjaxDeleteInvoice}'+invoiceId, function(data) {
                $.pjax.reload({container: "#invoices"}); //Reload GridView
            });
        } 
        return false;
    });
JS
);
