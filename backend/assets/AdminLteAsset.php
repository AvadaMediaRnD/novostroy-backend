<?php
namespace backend\assets;


use yii\base\Exception;
use yii\web\AssetBundle;

/**
 * AdminLte AssetBundle
 * @since 0.1
 */
class AdminLteAsset extends AssetBundle
{
    public $sourcePath = '@webroot/admin-lte';

    public $css = [
        "bower_components/bootstrap/dist/css/bootstrap.min.css",
        "bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css",
        "bower_components/font-awesome/css/font-awesome.min.css",
        "bower_components/Ionicons/css/ionicons.min.css",
        "bower_components/jvectormap/jquery-jvectormap.css",
        "dist/css/AdminLTE.min.css",
        "plugins/iCheck/flat/blue.css",
        "plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css",
        "dist/css/skins/_all-skins.min.css",
        "dist/css/style.css",
        "https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic",

        //'plugins/jvectormap/jquery-jvectormap-1.2.2.css',
        //'dist/css/AdminLTE.min.css',
        //'dist/css/skins/_all-skins.min.css',
        //'plugins/datatables/dataTables.bootstrap.css',
        //'dist/css/style.css',
    ];
    public $js = [
        "bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js",
        "bower_components/fastclick/lib/fastclick.js",
        "dist/js/adminlte.js",
        "plugins/iCheck/icheck.min.js",
        "plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.js",
        "plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.ru-RU.js",
        "bower_components/jquery-sparkline/dist/jquery.sparkline.min.js",
        "plugins/jvectormap/jquery-jvectormap-1.2.2.min.js",
        "plugins/jvectormap/jquery-jvectormap-world-mill-en.js",
        "bower_components/jquery-slimscroll/jquery.slimscroll.min.js",
        "bower_components/chart.js/Chart.js",
         //"https://cdn.jsdelivr.net/momentjs/latest/moment.min.js",
         //"https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js",
        "dist/js/main.js",
        //"/js/ckeditor4/lang/ru.js",
        "/js/ckeditor4/ckeditor.js",

       /* 'plugins/fastclick/fastclick.min.js',
        'dist/js/app.min.js',
        'plugins/sparkline/jquery.sparkline.min.js',
        'plugins/jvectormap/jquery-jvectormap-1.2.2.min.js',
        'plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
        'plugins/slimScroll/jquery.slimscroll.min.js',
        'plugins/chartjs/Chart.min.js',
        'plugins/datatables/jquery.dataTables.min.js',
        'plugins/datatables/dataTables.bootstrap.min.js'*/
    ];
    public $depends = [
        //'backend\assets\FontawesomeAsset',
        'backend\assets\AppAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    /**
     * @var string|bool Choose skin color, eg. `'skin-blue'` or set `false` to disable skin loading
     * @see https://almsaeedstudio.com/themes/AdminLTE/documentation/index.html#layout
     */
    public $skin = '_all-skins';

    /**
     * @inheritdoc
     */
    public function init()
    {
        // Append skin color file if specified
        if ($this->skin) {
            if (('_all-skins' !== $this->skin) && (strpos($this->skin, 'skin-') !== 0)) {
                throw new Exception('Invalid skin specified');
            }

            $this->css[] = sprintf('dist/css/skins/%s.min.css', $this->skin);
        }

        parent::init();
    }
}
