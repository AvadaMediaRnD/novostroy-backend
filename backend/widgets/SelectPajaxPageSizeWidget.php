<?php

namespace backend\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class SelectPajaxPageSizeWidget extends Widget
{
    public $pageSize;
    public $pajaxId;

    public function init()
    {
        parent::init();
        if ($this->pageSize=== null) {
            $this->pageSize = 10;
        }
        if ($this->pajaxId === null) {
            $this->pajaxId = 'pjax-grid-view';
        }
    }

    public function run()
    {
        return Html::dropDownList('pageSize',$this->pageSize,[10 => 10, 20 => 20, 50 => 50, 100 => 100],[
                'class' => 'form-control',
                'style' => 'width:5%; min-width:68px; float:left; margin:20px 5px 20px; ',
                'onchange'=>"$.pjax({container: '#".$this->pajaxId."' , data: {'pageSize': $(this).val() }})"
        ]);
    }
}
