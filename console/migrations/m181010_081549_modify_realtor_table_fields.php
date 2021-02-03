<?php

use yii\db\Migration;

/**
 * Class m181010_081549_modify_realtor_table_fields
 */
class m181010_081549_modify_realtor_table_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('rieltor', 'lastname', $this->string(255)->null()->after('id'));
        $this->addColumn('rieltor', 'middlename', $this->string(255)->null()->after('id'));
        $this->addColumn('rieltor', 'firstname', $this->string(255)->null()->after('id'));
        $this->dropColumn('rieltor', 'fullname');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('rieltor', 'firstname');
        $this->dropColumn('rieltor', 'middlename');
        $this->dropColumn('rieltor', 'lastname');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181010_081549_modify_realtor_table_fields cannot be reverted.\n";

        return false;
    }
    */
}
