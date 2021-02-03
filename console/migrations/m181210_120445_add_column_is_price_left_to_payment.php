<?php

use yii\db\Migration;

/**
 * Class m181210_120445_add_column_is_price_left_to_payment
 */
class m181210_120445_add_column_is_price_left_to_payment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `payment` ADD `is_price_left` TINYINT(1) DEFAULT 0 AFTER `pay_number`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('payment', 'is_price_left');
    }
}
