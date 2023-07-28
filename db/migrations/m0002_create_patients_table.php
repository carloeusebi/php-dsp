<?php

use app\app\App;

class m0002_create_patients_table
{

  public function up()
  {
    $db = App::$app->db;

    $SQL =  "CREATE TABLE IF NOT EXISTS `patients` (
      `id` int NOT NULL AUTO_INCREMENT,
      `fname` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
      `lname` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
      `age` tinyint DEFAULT NULL,
      `sex` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
      `birthday` date DEFAULT NULL,
      `birthplace` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
      `address` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
      `codice_fiscale` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
      `begin` date DEFAULT NULL,
      `email` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
      `phone` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
      `consent` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
      `weight` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
      `height` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
      `job` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
      `cohabitants` text COLLATE utf8mb4_general_ci,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    $db->execute($SQL);
  }
}
