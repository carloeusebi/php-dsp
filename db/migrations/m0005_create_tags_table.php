<?php

use app\App;

class m0005_create_tags_table
{
    public function up()
    {
        $db = App::$app->db;

        $SQL = "CREATE TABLE IF NOT EXISTS `tags` (
            `id` int NOT NULL AUTO_INCREMENT,
            `tag` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $db->execute($SQL);
    }
}
