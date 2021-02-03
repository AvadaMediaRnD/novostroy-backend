<?php

use yii\db\Migration;

/**
 * Class m180809_140329_demo_articles
 */
class m180809_140329_demo_articles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('article', ['id', 'type', 'name'], [
            [1, 'income', 'Оплата за квартиру'],
            [2, 'income', 'Прочие приходы'],
            [3, 'outcome', 'Комиссионные агентства'],
            [4, 'outcome', 'Комиссионные менеджера'],
            [5, 'outcome', 'Расходы на строительство'],
            [6, 'outcome', 'Сдача кассы директору'],
            [7, 'outcome', 'Прочие расходы'],
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

DELETE FROM `article`;

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
        echo "m180809_140329_demo_articles cannot be reverted.\n";

        return false;
    }
    */
}
