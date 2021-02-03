<?php

use yii\db\Migration;

/**
 * Class m180814_115854_demo_clients
 */
class m180814_115854_demo_clients extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('client', ['id', 'firstname', 'middlename', 'lastname', 'address', 'inn', 'passport_series', 'passport_number', 'passport_from', 'phone', 'email', 'description', 'created_at', 'updated_at', 'agency_id', 'user_id'], [
            [1, 'Павел', 'Алексеевич', 'Громов', 'ул. Зеленая, 12, кв.24', 'ИНН1234567890', 'КМ', '1234567', 'Паспортный стол ДЕМО', '+380771001010', 'client1@client.com', '', 1534240000, 1534240000, NULL, NULL],
            [2, 'Мария', 'Игоревна', 'Климко', 'ул. Радостная, 132, кв.54', 'ИНН2345678901', 'КМ', '2345678', 'Паспортный стол ДЕМО', '+380772002020', 'client2@client.com', '', 1534250000, 1534250000, NULL, NULL],
            [3, 'Алина', 'Михайловна', 'Хомкина', 'ул. Утренняя, 53, кв.7', 'ИНН3456789012', 'КМ', '3456789', 'Паспортный стол ДЕМО', '+380773003030', 'client3@client.com', '', 1534260000, 1534260000, NULL, NULL],
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

DELETE FROM `client`;

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
        echo "m180814_115854_demo_clients cannot be reverted.\n";

        return false;
    }
    */
}
