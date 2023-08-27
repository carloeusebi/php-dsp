<?php

use app\app\App;

class m0002_create_patients_table
{

  public function up()
  {
    $db = App::$app->db;

    $SQL =  "CREATE TABLE IF NOT EXISTS `patients` (
          `id` bigint unsigned NOT NULL AUTO_INCREMENT,
          `fname` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
          `lname` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
          `sex` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `birthday` date DEFAULT NULL,
          `birthplace` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `address` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `codice_fiscale` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `begin` date DEFAULT NULL,
          `email` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `weight` smallint unsigned DEFAULT NULL,
          `height` tinyint unsigned DEFAULT NULL,
          `qualification` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `job` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `cohabitants` text COLLATE utf8mb4_unicode_ci,
          `drugs` text COLLATE utf8mb4_unicode_ci,
          `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->execute($SQL);
  }
}
