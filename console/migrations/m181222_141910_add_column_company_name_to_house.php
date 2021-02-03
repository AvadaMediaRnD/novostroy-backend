<?php

use yii\db\Migration;

/**
 * Class m181222_141910_add_column_company_name_to_house
 */
class m181222_141910_add_column_company_name_to_house extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('house', 'company_name', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('house', 'company_name');
    }
}
