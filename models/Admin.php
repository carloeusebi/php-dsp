<?php

namespace app\models;

use app\db\DbModel;

class Admin extends DbModel
{
    public int $id;
    public string $username;
    public string $password;

    public static function tableName(): string
    {
        return 'admins';
    }

    static function attributes(): array
    {
        return ['id', 'username', 'password'];
    }

    static function labels(): array
    {
        return [];
    }

    protected static function joins(): string
    {
        return '';
    }
}
