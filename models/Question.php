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

        // Maps t
        return array_map(function ($question) {
            $question['tags'] = $this->getQuestionTags($question['id']);
            return $question;
        }, $questions);
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

        dd($this->tags);

        if (empty($errors)) {
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


    protected function checkIfExists(): bool
    {
        $questions = self::get();
        foreach ($questions as $question) {
            if ($this->question === $question['question'] && $this->id != $question['id']) return true;
        }
        return false;
    }
}
