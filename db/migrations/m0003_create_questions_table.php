<?php

use app\App;

class m0003_create_questions_table
{

  public function up()
  {
    $db = App::$app->db;

    $SQL =  "CREATE TABLE IF NOT EXISTS `questions` (
      `id` int NOT NULL AUTO_INCREMENT,
      `question` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `type` varchar(80) NOT NULL,
      `items` text,
      `legend` text,
      `tags` text,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    $db->execute($SQL);
  }
}
