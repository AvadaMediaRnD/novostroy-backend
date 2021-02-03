<?php
use common\widgets\adminlte\Menu;
use yii\helpers\Html;
use yii\helpers\Url;

$this->registerCss('');
?>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->

        <!-- /.search form -->

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <?= $this->render('menus/' . $menuName, ['route' => $route]) ?>
    </section>
    <!-- /.sidebar -->
</aside>







