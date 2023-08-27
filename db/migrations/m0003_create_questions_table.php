<?php

use app\app\App;

class m0003_create_questions_table
{

  public function up()
  {
    $db = App::$app->db;

    $SQL =  "CREATE TABLE IF NOT EXISTS `questions` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT,
        `question` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
        `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
        `type` char(3) COLLATE utf8mb4_unicode_ci NOT NULL,
        `items` text COLLATE utf8mb4_unicode_ci,
        `legend` text COLLATE utf8mb4_unicode_ci,
        `variables` text COLLATE utf8mb4_unicode_ci,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->execute($SQL);
  }
}
