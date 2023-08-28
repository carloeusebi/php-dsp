<?php

use app\app\App;

class m0010_create_files_table
{

    public function up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `files` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `patient_id` bigint unsigned NOT NULL,
            `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
            `type` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
            `path` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `files_patient_id_foreign` (`patient_id`),
            CONSTRAINT `files_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        App::$app->db->execute($sql);
    }
}
