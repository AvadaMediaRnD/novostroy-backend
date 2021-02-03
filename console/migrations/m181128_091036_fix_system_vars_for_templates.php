<?php

use yii\db\Migration;
use common\models\SystemConfig;

/**
 * Class m181128_091036_fix_system_vars_for_templates
 */
class m181128_091036_fix_system_vars_for_templates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach (SystemConfig::find()->where(['type' => SystemConfig::TYPE_VARIABLE])->all() as $configVar) {
            $configVar->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
