<?php

use app\app\App;

class m0006_create_question_tag_table
{
    public function up()
    {
        $db = App::$app->db;

        $SQL = "CREATE TABLE IF NOT EXISTS `question_tag` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `question_id` bigint unsigned NOT NULL,
            `tag_id` bigint unsigned NOT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `question_tag_question_id_foreign` (`question_id`),
            KEY `question_tag_tag_id_foreign` (`tag_id`),
            CONSTRAINT `question_tag_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`),
            CONSTRAINT `question_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $db->execute($SQL);
    }
}
