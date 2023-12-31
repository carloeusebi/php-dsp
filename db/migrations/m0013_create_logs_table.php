<?php

use app\app\App;

class m0013_create_logs_table
{
    public function up()
    {
        $db = App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS `logs` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
            `read_at` date DEFAULT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $db->execute($sql);
    }
}
