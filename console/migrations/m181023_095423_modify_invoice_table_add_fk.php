<?php

use yii\db\Migration;

/**
 * Class m181023_095423_modify_invoice_table_add_fk
 */
class m181023_095423_modify_invoice_table_add_fk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
ALTER TABLE `invoice` 
ADD `user_id` INT(11) NULL DEFAULT NULL AFTER `payment_id`,
ADD CONSTRAINT `fk_invoice_user1` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `invoice` 
ADD `rieltor_id` INT(11) NULL DEFAULT NULL AFTER `payment_id`,
ADD CONSTRAINT `fk_invoice_rieltor1` FOREIGN KEY (`rieltor_id`) REFERENCES `rieltor`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `invoice` 
ADD `client_id` INT(11) NULL DEFAULT NULL AFTER `payment_id`,
ADD CONSTRAINT `fk_invoice_client1` FOREIGN KEY (`client_id`) REFERENCES `client`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
        ');
        $this->execute('
ALTER TABLE `invoice` DROP FOREIGN KEY `fk_invoice_counterparty1`;
ALTER TABLE `invoice` DROP `counterparty_id`;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
ALTER TABLE `invoice` DROP FOREIGN KEY `fk_invoice_user1`;
ALTER TABLE `invoice` DROP FOREIGN KEY `fk_invoice_rieltor1`;
ALTER TABLE `invoice` DROP FOREIGN KEY `fk_invoice_client1`;
ALTER TABLE `invoice` DROP `client_id`;
ALTER TABLE `invoice` DROP `rieltor_id`;
ALTER TABLE `invoice` DROP `user_id`;
        ');
        $this->execute('
ALTER TABLE `invoice` 
ADD `counterparty_id` INT(11) NULL DEFAULT NULL,
ADD CONSTRAINT `fk_invoice_counterparty1` FOREIGN KEY (`counterparty_id`) REFERENCES `counterparty`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
        ');
    }
}
