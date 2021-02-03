<?php

use yii\db\Migration;

/**
 * Class m180814_120929_demo_agreements
 */
class m180814_120929_demo_agreements extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('agreement', ['id', 'uid', 'uid_date', 'status', 'created_at', 'updated_at', 'flat_id', 'agency_id', 'client_id', 'agreement_template_id'], [
            [1, '0001', '2018-06-20', 5, 1534240000, 1534240000, 3, NULL, 1, NULL],
            [2, '0002', '2018-07-15', 5, 1534250000, 1534250000, 6, NULL, 2, NULL],
            [3, '0003', '2018-07-30', 5, 1534260000, 1534260000, 9, NULL, 3, NULL],
            [4, '0004', '2018-08-01', 5, 1534270000, 1534270000, 16, NULL, 1, NULL],
            [5, '0005', '2018-08-10', 10, 1534280000, 1534280000, 26, NULL, 2, NULL],
        ]);
        
        $this->batchInsert('agreement_flat', ['id', 'number', 'n_rooms', 'floor', 'square', 'address', 'price', 'created_at', 'updated_at', 'agreement_id'], [
            [1, 103, 1, 2, 30, 'ул. Фиолетовая, 132', '13500', 1534240000, 1534240000, 1],
            [2, 106, 1, 4, 33, 'ул. Фиолетовая, 132', '23100', 1534250000, 1534250000, 2],
            [3, 109, 1, 5, 36, 'ул. Фиолетовая, 132', '28800', 1534260000, 1534260000, 3],
            [4, 206, 1, 4, 33, 'ул. Фиолетовая, 136', '19800', 1534270000, 1534270000, 4],
            [5, 306, 1, 4, 33, 'ул. Бронзовая, 34', '19800', 1534280000, 1534280000, 5],
        ]);
        
        $this->batchInsert('agreement_client', ['id', 'firstname', 'middlename', 'lastname', 'address', 'inn', 'passport_series', 'passport_number', 'passport_from', 'phone', 'email', 'description', 'created_at', 'updated_at', 'agreement_id'], [
            [1, 'Павел', 'Алексеевич', 'Громов', 'ул. Зеленая, 12, кв.24', 'ИНН1234567890', 'КМ', '1234567', 'Паспортный стол ДЕМО', '+380771001010', 'client1@client.com', '', 1534240000, 1534240000, 1],
            [2, 'Мария', 'Игоревна', 'Климко', 'ул. Радостная, 132, кв.54', 'ИНН2345678901', 'КМ', '2345678', 'Паспортный стол ДЕМО', '+380772002020', 'client2@client.com', '', 1534250000, 1534250000, 2],
            [3, 'Алина', 'Михайловна', 'Хомкина', 'ул. Утренняя, 53, кв.7', 'ИНН3456789012', 'КМ', '3456789', 'Паспортный стол ДЕМО', '+380773003030', 'client3@client.com', '', 1534260000, 1534260000, 3],
            [4, 'Павел', 'Алексеевич', 'Громов', 'ул. Зеленая, 12, кв.24', 'ИНН1234567890', 'КМ', '1234567', 'Паспортный стол ДЕМО', '+380771001010', 'client1@client.com', '', 1534270000, 1534270000, 4],
            [5, 'Мария', 'Игоревна', 'Климко', 'ул. Радостная, 132, кв.54', 'ИНН2345678901', 'КМ', '2345678', 'Паспортный стол ДЕМО', '+380772002020', 'client2@client.com', '', 1534280000, 1534280000, 5],
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

DELETE FROM `agreement_client`;
DELETE FROM `agreement_flat`;
DELETE FROM `agreement`;

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
        echo "m180814_120929_demo_agreements cannot be reverted.\n";

        return false;
    }
    */
}
