<?php

use yii\db\Migration;

/**
 * Class m180806_121304_modify_user_table
 */
class m180806_121304_modify_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('user', 'username');
        $this->addColumn('user', 'firstname', $this->string()->after('password_reset_token'));
        $this->addColumn('user', 'middlename', $this->string()->after('firstname'));
        $this->addColumn('user', 'lastname', $this->string()->after('middlename'));
        $this->addColumn('user', 'birthdate', $this->date()->after('lastname'));
        $this->addColumn('user', 'phone', $this->string()->after('birthdate'));
        $this->addColumn('user', 'viber', $this->string()->after('phone'));
        $this->addColumn('user', 'telegram', $this->string()->after('viber'));
        $this->addColumn('user', 'description', $this->string()->after('telegram'));
        $this->addColumn('user', 'image', $this->string()->after('description'));
        $this->addColumn('user', 'role', $this->integer()->after('image')->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('user', 'username', $this->string()->after('id')->notNull()->unique());
        $this->dropColumn('user', 'firstname');
        $this->dropColumn('user', 'middlename');
        $this->dropColumn('user', 'lastname');
        $this->dropColumn('user', 'birthdate');
        $this->dropColumn('user', 'phone');
        $this->dropColumn('user', 'viber');
        $this->dropColumn('user', 'telegram');
        $this->dropColumn('user', 'description');
        $this->dropColumn('user', 'image');
        $this->dropColumn('user', 'role');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180806_121304_modify_user_table cannot be reverted.\n";

        return false;
    }
    */
}
