<?php

use app\app\App;

class m0003_create_questions_table
{

  public function up()
  {
    $db = App::$app->db;

    $SQL =  "CREATE TABLE IF NOT EXISTS `questions` (
      `id` int NOT NULL AUTO_INCREMENT,
      `question` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `type` varchar(3) NOT NULL,
      `items` text DEFAULT NULL,
      `legend` text DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `Title` (`question`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    $db->execute($SQL);
  }
}
