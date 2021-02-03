<?php
namespace backend\assets;


use yii\base\Exception;
use yii\web\AssetBundle;

/**
 * AdminLte AssetBundle
 * @since 0.1
 */
class DashBoardAsset extends AssetBundle
{
    public $sourcePath = '@vendor/bower/AdminLTE';

    public $css = [
        "plugins/morris/morris.css",
        "https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"
        //'plugins/jvectormap/jquery-jvectormap-1.2.2.css',
        //'dist/css/AdminLTE.min.css',
        //'dist/css/skins/_all-skins.min.css',
        //'dist/css/style.css',
    ];
    public $js = [
        "https://code.jquery.com/ui/1.11.4/jquery-ui.min.js",
        "https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js",
        "plugins/morris/morris.min.js",
        "plugins/knob/jquery.knob.js",
        "https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js",
        "plugins/daterangepicker/daterangepicker.js",
        "plugins/datepicker/bootstrap-datepicker.js",
        "plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js",
        //"dist/js/pages/dashboard.js",
        "dist/js/demo.js",
        //'js/pages/dashboard2.js',
    ];
    public $depends = [
        'backend\assets\AdminLteAsset',
    ];

}
