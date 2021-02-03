<?php

use yii\db\Migration;

/**
 * Class m181113_101803_add_column_number_index_to_flat
 */
class m181113_101803_add_column_number_index_to_flat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('flat', 'number_index', $this->string(255)->null()->after('number'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('flat', 'number_index');
    }
}
