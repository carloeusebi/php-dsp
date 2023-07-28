<?php

use app\app\App;

class m0008_create_issues_table
{
    public function up()
    {
        $db = App::$app->db;

        $SQL = "CREATE TABLE IF NOT EXISTS `issues`(
        `id` int NOT NULL AUTO_INCREMENT,
        `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `message` varchar(255) NOT NULL,
        `name` varchar(80) DEFAULT NULL,
        `email` varchar(30) DEFAULT NULL,
        `seen` tinyint(1) DEFAULT NULL,
        PRIMARY KEY (`id`)    
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $db->execute($SQL);
    }
}
