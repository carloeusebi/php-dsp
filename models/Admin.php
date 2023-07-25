<?php

namespace models;

use db\DbModel;

class Admin extends DbModel
{
    public int $id;
    public string $username;
    public string $password;

    public static function tableName(): string
    {
        return 'admins';
    }

    public function attributes(): array
    {
        return ['id', 'username', 'password'];
    }

    public function labels(): array
    {
        return [];
    }

    public function save(): array
    {
        return [];
    }
}
