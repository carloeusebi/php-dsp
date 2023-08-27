<?php

use app\app\App;

class m0014_create_personal_access_tokens_table
{
    public function up()
    {
        $db = App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `tokenable_id` bigint unsigned NOT NULL,
                `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
                `abilities` text COLLATE utf8mb4_unicode_ci,
                `last_used_at` timestamp NULL DEFAULT NULL,
                `expires_at` timestamp NULL DEFAULT NULL,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
                KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $db->execute($sql);
    }
}
