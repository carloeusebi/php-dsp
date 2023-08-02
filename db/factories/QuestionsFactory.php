<?php

use app\app\App;
use app\db\factories\BaseFactory;

class QuestionsFactory extends BaseFactory
{
    public const TABLE_NAME = 'questions';

    private const FILE_PATH = __DIR__ . '/sql/questions.sql';
    private const GITHUB_URL = 'https://github.com/carloeusebi/php-vue-dsp';

    public function generateAndInsert(): void
    {
        $file = file_exists(self::FILE_PATH);

        if (!$file) {
            echo "File questions.sql not found, download it form github " . self::GITHUB_URL . PHP_EOL;
            return;
        }

        $sql = file_get_contents(self::FILE_PATH);
        $number_of_inserts = App::$app->db->execute($sql);

        if ($number_of_inserts)
            echo "$number_of_inserts Questions inserted successfully!\n";
        else
            echo "No Questions inserted\n";
    }
}
