<?php

namespace app\models;

use app\db\DbModel;

class User extends DbModel
{
    public $id;
    public $email;
    public $password;

    protected array $fields_to_decode = [];


    public function save(): array
    {
        parent::create();
        return [];
    }

    public static function tableName(): string
    {
        return 'users';
    }

    static function attributes(): array
    {
        return ['id', 'email', 'password'];
    }

    static function labels(): array
    {
        return [];
    }
}
