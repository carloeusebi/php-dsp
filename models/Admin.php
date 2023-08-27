<?php

namespace app\models;

use app\db\DbModel;

class Admin extends DbModel
{
    public $id;
    public $username;
    public $password;

    public function save(): array
    {
        parent::create();
        return [];
    }

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
}
