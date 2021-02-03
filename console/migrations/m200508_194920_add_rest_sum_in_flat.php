<?php

use yii\db\Migration;

/**
 * Class m200508_194920_add_rest_sum_in_flat
 */
class m200508_194920_add_rest_sum_in_flat extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('flat', 'price_paid_out', $this->decimal(14, 4)->notNull()->defaultValue(0.0000)->after('price_paid_init'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m200508_194920_add_rest_sum_in_flat cannot be reverted.\n";

        $this->dropColumn('flat', 'price_paid_out');
    }

}
