<?php

namespace app\models;

use app\db\DbModel;

class Tag extends DbModel
{
    public $id;
    public $tag;
    public $color;

    protected const REL_TABLE = '`question_tag`';
    protected array $fields_to_decode = [];

    public static function tableName(): string
    {
        return 'tags';
    }

    static function attributes(): array
    {
        return ['id', 'tag', 'color'];
    }

    static function labels(): array
    {
        return [];
    }


    public function save(): array
    {
        $errors = [];

        if (!$this->tag) $errors['tag'] = "Manca il nome del tag";
        if (!$this->color) $errors['color'] = "Manca il colore del tag";

        if (empty($errors))
            if ($this->id)
                $this->update();
            else
                $this->create();

        return $errors;
    }


    /**
     * Adds tag relationships to associate tags with a specific question.
     * @param int $question_id The ID of the question to which the tags will be associated.
     * @param array $rel An array of tag IDs to establish relationships with the question.
     */
    public function addRelationships(int $question_id, array $rel)
    {
        foreach ($rel as $tag_id) {
            $sql = "INSERT INTO " . self::REL_TABLE . " (`question_id`, `tag_id`)
                    VALUES (:question_id, :tag_id)";
            $statement = $this->prepare($sql);
            $statement->bindValue(':question_id', $question_id);
            $statement->bindValue(':tag_id', $tag_id);

            $statement->execute();
        }
    }


    /**
     * Removes tag relationships associated with a specific question.
     * @param int $question_id The ID of the question from which tag relationships will be removed.
     * @param array $rel An array of tag IDs to remove relationships with the question.
     */
    public function removeRelationship(int $question_id, array $rel)
    {
        foreach ($rel as $tag_id) {
            $sql = 'DELETE FROM ' . self::REL_TABLE;
            $sql .= "WHERE `question_id` = :question_id AND `tag_id` = :tag_id";
            $statement = $this->prepare($sql);
            $statement->bindValue(':question_id', $question_id);
            $statement->bindValue(':tag_id', $tag_id);

            $statement->execute();
        }
    }
}
