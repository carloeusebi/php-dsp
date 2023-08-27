<?php

use app\app\App;

class m0013_create_logs_table
{
    public function up()
    {
        $db = App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS `logs` (
                `id` int NOT NULL AUTO_INCREMENT,
                `message` text NOT NULL,
                `logged_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `read_at` date DEFAULT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $db->execute($sql);
    }
}
