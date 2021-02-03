<?php

use yii\db\Migration;

/**
 * Class m191118_155912_replace_view_total_plan_fact
 */
class m191216_105412_add_hash_string_for_sync_in_houses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('house', 'sync_id', $this->string(16));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('house', 'sync_id');
    }
}
