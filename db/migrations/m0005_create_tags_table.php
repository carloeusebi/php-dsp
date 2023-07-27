<?php

use app\app\App;

class m0005_create_tags_table
{
    public function up()
    {
        $db = App::$app->db;

        $SQL = "CREATE TABLE IF NOT EXISTS `tags` (
                `id` int NOT NULL AUTO_INCREMENT,
                `tag` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
                CREATE TABLE IF NOT EXISTS `question_tag` (
                `question_id` int NOT NULL,
                `tag_id` int NOT NULL,
                PRIMARY KEY (`question_id`,`tag_id`),
                KEY `tag_id` (`tag_id`),
                CONSTRAINT `question_tag_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`),
                CONSTRAINT `question_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $db->execute($SQL);
    }
}
