<?php

use yii\helpers\Url,
    yii\widgets\ActiveForm,
    yii\widgets\LinkPager,
    yii\web\JsExpression;
use backend\widgets\InformerWidget;
use kartik\daterange\DateRangePicker;
use common\helpers\PriceHelper;
use dosamigos\multiselect\MultiSelect;

$this->registerJS("

//$('#test').change(function() {
//    sectionChange();
//});

function sectionChange() {
    data = $('#test').select2('data');
    newUrl = removeParam('sections', window.location.href);

    data.map(function(a) {return a.text;}).forEach(function(element, index) {

        if(!(window.location.href.indexOf('?') > -1) && index == 0) {
            newUrl = newUrl + '?sections[' + index + ']=' + element;
        } else {
            newUrl = newUrl +  '&sections[' + index + ']=' + element;
        }
    });
    console.log(newUrl);

    window.location.href = newUrl;
}

function removeParam(key, sourceURL) {
    var rtn = sourceURL.split('?')[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf('?') !== -1) ? sourceURL.split('?')[1] : '';
    if (queryString !== '') {
        params_arr = queryString.split('&');
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split('=')[0];
            console.log(param);
            if (param.includes(key)) {
                params_arr.splice(i, 1);
            }
        }
        rtn = rtn + '?' + params_arr.join('&');
    }
    return rtn;
}

");

/* @var $this yii\web\View */
/* @var $searchModel backend\models\product\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $models \common\models\ViewTotalPlanFact[] */
/* @var $houses \common\models\House[] */
/* @var $pages yii\data\Pagination */
/* @var $chartLabels string */
/* @var $chartDataPlan string */
/* @var $chartDataFact string */
/* @var $chartDataDebt string */
/* @var $flatsTotal integer */
/* @var $flatsTotalAvailable integer */
/* @var $flatsTotalSold integer */
/* @var $squareTotal integer */
/* @var $squareTotalAvailable integer */
/* @var $squareTotalSold integer */
/* @var $priceTotalPlan float */
/* @var $priceTotalFact float */
/* @var $priceTotalRemain float */

$this->title = 'Статистика';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12 col-lg-5">
        <h2 class="page-header">Сводка по ПЛАН-ФАКТ:</h2>
    </div>
    <div class="col-xs-12 col-lg-7">
        <div class="menu-align text-right">
            <?php
            ActiveForm::begin([
                'method' => 'get',
                'action' => Url::current(['date_range' => null, 'sections' => null]),
                'options' => ['class' => 'form'],
            ])
            ?>
            <div class="daterange margin-right-15 text-left display-inline">
                <label class="control-label">Период:</label>
                <div class="input-group drp-container">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <?=
                    DateRangePicker::widget([
                        'name' => 'date_range',
                        'value' => Yii::$app->request->get('date_range'),
                        'convertFormat' => true,
                        'useWithAddon' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'd.m.Y',
                                'separator' => ' - ',
                            ],
                            'opens' => 'left'
                        ],
                        'options' => [
                            'class' => 'form-control',
                            'onchange' => '$(".form").submit();',
                        ],
                    ]);
                    ?>
                </div>
                <!-- /.input group -->
            </div>

            <div class="btn-group margin-bottom text-left display-inline">
                <label style="display:block;">Объект:</label>
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $selectedHouseName ? $selectedHouseName : 'Выберите действие' ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="<?= Url::to(['/statistic/index', 'date_range' => Yii::$app->request->get('date_range')]) ?>">Все объекты</a></li>
                    <?php foreach ($houseNames as $houseName) { ?>
                        <li><a href="<?= Url::to(['/statistic/index', 'house_name' => $houseName, 'date_range' => Yii::$app->request->get('date_range')]) ?>"><?= $houseName ?></a></li>
                    <?php } ?>
                </ul>
            </div>

            <?php if ($selectedHouseName): ?>
                <div class="btn-group margin-bottom text-left display-inline">
                    <label style="display:block;">Секция:</label>

                    <?php
                    echo MultiSelect::widget([
                        'id' => 'sections',
                        'options' => ['multiple' => 'multiple'], // for the actual multiselect
                        'data' => $sectionsNames, // data as array
                        'value' => $sections, // if preselected
                        'name' => 'sections', // name for the form
                        'clientOptions' => [
                            'includeSelectAllOption' => true,
                            'numberDisplayed' => 3,
                            'buttonWidth' => 120,
                            'selectAllText' => 'Все',
                            'nonSelectedText' => 'Не выбрано',
                            'nSelectedText' => ' секций',
                            'allSelectedText' => 'Все секции',
                            'onDropdownHide' => new JsExpression('function(e){$(".form").submit();}')
                        ],
                    ]);
                    ?>

                    <?php /* echo Select2::widget([
                      'name' => 'sections',
                      'value' =>  Yii::$app->request->get('sections'), // initial value (will be ordered accordingly and pushed to the top)
                      'data' => $sectionsNames,
                      //'maintainOrder' => true,
                      'options' => ['id'=> 'test', 'placeholder' => 'Select a color ...', 'multiple' => true],
                      'pluginOptions' => [
                      'tags' => true,
                      'maximumInputLength' => 10
                      ],
                      ]); */ ?>

                </div>
            <?php endif; ?>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
<div class="row">

    <?=
    InformerWidget::widget([
        'items' => [
            InformerWidget::W_FLATS,
            InformerWidget::W_SQUARE,
            InformerWidget::W_MONEY,
        ],
    ])
    ?>

</div>
<div class="row">
    <div class="col-xs-8">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">График платежей</h3>
            </div>
            <div class="box-body">
                <div class="row barchart-flex">
                    <div class="col-sm-12 col-md-2 col-lg-1 hidden-sm">
                        <div id="barChart-legend" class="text-center"></div>
                    </div>
                    <div class="col-sm-12 col-md-10 col-lg-11">
                        <div class="chart">
                            <canvas id="barChart" style="height: 230px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-4">
        <div class="box box-default">
            <div class="box-body">
                <?=
                InformerWidget::widget([
                    'items' => [
                        InformerWidget::W_DEBT_DIAGRAM,
                    ],
                ])
                ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Таблица платежей</h3>
            </div>
            <?php // Pjax::begin();    ?>
            <div class="box-body table-responsive no-padding">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th style="width: 40px; min-width: 40px;">#</th>
                            <th style="min-width: 80px;">МЕСЯЦ</th>
                            <th style="min-width: 80px;">ПЛАН</th>
                            <th style="min-width: 80px;">ФАКТ</th>
                            <th style="min-width: 80px;">ЗАДОЛЖЕННОСТЬ</th>
                            <th style="min-width: 80px;">ОСТАТОК</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $fact_total = $startFactTotal;
                        foreach ($models as $k => $model) {
                            $fact_total += $model->price_fact_total;
                            if($model->year_month <= date('Y-m') && (floatval($model->price_fact_total) >= 0)) {
                                $priceDest = $model->price_plan_total - $model->price_fact_total;
                            }
                            else {
                                $priceDest = 0;
                            }
                            ?>
                            <?php
                            $isActive = $model->year_month == date('Y-m');
                            ?>
                            <tr class="<?= $isActive ? 'active' : '' ?>">
                                <td><?= $pages->offset + $k + 1 ?></td>
                                <td><?= $model->getMonthName() ?>, <?= $model->year ?></td>
                                <td><?= PriceHelper::format($model->price_plan_total, false) ?></td>
                                <td><?= PriceHelper::format($model->price_fact_total, false) ?></td>
                                <td><?= PriceHelper::format($priceDest, false) ?></td>
                                <td><?= PriceHelper::format($priceTotal - $fact_total, false) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer clearfix">
                <?=
                LinkPager::widget([
                    'pagination' => $pages,
                    'options' => [
                        'class' => 'pagination pagination-sm no-margin pull-right',
                    ]
                ]);
                ?>
            </div>
            <?php // Pjax::end();     ?>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
    $(function () {

        'use strict';

        //-------------
        //- BAR CHART -
        //-------------

        var chartData = {
            labels  : {$chartLabels},
            datasets: [
                {
                    label               : 'План',
                    fillColor           : 'rgba(51, 122, 183, 1)',
                    strokeColor         : 'rgba(51, 122, 183, 1)',
                    data                : {$chartDataPlan}
                },
                {
                    label               : 'Факт',
                    fillColor           : 'rgba(0, 166, 90, 1)',
                    strokeColor         : 'rgba(0, 166, 90, 1)',
                    data                : {$chartDataFact}
                },
                {
                    label               : 'Задолженность',
                    fillColor           : 'rgba(243, 156, 18, 1)',
                    strokeColor         : 'rgba(243, 156, 18, 1)',
                    data                : {$chartDataDebt}
                }
            ]
        };

        var barChartCanvas                   = $('#barChart').get(0).getContext('2d');
        var barChart                         = new Chart(barChartCanvas);
        var barChartData                     = chartData;
//        barChartData.datasets[1].fillColor   = '#00a65a';
//        barChartData.datasets[1].strokeColor = '#00a65a';
//        barChartData.datasets[1].pointColor  = '#00a65a';
        var barChartOptions                  = {
            //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
            scaleBeginAtZero        : true,
            //Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines      : true,
            //String - Colour of the grid lines
            scaleGridLineColor      : 'rgba(0,0,0,.05)',
            //Number - Width of the grid lines
            scaleGridLineWidth      : 1,
            //Boolean - Whether to show horizontal lines (except X axis)
            scaleShowHorizontalLines: true,
            //Boolean - Whether to show vertical lines (except Y axis)
            scaleShowVerticalLines  : true,
            //Boolean - If there is a stroke on each bar
            barShowStroke           : true,
            //Number - Pixel width of the bar stroke
            barStrokeWidth          : 2,
            //Number - Spacing between each of the X value sets
            barValueSpacing         : 5,
            //Number - Spacing between data sets within X values
            barDatasetSpacing       : 1,
            //String - A legend template
            legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
            //Boolean - whether to make the chart responsive
            responsive              : true,
            maintainAspectRatio     : true
        };

        barChartOptions.datasetFill = false;
        var myBarChart = barChart.Bar(barChartData, barChartOptions);
        document.getElementById('barChart-legend').innerHTML = myBarChart.generateLegend();

        //-----------------
        //- END BAR CHART -
        //-----------------
        
    });
   
JS
);
