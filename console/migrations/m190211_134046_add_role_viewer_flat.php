<?php

use yii\db\Migration;
use common\models\User;

/**
 * Class m190211_134046_add_role_viewer_flat
 */
class m190211_134046_add_role_viewer_flat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('roles', ['id' => User::ROLE_VIEWER_FLAT, 'name' => 'viewer_flat']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('roles', ['id' => User::ROLE_VIEWER_FLAT]);
    }

}
