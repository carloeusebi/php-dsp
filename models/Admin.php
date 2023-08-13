<?php

namespace app\models;

use app\db\DbModel;

class Admin extends DbModel
{
    public int $id;
    public string $username;
    public string $password;

    public function save(): array
    {
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

    /**
     * Attempts login if provided with an username and a password
     * @return bool Returns TRUE if login is successful, FALSE otherwise
     */
    public function attemptLogin(string $username, string $password): bool
    {
        $table_name = $this->tableName();
        $sql = "SELECT * FROM $table_name WHERE `username` = :username AND `password` = :password";

        $statement = $this->prepare($sql);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $password);
        $statement->execute();

        return $statement->rowCount() > 0;
    }
}
