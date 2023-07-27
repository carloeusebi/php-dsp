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
        return ' LEFT JOIN patients ON surveys.patient_id = patients.id ';
    }


    public function get()
    {
        $order = $this->getOrder();
        $tableName = $this->tableName();

        $query = "SELECT $tableName.*, patients.id AS patient_id, patients.fname, patients.lname, patients.email, patients.age, patients.weight, patients.height, patients.job, patients.cohabitants";
        $query .= " FROM $tableName ";
        $query .= $this->joins();
        $query .= " ORDER BY $tableName.$order";
        $statement = $this->prepare($query);
        $statement->execute();

        return $this->decodeMany($statement->fetchAll(PDO::FETCH_ASSOC));
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
