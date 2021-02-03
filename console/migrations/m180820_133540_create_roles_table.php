<?php

use yii\db\Migration;
use common\models\User;

/**
 * Handles the creation of table `roles`.
 */
class m180820_133540_create_roles_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->createTable('roles', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->insert('roles', [
            'id' => User::ROLE_DEFAULT,
            'name' => 'default',
        ]);

        $this->insert('roles', [
            'id' => User::ROLE_ADMIN,
            'name' => 'admin',
        ]);

        $this->insert('roles', [
            'id' => User::ROLE_MANAGER,
            'name' => 'manager',
        ]);

        $this->insert('roles', [
            'id' => User::ROLE_ACCOUNTANT,
            'name' => 'accountant',
        ]);

        $this->insert('roles', [
            'id' => User::ROLE_FIN_DIRECTOR,
            'name' => 'fin_director',
        ]);

        $this->insert('roles', [
            'id' => User::ROLE_SALES_MANAGER,
            'name' => 'sales_manager',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('roles');
    }



}
