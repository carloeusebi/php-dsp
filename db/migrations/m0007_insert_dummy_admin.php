<?php

use app\app\App;

class m0007_insert_dummy_admin
{
    public function up()
    {
        $db = App::$app->db;

        $SQL = "INSERT INTO `admins` (`id`, `username`, `password`) VALUES (NULL, 'admin', 'admin');";

        $db->execute($SQL);
    }
}
