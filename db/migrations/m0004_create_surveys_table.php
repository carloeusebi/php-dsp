<?php

use app\app\App;

class m0004_create_surveys_table
{

  public function up()
  {
    $db = App::$app->db;

    $SQL =  "CREATE TABLE IF NOT EXISTS `surveys` (
      `id` int NOT NULL AUTO_INCREMENT,
      `patient_id` int DEFAULT NULL,
      `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
      `questions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `last_update` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      `completed` tinyint(1) DEFAULT NULL,
      `token` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `token` (`token`),
      KEY `patient_id` (`patient_id`),
      CONSTRAINT `surveys_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    $db->execute($SQL);
  }
}
