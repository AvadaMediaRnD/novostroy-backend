<?php

use yii\db\Migration;

/**
 * Class m181221_141910_add_column_company_name_to_invoice
 */
class m181221_141910_add_column_company_name_to_invoice extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('invoice', 'company_name', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('invoice', 'company_name');
    }
}
