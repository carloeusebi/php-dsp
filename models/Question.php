<?php

namespace app\models;

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

        if (empty($errors)) {
            $this->legend = json_encode($this->legend);
            $this->items = json_encode($this->items);
            $this->variables = json_encode($this->variables);

            if ($this->id) self::update();
            else self::create();
        }

        return $errors;
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
