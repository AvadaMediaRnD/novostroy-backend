<?php

use yii\db\Migration;

/**
 * Class m190123_151730_add_columns_to_agreement_for_ukr
 */
class m190123_151730_add_columns_to_agreement_for_ukr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('agreement', 'tpl_house_address', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('agreement', 'tpl_client_fullname', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('agreement', 'tpl_client_fullname_short', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('agreement', 'tpl_client_birthdate_text', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('agreement', 'tpl_client_passport_from', $this->string(255)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('agreement', 'tpl_house_address');
        $this->dropColumn('agreement', 'tpl_client_fullname');
        $this->dropColumn('agreement', 'tpl_client_fullname_short');
        $this->dropColumn('agreement', 'tpl_client_birthdate_text');
        $this->dropColumn('agreement', 'tpl_client_passport_from');
    }

}
