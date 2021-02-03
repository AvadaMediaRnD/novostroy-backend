<?php

use yii\db\Migration;

/**
 * Class m181211_090105_add_column_is_custom_to_article
 */
class m181211_090105_add_column_is_custom_to_article extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `article` ADD `is_custom` TINYINT(1) DEFAULT 0;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('article', 'is_custom');
    }
}
