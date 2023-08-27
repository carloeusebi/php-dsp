<?php

use app\app\App;

class m0005_create_tags_table
{
    public function up()
    {
        $db = App::$app->db;

        $SQL = "CREATE TABLE IF NOT EXISTS `tags` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `tag` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
            `color` char(7) COLLATE utf8mb4_unicode_ci NOT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $db->execute($SQL);
    }
}
