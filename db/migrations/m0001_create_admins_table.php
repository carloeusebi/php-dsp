<?php

use app\app\App;

class m0001_create_admins_table
{
    public function up()
    {
        $db = App::$app->db;

        $SQL = "CREATE TABLE IF NOT EXISTS `admins` (
            `id` int NOT NULL AUTO_INCREMENT,
            `username` varchar(80) NOT NULL,
            `password` varchar(80) NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $db->execute($SQL);
    }
}
