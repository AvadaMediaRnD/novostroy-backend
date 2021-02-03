<?php

use yii\db\Migration;

/**
 * Class m181019_111210_add_column_type_to_table_invoice
 */
class m181019_111210_add_column_type_to_table_invoice extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `invoice` ADD `type` ENUM('income','outcome') NOT NULL AFTER `description`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('invoice', 'type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181019_111210_add_column_type_to_table_invoice cannot be reverted.\n";

        return false;
    }
    */
}
