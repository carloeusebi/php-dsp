<?php

use app\App;

class m0002_create_patients_table
{

  public function up()
  {
    $db = App::$app->db;

    $SQL =  "CREATE TABLE IF NOT EXISTS `patients` (
    `id` int NOT NULL AUTO_INCREMENT,
    `fname` varchar(80) NOT NULL,
    `lname` varchar(80) NOT NULL,
    `age` varchar(20) DEFAULT NULL,
    `sex` varchar(2) DEFAULT NULL,
    `birthday` date DEFAULT NULL,
    `birthplace` varchar(80) DEFAULT NULL,
    `address` varchar(80) DEFAULT NULL,
    `fiscalcode` varchar(80) DEFAULT NULL,
    `begin` date DEFAULT NULL,
    `email` varchar(80) DEFAULT NULL,
    `phone` varchar(80) DEFAULT NULL,
    `consent` varchar(255) DEFAULT NULL,
    `weight` varchar(20) DEFAULT NULL,
    `height` varchar(20) DEFAULT NULL,
    `job` varchar(80) DEFAULT NULL,
    `cohabitants` text,
    `username` varchar(80) DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    $db->execute($SQL);
  }
}
