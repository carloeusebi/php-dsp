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

    protected array $fields_to_decode = ['legend', 'items'];


    public static function tableName(): string
    {
        return 'questions';
    }


    public function attributes(): array
    {
        return ['question', 'description', 'type', 'items', 'legend', 'id'];
    }


    public function labels(): array
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


    public function save(): array
    {
        $errors = [];

        if ($this->checkIfExists()) $errors['exists'] = 'Una Domanda con questo nome esiste già!!';

        if (!$this->question) $errors['question'] = "Il nome del questionario obbligatorio";
        if (!$this->description) $errors['description'] = "La descrizione è obbligatoria";
        if (!$this->type) $errors['type'] = "Il tipo della domanda è obbligatorio";

        $this->legend = json_encode($this->legend);
        $this->items = json_encode($this->items);

        if (empty($errors)) {
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
