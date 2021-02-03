<?php

use yii\db\Migration;

/**
 * Class m181225_130136_add_column_birthdate_to_client_agreement_client
 */
class m181225_130136_add_column_birthdate_to_client_agreement_client extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('agreement_client', 'birthdate', $this->date()->null()->defaultValue(null)->after('lastname'));
        $this->addColumn('client', 'birthdate', $this->date()->null()->defaultValue(null)->after('lastname'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('agreement_client', 'birthdate');
        $this->dropColumn('client', 'birthdate');
    }

}
