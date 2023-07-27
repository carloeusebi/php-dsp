<?php

use app\app\App;

class m0006_create_logs_table
{
  public function up()
  {
    $db = App::$app->db;

    $SQL = "CREATE TABLE IF NOT EXISTS `logs` (
      `id` int NOT NULL AUTO_INCREMENT,
      `code` int NOT NULL,
      `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `message` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
      `seen` tinyint(1) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    $db->execute($SQL);
  }
}
