<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use backend\widgets\SelectPajaxPageSizeWidget;
use backend\widgets\InformerWidget;
use common\helpers\PriceHelper;
use common\models\House;
use common\models\Flat;
use kartik\export\ExportMenu;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use backend\models\FlatImportForm;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FlatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $informerFilter array */

$this->title = 'Помещения';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="row">
    <div class="col-xs-12 col-lg-5">
        <h2 class="page-header">Общая информация:</h2>
    </div>
    <?php if (Yii::$app->user->identity->role != User::ROLE_VIEWER_FLAT) { ?>
    <div class="col-xs-12 col-lg-7">
        <div class="menu-align text-right">
            <div class="btn-group margin-bottom">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Выберите действие <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['/flats/create']) ?>">Создать помещение</a></li>
                    <li><a href="#!" onclick="$('#export-xlsx a').click()">Скачать xlsx</a></li>
                    <li><a href="#!" data-toggle="modal" data-target="#modalFile">Загрузить xlsx</a></li>
                    <li><a href="#!" data-toggle="modal" data-target="#modalReport">Сформировать отчет</a></li>
                </ul>
                <?php
                $gridColumns = [
                    'id',
                    [
                        'attribute' => 'searchNumberWithIndex',
                        'label' => '№',
                        'value' => function ($model) {
                            return $model->getNumberWithIndex();
                        }
                    ],
                    [
                        'attribute' => 'unit_type',
                        'value' => function ($model) {
                            return $model->getUnitTypeLabel();
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            return $model->getStatusLabel();
                        }
                    ],
                    'house.name',
                    'house.section',
                    'floor',
                    'n_rooms',
                    'square',
                    [
                        'attribute' => 'price_m', 
                        'value' => function ($model) {
                            return floatval($model->price_m);
                        }
                    ],
                    [
                        'attribute' => 'price_sell_m', 
                        'value' => function ($model) {
                            return floatval($model->price_sell_m);
                        }
                    ],
                    [
                        'label' => 'Цена', 
                        'value' => function ($model) {
                            return floatval($model->getPrice());
                        }
                    ],
                    [
                        'label' => 'Цена продажи', 
                        'value' => function ($model) {
                            return floatval($model->getPriceSell());
                        }
                    ],
                    'price_paid_init',
                    'commission_agency',
                    [
                        'attribute' => 'commission_agency_type', 
                        'value' => function ($model) {
                            return $model->getCommissionAgencyTypeLabel();
                        }
                    ],
                    'commission_manager',
                    [
                        'attribute' => 'commission_manager_type', 
                        'value' => function ($model) {
                            return $model->getCommissionManagerTypeLabel();
                        }
                    ],
                    'description:raw',
                    [
                        'attribute' => 'client.lastname', 
                        'label' => 'Фамилия покупателя'
                    ],
                    [
                        'attribute' => 'client.firstname', 
                        'label' => 'Имя покупателя'
                    ],
                    [
                        'attribute' => 'client.middlename', 
                        'label' => 'Отчество покупателя'
                    ],
                    [
                        'attribute' => 'client.phone', 
                        'format' => 'raw',
                        'label' => 'Телефон покупателя'
                    ],
                    [
                        'attribute' => 'client.email', 
                        'label' => 'Email покупателя'
                    ],
                    [
                        'label' => 'Агентство',
                        'value' => function ($model) {
                            return $model->agency['name'];
                        }
                    ],
                    [
                        'attribute' => 'client.user.fullname', 
                        'label' => 'ФИО менеджера'
                    ],
                ];
                echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'showConfirmAlert' => false,
                    'showColumnSelector' => false,
                    'container' => ['class' => 'btn-group hide', 'role' => 'group'],
                    'filename' => 'export-flats-' . date('Ymd'),
                    'exportConfig' => [
                        ExportMenu::FORMAT_TEXT => false,
                        ExportMenu::FORMAT_HTML => false,
                        ExportMenu::FORMAT_PDF => false,
                        ExportMenu::FORMAT_CSV => false,
                        ExportMenu::FORMAT_EXCEL => false,
                        ExportMenu::FORMAT_EXCEL_X => [
                            'label' => Yii::t('kvexport', 'Excel 2007+'),
                            'icon' => 'floppy-remove',
                            'iconOptions' => ['class' => 'text-success'],
                            'linkOptions' => [],
                            'options' => ['id' => 'export-xlsx', 'title' => Yii::t('kvexport', 'Microsoft Excel 2007+ (xlsx)')],
                            'alertMsg' => Yii::t('kvexport', 'The EXCEL 95+ (xls) export file will be generated for download.'),
                            'mime' => 'application/application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'extension' => 'xlsx',
                            'writer' => ExportMenu::FORMAT_EXCEL_X,
                            
                        ],
                    ],
                    'dropdownOptions' => [
                        'label' => 'Выгрузить в XLS',
                        'class' => 'btn btn-secondary'
                    ],
                    'boxStyleOptions' => [
                        ExportMenu::FORMAT_EXCEL_X => [
                            'borders' => [
                                'outline' => [
                                    'borderStyle' => Border::BORDER_NONE,
                                    'color' => ['argb' => Color::COLOR_BLACK],
                                ],
                                'inside' => [
                                    'borderStyle' => Border::BORDER_NONE,
                                    'color' => ['argb' => Color::COLOR_BLACK],
                                ]
                            ],
                        ],
                    ],
                    'headerStyleOptions' => [
                        ExportMenu::FORMAT_EXCEL_X => [
                            'font' => ['bold' => true],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => [
                                    'argb' => 'FFE5E5E5',
                                ],
                            ],
                            'borders' => [
                                'outline' => [
                                    'borderStyle' => Border::BORDER_NONE,
                                    'color' => ['argb' => Color::COLOR_BLACK],
                                ],
                                'inside' => [
                                    'borderStyle' => Border::BORDER_NONE,
                                    'color' => ['argb' => Color::COLOR_BLACK],
                                ]
                            ],
                        ],
                    ],
                    'onRenderDataCell' => function($cell, $content, $model, $key, $index, $widget) {
                        if (!in_array($cell->getColumn(), ['H','I','J','K','L','M','N','O','Q'])) {
                            /* @var $cell PhpOffice\PhpSpreadsheet\Cell\Cell */
                            $cell->setValueExplicit($content, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                        }
                    },
                ]);
                ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<div class="row">
    
    <?= InformerWidget::widget([
        'notRoles' => [User::ROLE_VIEWER_FLAT],
        'items' => [
            InformerWidget::W_FLATS,
            InformerWidget::W_SQUARE,
            InformerWidget::W_MONEY,
        ],
        'filter' => $informerFilter,
    ]) ?>
    
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Таблица помещений</h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/flats/index']) ?>" class="btn btn-default btn-sm">
                        <span class="hidden-xs">Очистить фильтры</span><i class="fa fa-eraser visible-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            
            <?php 
            $columns = [
                [
                    'attribute' => 'searchNumberWithIndex',
                    'label' => '№',
                    'headerOptions' => ['style' => 'min-width: 100px; width: 100px'],
                    'value' => function ($model) {
                        return $model->getNumberWithIndex();
                    }
                ],
                [
                    'attribute' => 'unit_type',
                    'filter' => \kartik\select2\Select2::widget([
                        'data' => Flat::getUnitTypeOptions(),
                        'model' => $searchModel,
                        'attribute' => 'unit_type',
                        'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                        'options' => [
                            'placeholder' => '',
                        ],
                        'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                    ]),
                    'headerOptions' => ['style' => 'min-width: 150px; width: 150px'],
                    'value' => function ($model) {
                        return $model->getUnitTypeLabel();
                    }
                ],
                [
                    'attribute' => 'house_id',
                    'filter' => \kartik\select2\Select2::widget([
                        'data' => House::getOptions(),
                        'model' => $searchModel,
                        'attribute' => 'house_id',
                        'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                        'options' => [
                            'placeholder' => '',
                        ],
                        'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                    ]),
                    'value' => function ($model) {
                        return $model->house->getNameSection();
                    }
                ],
                [
                    'attribute' => 'status',
                    'headerOptions' => ['style' => 'min-width: 160px; width: 160px'],
                    'filter' => \kartik\select2\Select2::widget([
                        'data' => Flat::getStatusOptions(),
                        'model' => $searchModel,
                        'attribute' => 'status',
                        'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                        'options' => [
                            'placeholder' => '',
                        ],
                        'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                    ]),
                    'enableSorting' => false,
                    'value' => function ($model) {
                        return $model->getStatusLabel();
                    }
                ],
                [
                    'attribute' => 'square',
                    'label' => 'Площадь (м<sup>2</sup>)',
                    'headerOptions' => ['style' => 'min-width: 120px; width: 120px'],
                    'filter' => \kartik\field\FieldRange::widget([
                        'model' => $searchModel,
                        'attribute1' => 'searchSquareFrom',
                        'attribute2' => 'searchSquareTo',
                        'type' => \kartik\field\FieldRange::INPUT_TEXT,
                        'separator' => '',
                        'separatorOptions' => ['tag' => 'span', 'class' => 'hide'],
                        'options1' => ['placeholder' => 'от',],
                        'options2' => ['placeholder' => 'до',],
                    ]),
                    'encodeLabel' => false,
                ],
                [
                    'attribute' => 'searchPriceSell',
                    'label' => 'Стоимость факт (у.е.)',
                    'headerOptions' => ['style' => 'min-width: 180px; width: 180px'],
                    'filter' => \kartik\field\FieldRange::widget([
                        'model' => $searchModel,
                        'attribute1' => 'searchPriceSellFrom',
                        'attribute2' => 'searchPriceSellTo',
                        'type' => \kartik\field\FieldRange::INPUT_TEXT,
                        'separator' => '',
                        'separatorOptions' => ['tag' => 'span', 'class' => 'hide'],
                        'options1' => ['placeholder' => 'от',],
                        'options2' => ['placeholder' => 'до',],
                    ]),
                    'value' => function ($model) {
                        return PriceHelper::format($model->getPriceSell(), false);
                    }
                ],
//                    [
//                        'attribute' => 'searchPaymentBefore',
//                        'label' => 'Аванс (у.е.)',
//                        'headerOptions' => ['style' => 'min-width: 120px; width: 120px'],
//                        'filter' => false,
//                        'enableSorting' => false,
//                        'value' => function ($model) {
//                            return PriceHelper::format($model->getPaymentBefore(), false);
//                        }
//                    ],
            ];
            if (Yii::$app->user->identity->role == User::ROLE_VIEWER_FLAT) {
                $columns[] = [
                    'attribute' => 'searchClient',
                    'label' => 'Покупатель',
                    'headerOptions' => ['style' => 'min-width: 200px; width: 200px'],
                    'value' => function ($model) {
                        return $model->client->fullname;
                    }
                ];
                $columns[] = [
                    'attribute' => 'searchClientPhone',
                    'label' => 'Тел. покупателя',
                    'headerOptions' => ['style' => 'min-width: 200px; width: 200px'],
                    'value' => function ($model) {
                        return $model->client->phone;
                    }
                ];
            } else {
                $columns[] = [
                    'attribute' => 'searchPaymentPaid',
                    'label' => 'Внесено (у.е.)',
                    'headerOptions' => ['style' => 'min-width: 120px; width: 120px'],
                    'filter' => false,
                    'enableSorting' => false,
                    'value' => function ($model) {
                        return PriceHelper::format($model->getPaymentFact(), false);
                    }
                ];
                $columns[] = [
                    'attribute' => 'searchPaymentLeft',
                    'label' => 'Остаток (у.е.)',
                    'headerOptions' => ['style' => 'min-width: 120px; width: 120px'],
                    'filter' => false,
                    'enableSorting' => false,
                    'value' => function ($model) {
                        return PriceHelper::format($model->getPaymentLeft(), false);
                    }
                ];
                $columns[] = [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '<div class="btn-group pull-right">{update} {delete}</div>',
                    'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            return Html::a('<i class="fa fa-pencil"></i>',
                                ['/flats/update', 'id' => $model->id],
                                ['class' => 'btn btn-default btn-sm', 'title' => 'Редактировать']
                            );
                        },
                        'delete' => function ($url, $model, $key) {
                            return Html::a('<i class="fa fa-trash"></i>',
                                [
                                    '/flats/delete',
                                    'id' => $model->id,
                                ],
                                ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Удалить', 'data-pjax' => 0, 'data-method' => 'post', 'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?']
                            );
                        },
                    ]
                ];
            }
            
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions'=>['class'=>'table table-bordered table-hover table-striped table-nowrap linkedRow'],
                'layout'=> "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                'pager' => [
                    'class' => 'yii\widgets\LinkPager',
                    'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                ],
                'rowOptions' => function ($model, $index, $widget, $grid) {
                    return [
                        'data-href' => Yii::$app->user->identity->role == User::ROLE_VIEWER_FLAT ? '#' : Yii::$app->urlManager->createUrl(['/flats/update', 'id' => $model['id']]),
                    ];
                },
                'columns' => $columns,
            ]); ?>
            
        </div>
    </div>
</div>

<!-- Modal -->
<?php $flatImportForm = new FlatImportForm(['format' => 'xlsx']); ?>
<div class="modal fade" id="modalFile" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                'action' => ['flats/import'],
                'enableAjaxValidation' => true,
                'options' => ['enctype' => 'multipart/form-data'],
            ]);
            ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Импорт из xlsx</h4>
            </div>

            <div class="modal-body">
                <?= $form->field($flatImportForm, 'format')->hiddenInput()->label(false) ?>
                <?= $form->field($flatImportForm, 'file')->fileInput(['accept' => '.csv, .ods, .xls, .xlsx, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel']) ?>
                <?= $form->field($flatImportForm, 'is_only_new', ['enableClientValidation' => false])->checkbox() ?>
                
                <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#hint-types"><i class="fa fa-question-circle"></i> Помощь по заполнению</button>
                <div id="hint-types" class="collapse margin-top-15">
                    <p>1. <i class="fa fa-warning text-orange"></i> При загрузке система попытается найти квартиру с указанном в файле <b>ID</b> или <b>номером</b>, <b>домом</b> и <b>секцией</b>. 
                        Если такая квартира есть в базе, данные будут обновлены.
                        Иначе будет добавлена новая квартира.
                    </p>
                    <p>2. Если в базе не найден объект с названием <b>дома</b> и <b>секцией</b>, то он будет автоматически добавлен в систему.</p>
                    <p>2. Чтобы добавить только новые квартиры, установите флаг "<?= $flatImportForm->getAttributeLabel('is_only_new') ?>".</p>
                    <p>3. Доступные значения для поля <b>Статус</b>:
                        <ul>
                            <?php foreach (Flat::getStatusOptions() as $status) {
                                echo "<li>{$status}</li>";
                            } ?>
                        </ul>
                    </p>
                    <p>4. Доступные значения для поля <b>Тип помещения</b>:
                        <ul>
                            <?php foreach (Flat::getUnitTypeOptions() as $type) {
                                echo "<li>{$type}</li>";
                            } ?>
                        </ul>
                    </p>
                    <p>5. Доступные значения для полей <b>Тип комиссии агентства</b>, <b>Тип комиссии менеджера</b>:
                        <ul>
                            <?php foreach (Flat::getCommissionTypeOptions() as $type) {
                                echo "<li>{$type}</li>";
                            } ?>
                        </ul>
                    </p>
                    <p>6. Для заполнения стоимости, можно указать или цену за м<sup>2</sup>, или сразу полную цену в соответствующих колонках. При этом, обязательно должна быть заполнена площадь помещения.
                    </p>
                    <p>7. Чтобы указать номер помещения с индексом, необходимо его ввести через слеш, например: 523/2.
                    </p>
                </div>
                
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

<!-- Modal -->
<div class="modal fade" id="modalReport" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                'action' => ['flats/report'],
                'method' => 'get',
                'enableAjaxValidation' => true,
                'options' => ['enctype' => 'multipart/form-data', 'method' => 'get'],
            ]);
            ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Сформировать отчет</h4>
            </div>

            <div class="modal-body">
                <label>Дата от</label>
                <?php echo DatePicker::widget([
                    'name' => 'date_from',
                    'value' => date('01.m.Y', time()),
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]) ?>
            </div>
            <div class="modal-body">
                <label>Дата до</label>
                <?php echo DatePicker::widget([
                    'name' => 'date_to',
                    'value' => date('t.m.Y', time()),
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]) ?>
            </div>
            
            <div class="modal-footer text-center">
                <?= Html::button('Отмена', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
                <?= Html::submitButton('Скачать', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<!-- Modal -->