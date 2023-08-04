<?php

use app\app\App;

class m0011_add_qualification_to_patients_table
{

    public function up()
    {
        $sql = "ALTER TABLE `patients`
            ADD COLUMN `qualification` VARCHAR(80) DEFAULT NULL
            AFTER `job`";

        App::$app->db->execute($sql);
    }
}
