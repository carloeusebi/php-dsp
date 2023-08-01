<?php

namespace app\models;

use app\db\DbModel;
use PDO;

class Survey extends DbModel
{
    public $id;
    public $patient_id;
    public $title;
    public $questions;
    public $completed;
    public $token;

    protected array $fields_to_decode = ['questions'];


    static function tableName(): string
    {
        return 'surveys';
    }

    static function attributes(): array
    {
        return [
            'patient_id', 'title', 'questions', 'completed', 'id', 'token'
        ];
    }

    static function labels(): array
    {
        return [];
    }

    protected static function joins(): string
    {
        return ' JOIN patients AS P ON surveys.patient_id = P.id ';
    }


    public function get(string $fields = '*')
    {
        $tableName = $this->tableName();
        $fields = " $tableName.*, P.id AS patient_id, P.fname, P.lname, P.email, P.age, P.weight, P.height, P.job, P.cohabitants";

        return parent::get($fields);
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
            if ($this->id) self::update();
            else {
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
