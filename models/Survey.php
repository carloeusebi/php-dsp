<?php

namespace models;

use db\DbModel;
use PDO;

class Survey extends DbModel
{
    public $id;
    public $patient_id;
    public $title;
    public $questions;
    public $created_at;
    public $last_update;
    public $completed;
    public $token;

    protected array $fields_to_decode = ['questions'];


    public static function tableName(): string
    {
        return 'surveys';
    }

    public function attributes(): array
    {
        return [
            'patient_id', 'title', 'questions', 'created_at', 'last_update', 'completed', 'id', 'token'
        ];
    }

    public function labels(): array
    {
        return [];
    }

    public function getByToken(string $token)
    {
        $tableName = $this->tableName();
        $statement = self::prepare("SELECT * FROM $tableName WHERE token = :token");
        $statement->bindValue('token', $token);
        $statement->execute();

        return $this->decodeOne($statement->fetch(PDO::FETCH_ASSOC));
    }


    public function save(): array
    {
        $errors = [];

        // check for errors

        if (!$this->title) $errors['title'] = 'Il nome per il Sondaggio è obbligatorio';
        if (!$this->patient_id) $errors['patient_id'] = 'Nessun Paziente selezionato, il Paziente è obbligatorio';
        if (!$this->questions) $errors['questions'] = 'Nessun questionario selezionato, selezionarne almeno uno';

        $this->questions = json_encode($this->questions);

        if (empty($errors)) {
            if ($this->id) {
                $this->last_update = date("y-m-d", time());
                self::update();
            } else {
                $this->created_at = date("y-m-d", time());
                $this->token = $this->generateToken();
                self::create();
            }
        }
        return $errors;
    }


    protected function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }
}
