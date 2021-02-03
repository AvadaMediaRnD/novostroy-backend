<?php

namespace backend\widgets\adminlte;

use yii\bootstrap\Widget;

class LeftsideWidget extends Widget {

    const MENUS_VIEW_PATH = '@widgets/navwidget/views/menus/';

    /**
     * @var $_user User
     */
    private $_user;
    public $route;

    /**
     * @var $_authManager DbManager
     */
    private $_authManager;

    public function init() {
        $this->_user = \Yii::$app->user;
        $this->_authManager = \Yii::$app->authManager;

        parent::init();
    }

    public function run() {
        return $this->render('nav', ['menuName' => 'user-menu', 'route' => $this->route]);
    }

    /**
     * @return string
     */
    protected function getUserRole() {
        $userRoleName = 'admin';
        return isset($userRoleName) ? $userRoleName : null;
    }

}
