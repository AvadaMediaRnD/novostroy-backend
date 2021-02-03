<?php

use yii\db\Migration;

/**
 * Class m181204_084943_modify_flat_table_add_fk_agency_id
 */
class m181204_084943_modify_flat_table_add_fk_agency_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
ALTER TABLE `flat` 
ADD `agency_id` INT(11) NULL DEFAULT NULL AFTER `client_id`,
ADD CONSTRAINT `fk_flat_agency1` FOREIGN KEY (`agency_id`) REFERENCES `agency`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
ALTER TABLE `flat` DROP FOREIGN KEY `fk_flat_agency1`;
ALTER TABLE `flat` DROP `agency_id`;
        ');
    }
}
