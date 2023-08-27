<?php

use app\app\App;
use app\db\factories\BaseFactory;
use app\models\Question;

class QuestionsFactory extends BaseFactory
{
    public const TABLE_NAME = 'questions';
    private const FILE_PATH = '/db/seeds/questions.csv';

    public function generateAndInsert(): void
    {
        $csv_file = fopen(App::$ROOT_DIR . self::FILE_PATH, 'r');
        $number_of_inserts = 0;
        $first_line = true;

        while (($data = fgetcsv($csv_file, separator: ',')) !== false) {
            if (!$first_line) {
                $new_question = new Question();

                $new_question->question = $data[1];
                $new_question->description = $data[2];
                $new_question->type = $data[3];
                $new_question->items = $data[4];
                $new_question->legend = $data[5];
                $new_question->variables = $data[6];

                $new_question->save();
                $number_of_inserts++;
            }
            $first_line = false;
        }

        if ($number_of_inserts)
            echo "$number_of_inserts Questions inserted successfully!\n";
        else
            echo "No Questions inserted\n";
    }
}
