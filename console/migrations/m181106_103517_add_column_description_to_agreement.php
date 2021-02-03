<?php

use yii\db\Migration;

/**
 * Class m181106_103517_add_column_description_to_agreement
 */
class m181106_103517_add_column_description_to_agreement extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `agreement` ADD `description` TEXT NULL DEFAULT NULL AFTER `scan_file`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('agreement', 'description');
    }

}
