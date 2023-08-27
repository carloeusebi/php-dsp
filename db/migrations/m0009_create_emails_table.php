<?php

use app\app\App;

class m0009_create_emails_table
{

    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `emails`(
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `email` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `emails_email_unique` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        App::$app->db->execute($sql);
    }
}
