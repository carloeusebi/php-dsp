<?php

namespace app\models;

use app\app\App;
use app\db\DbModel;

class Question extends DbModel
{

    public $id;
    public $question;
    public $description;
    public $type;
    public $items;
    public $legend;
    public $variables;
    public $tags;

    protected array $fields_to_decode = ['legend', 'items', 'variables'];


    public static function tableName(): string
    {
        return 'questions';
    }


    static function attributes(): array
    {
        return ['question', 'description', 'type', 'items', 'legend', 'variables', 'id'];
    }


    static function labels(): array
    {
        return
            [
                'question' => 'Nome del questionario',
                'description' => 'Descrizione',
                'type' => 'Tipo',
                'items' => 'Lista delle domande',
                'legend' => 'Legenda'
            ];
    }

    protected static function joins(): string
    {
        return '';
    }


    public function get(string $fields = '*'): array
    {
        $questions = parent::get();

        // Map each question with its associated tags
        return array_map(function ($question) {
            $question['tags'] = $this->getQuestionTags($question['id']);
            return $question;
        }, $questions);
    }


    public function getById(int $id)
    {
        $question = parent::getById($id);
        $question['tags'] = $this->getQuestionTags($question['id']);
        return $question;
    }


    public function save(): array
    {
        $errors = [];

        if ($this->checkIfExists()) $errors['exists'] = 'Una Domanda con questo nome esiste già!!';

        if (!$this->question) $errors['question'] = "Il nome del questionario obbligatorio";
        if (!$this->description) $errors['description'] = "La descrizione è obbligatoria";
        if (!$this->type) $errors['type'] = "Il tipo della domanda è obbligatorio";

        if ($this->variables) {
            $i = 1;
            foreach ($this->variables as $variable) {
                if (!isset($variable['name']) || strlen($variable['name']) === 0) {
                    $errors["variable-$i"] = "La variabile numero $i non ha un nome!";
                }
                $i++;
            }
        }

        $this->tags = $this->tags ?? [];

        if (empty($errors)) {
            $this->updateTags();

            $this->legend = json_encode($this->legend);
            $this->items = json_encode($this->items);
            $this->variables = json_encode($this->variables);

            if ($this->id) self::update();
            else self::create();
        }

        return $errors;
    }


    /**
     * Returns all the Tags that have a many-to-many relationship with the Questionnaire with the given ID.
     * @param int $question_id The Questionnaire's ID from which to fetch the Tags.
     * @return array The Questionnaire's Tags.
     */
    protected function getQuestionTags(int $question_id): array
    {
        $sql = "SELECT `tags`.*
                FROM `tags`
                JOIN `question_tag` AS QT ON QT.`tag_id` = `tags`.`id`
                WHERE QT.`question_id` = $question_id";
        $statement = $this->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * Updates the tag relationships for the current question based on its tags and existing relationships.
     * Adds new relationships and removes unnecessary relationships to keep the question's tags up to date.
     */
    protected function updateTags()
    {
        $tag = App::$app->tag;
        $tags_relationships = $this->id ? $this->getQuestionTags($this->id) : [];

        $tags_to_add = $this->getTagsToAdd($tags_relationships);
        $tag->addRelationships($this->id, $tags_to_add);

        $tags_to_remove = $this->getTagsToRemove($tags_relationships);
        $tag->removeRelationship($this->id, $tags_to_remove);
    }


    /**
     * Checks that there isn't another Questionnaire in the database with the same name.
     * @return bool True if another record is found, false otherwise.
     */
    protected function checkIfExists(): bool
    {
        $questions = self::get();
        foreach ($questions as $question) {
            if ($this->question === $question['question'] && $this->id != $question['id']) return true;
        }
        return false;
    }


    protected function getTagsToAdd(array $rel): array
    {
        return array_diff(array_column($this->tags, 'id'), array_column($rel, 'id'));
    }

    protected function getTagsToRemove(array $rel): array
    {
        return array_diff(array_column($rel, 'id'), array_column($this->tags, 'id'));
    }
}
