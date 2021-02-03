<?php

use yii\db\Migration;

/**
 * Class m180809_135330_demo_cashbox
 */
class m180809_135330_demo_cashbox extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('cashbox', ['id', 'name', 'currency', 'rate', 'is_default'], [
            [1, 'Касса 1 (USD)', 'USD', 27.2, 0],
            [2, 'Касса 2 (EUR)', 'EUR', 31.4, 0],
            [3, 'Касса 3 (UAH)', 'UAH', 1, 1],
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

DELETE FROM `cashbox`;

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
        echo "m180809_135330_demo_cashbox cannot be reverted.\n";

        return false;
    }
    */
}
