<?php

use app\db\factories\BaseFactory;
use app\models\Admin;

class AdminsFactory extends BaseFactory
{
    public const TABLE_NAME = 'admins';

    public function generateAndInsert(): void
    {
        $admin = new Admin();

        // generates dummy credentials; username: 'admin', password: 'admin'
        $username = 'admin';
        $password = password_hash('admin', PASSWORD_BCRYPT, ['cost' => 12]);

        $admin->username = $username;
        $admin->password = $password;

        $admin->save();

        echo 'Generated dummy admin.' . PHP_EOL;
    }
}
