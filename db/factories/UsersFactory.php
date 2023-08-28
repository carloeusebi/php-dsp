<?php

use app\db\factories\BaseFactory;
use app\models\User;

class UsersFactory extends BaseFactory
{
    public const TABLE_NAME = 'admins';

    public function generateAndInsert(): void
    {
        $user = new User();

        // generates dummy credentials; email: 'admin', password: 'admin'
        $email = 'admin';
        $password = password_hash('admin', PASSWORD_BCRYPT, ['cost' => 12]);

        $user->email = $email;
        $user->password = $password;

        $user->save();

        echo 'Generated dummy admin.' . PHP_EOL;
    }
}
