<?php

use app\app\App;
use app\db\Database;
use app\db\factories\BaseFactory;
use app\models\Tag;

class TagsFactory extends BaseFactory
{
    public const TABLE_NAME = 'tags';
    private const FILE_PATH = '/db/seeds/tags.csv';
    private const RELATION_FILE_PATH = '/db/seeds/question_tag.csv';


    public function generateAndInsert(): void
    {
        $csv_file = fopen(App::$ROOT_DIR . self::FILE_PATH, 'r');
        $number_of_inserts = 0;

        $first_line = true;
        while (($data = fgetcsv($csv_file, separator: ',')) !== false) {
            if (!$first_line) {
                $new_tag = new Tag();

                $new_tag->tag = $data[1];
                $new_tag->color = $data[2];

                $new_tag->save();
                $number_of_inserts++;
            }
            $first_line = false;
        }

        if ($number_of_inserts)
            echo "$number_of_inserts Tags inserted successfully!\n";
        else
            echo "No Tags inserted\n";

        // creates question_tags relation table
        if (file_exists(App::$ROOT_DIR . self::RELATION_FILE_PATH)) {
            $this->createQuestionTagPivot();
            echo "Created question_tag relations!\n";
        }
    }

    private function createQuestionTagPivot()
    {
        $csv_file = fopen(App::$ROOT_DIR . self::RELATION_FILE_PATH, 'r');

        $first_line = true;
        while (($data = fgetcsv($csv_file, separator: ',')) !== false) {
            if (!$first_line) {
                $question_id = $data[0];
                $tag_id = $data[1];

                Database::table('question_tag')->insert(['question_id' => $question_id, 'tag_id' => $tag_id]);
            }
            $first_line = false;
        }
    }
}
