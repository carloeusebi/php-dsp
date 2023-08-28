<?php

use app\app\App;

class m0004_create_surveys_table
{

  public function up()
  {
    $db = App::$app->db;

    $SQL =  "CREATE TABLE IF NOT EXISTS `surveys` (
          `id` bigint unsigned NOT NULL AUTO_INCREMENT,
          `patient_id` bigint unsigned NOT NULL,
          `title` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
          `questions` longtext COLLATE utf8mb4_unicode_ci,
          `completed` tinyint(1) DEFAULT NULL,
          `token` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
          `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `surveys_token_unique` (`token`),
          KEY `surveys_patient_id_foreign` (`patient_id`),
          CONSTRAINT `surveys_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->execute($SQL);
  }
}
