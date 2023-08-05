<?php

use app\app\App;

class m0012_add_variables_to_question_table
{

    public function up()
    {
        $sql = "ALTER TABLE `questions`
            ADD COLUMN `variables` text DEFAULT NULL
            AFTER `legend`";

        App::$app->db->execute($sql);
    }
}
