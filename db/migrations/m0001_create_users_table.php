<?php

use app\app\App;

class m0001_create_users_table
{
    public function up()
    {
        $db = App::$app->db;

        $SQL = "CREATE TABLE IF NOT EXISTS `users` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT,
        `email` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
        `password` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $db->execute($SQL);
    }
}
