<?php

use yii\db\Migration;

/**
 * Class m180806_122713_create_demo_users
 */
class m180806_122713_create_demo_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = "
INSERT INTO `user` (`id`, `auth_key`, `password_hash`, `password_reset_token`, `firstname`, `middlename`, `lastname`, `birthdate`, `phone`, `viber`, `telegram`, `description`, `image`, `role`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'TkhUmC6zbiTOEvzzoUlYXKnKZx5IYSfL', '" . '$2y$13$5magnHF5hsD9dQKvgq/.zeaAf1X74cL5HMYI7R1ConB8OQgX7Iufe' . "', NULL, 'Константин', 'Александрович', 'Филимонов', '1985-07-21', '+380991001010', '+380991001010', '@adminTg', 'Я тут главный', NULL, 10, 'admin@admin.com', 10, 1533645326, 1533645326),
(2, 'pgHC54uP5YkxqSfPo1WhK04Q9G7hEQ8i', '" . '$2y$13$nd9z/QHx99iEPBRHKGdVEeODF5V1jUHAIOZtaBSOjHTzvC6gVsmLW' . "', NULL, 'Марина', 'Николавна', 'Смит', '1989-09-22', '+380992002020', NULL, NULL, NULL, NULL, 0, 'user1@admin.com', 10, 1533728820, 1533728820);
            ";
        $this->execute($sql);
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

DELETE FROM `user`;

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
        echo "m180813_082713_create_demo_users cannot be reverted.\n";

        return false;
    }
    */
}
