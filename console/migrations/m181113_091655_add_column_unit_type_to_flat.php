<?php

use yii\db\Migration;

/**
 * Class m181113_091655_add_column_unit_type_to_flat
 */
class m181113_091655_add_column_unit_type_to_flat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('flat', 'unit_type', $this->string(255)->null()->defaultValue(common\models\Flat::TYPE_FLAT)->after('number'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('flat', 'unit_type');
    }
}
