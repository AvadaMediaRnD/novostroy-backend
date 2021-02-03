<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\widgets\adminlte;

use \yii\bootstrap\Widget;
use common\models\ViewNotification;

/* * `
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

class AlertSaldo extends Widget {

    /**
     * Initializes the widget.
     * This method will register the bootstrap asset bundle. If you override this method,
     * make sure you call the parent implementation first.
     */
    public function init() {
        parent::init();
    }

    public function run() {

        $notifications = ViewNotification::find()->where(['<', 'price_saldo_total', 0])->all();

        return $this->render('saldo-informer', ['notifications' => $notifications]);
    }

}
