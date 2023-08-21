<?php

use app\app\App;
use app\db\factories\BaseFactory;

class TagsFactory extends BaseFactory
{
    public const TABLE_NAME = 'tags';

    private const FILE_PATH = __DIR__ . '/sql/tags.sql';
    private const RELATION_FILE_PATH = __DIR__ . '/sql/question_tag.sql';
    private const GITHUB_URL = 'https://github.com/carloeusebi/php-vue-dsp';

    public function generateAndInsert(): void
    {
        $file = file_exists(self::FILE_PATH);

        if (!$file) {
            echo "File tags.sql not found, download it from github " . self::GITHUB_URL;
            return;
        }

        $sql = file_get_contents(self::FILE_PATH);
        $number_of_inserts = App::$app->db->execute($sql);

        if ($number_of_inserts)
            echo "$number_of_inserts Tags inserted successfully!\n";
        else
            echo "No Tags inserted\n";

        // creates question_tags relation table
        if (file_exists(self::RELATION_FILE_PATH)) {
            $sql = file_get_contents(self::RELATION_FILE_PATH);
            App::$app->db->execute($sql);

            echo "Created question_tag relations!\n";
        }
    }
}
