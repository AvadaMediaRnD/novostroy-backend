<?php

use yii\db\Migration;

/**
 * Class m181225_111207_add_column_rate_to_agreement
 */
class m181225_111207_add_column_rate_to_agreement extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('agreement_flat', 'rate', $this->decimal(8, 4)->null()->defaultValue(0)->after('price'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('agreement_flat', 'rate');
    }

}
