<?php

use yii\bootstrap\Html,
    yii\web\JsExpression;
use miloschuman\highcharts\Highcharts;

/* @var $this yii\web\View */
/* @var $priceTotalDebt float */
?>

<div class="row">
    <?php
    echo Highcharts::widget([
        'scripts' => [
            'modules/exporting',
            'themes/grid-light',
        ],
        'options' => [
            'chart' => [
                'height' => 278,
                'type' => 'pie'
            ],
            'title' => [
                'text' => 'Статистика продаж квартир',
            ],
            'labels' => [
                'items' => [
                    [
                        'style' => [
                            'left' => '0px',
                            'top' => '0px',
                            'color' => new JsExpression('(Highcharts.theme && Highcharts.theme.textColor) || "light"'),
                        ],
                    ],
                ],
            ],
            'tooltip' => [
                'pointFormat' => '{series.name}  <b>{point.y} y.e. ({point.percentage:.1f} %)</b>'
            ],
            'series' => [
                [
                    'type' => 'pie',
                    'name' => ' ',
                    'data' => [
                        [
                            'name' => 'Получено',
                            'y' => $totalInUsd,
                            'color' => new JsExpression('Highcharts.getOptions().colors[7]'),
                        ],
                        [
                            'name' => 'Необходимо',
                            'y' => 20000000,
                            'color' => new JsExpression('Highcharts.getOptions().colors[5]'),
                        ],
                    ],
                    'showInLegend' => true,
                    'dataLabels' => [
                        'enabled' => false,
                    ],
                ],
            ],
            'radialGradient' => [
                'cx' => 0.5,
                'cy' => 0.3,
                'r' => 0.7
            ],
            'stops' => [
                [0, 'color'],
                [1, 'color']
            ],
        ],
    ]);
    ?>
</div>
