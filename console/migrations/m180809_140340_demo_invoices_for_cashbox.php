<?php

use yii\db\Migration;

/**
 * Class m180809_140340_demo_invoices_for_cashbox
 */
class m180809_140340_demo_invoices_for_cashbox extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('invoice', ['id', 'uid', 'uid_date', 'price', 'rate', 'description', 'status', 'created_at', 'updated_at', 'article_id', 'cashbox_id'], [
            [1, '0001', '2018-06-10', 1000, '27.2', '', 1, 1534140000, 1534140000, 2, 1],
            [2, '0002', '2018-06-15', 1000, '31.4', '', 1, 1534140100, 1534140100, 2, 2],
            [3, '0003', '2018-07-15', 1000, '1.0', '', 1, 1534140200, 1534140200, 2, 3],
            [4, '0004', '2018-06-12', 500, '27.2', '', 1, 1534140300, 1534140300, 7, 1],
            [5, '0005', '2018-07-15', 300, '31.4', '', 1, 1534140400, 1534140400, 7, 2],
            [6, '0006', '2018-08-01', 1000, '1.0', '', 1, 1534140500, 1534140500, 7, 3],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $sql = "
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DELETE FROM `invoice`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
            ";
        $this->execute($sql);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180809_140340_demo_invoices_for_cashbox cannot be reverted.\n";

        return false;
    }
    */
}
