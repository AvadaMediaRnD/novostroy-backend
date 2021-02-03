<?php

use yii\db\Migration;

/**
 * Class m181114_150812_add_column_price_paid_init_to_flat
 */
class m181114_150812_add_column_price_paid_init_to_flat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('flat', 'price_paid_init', $this->decimal(14,4)->null()->after('price_discount_m'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('flat', 'price_paid_init');
    }
}
