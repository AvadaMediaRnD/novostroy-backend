<?php

use yii\db\Migration;

/**
 * Class m181024_103956_modify_invoice_table_add_fk_agency_id
 */
class m181024_103956_modify_invoice_table_add_fk_agency_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
ALTER TABLE `invoice` 
ADD `agency_id` INT(11) NULL DEFAULT NULL AFTER `client_id`,
ADD CONSTRAINT `fk_invoice_agency1` FOREIGN KEY (`agency_id`) REFERENCES `agency`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
ALTER TABLE `invoice` DROP FOREIGN KEY `fk_invoice_agency1`;
ALTER TABLE `invoice` DROP `agency_id`;
        ');
    }

}
