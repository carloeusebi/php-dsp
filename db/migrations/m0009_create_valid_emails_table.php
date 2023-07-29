<?php

use app\app\App;

class m0009_create_valid_emails_table
{

    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `emails`(
            `id` int NOT NULL AUTO_INCREMENT,
            `email` varchar(80) NOT NULL,
            `validation_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY(`id`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        App::$app->db->execute($sql);
    }
}
