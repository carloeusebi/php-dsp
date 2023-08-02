<?php

use app\app\App;

class m0010_create_files_table
{

    public function up()
    {
        $sql = "CREATE TABLE `files` (
            `id` int NOT NULL AUTO_INCREMENT,
            `patient_id` int NOT NULL,
            `path` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
            `type` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `patient_fk` (`patient_id`),
            CONSTRAINT `patient_fk` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        App::$app->db->execute($sql);
    }
}
