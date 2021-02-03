<?php

use yii\db\Migration;

/**
 * Class m181010_084832_modify_client_agency_description_field
 */
class m181010_084832_modify_client_agency_description_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('agency', 'description', $this->text()->null()->after('email'));
        $this->alterColumn('client', 'description', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('agency', 'description');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181010_084832_modify_client_agency_description_field cannot be reverted.\n";

        return false;
    }
    */
}
