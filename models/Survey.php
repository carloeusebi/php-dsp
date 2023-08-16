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
        return 'JOIN patients AS P ON surveys.patient_id = P.id ';
    }


    /**
     * @return array An array of default columns, contains all Surveys columns but the `questions` column, and `id`, `fname`, `lname`, `phone` and `email` from the Patients table.
     */
    protected static function columns(): array
    {
        $table_name = self::tableName();
        return ["$table_name.`id`", "$table_name.`patient_id`", "$table_name.`title`", "$table_name.`created_at`", "$table_name.`last_update`", "$table_name.`completed`", "$table_name.`token`", 'P.id AS patient_id', 'P.fname', 'P.lname', 'P.phone', 'P.email'];
    }


    public function get(array $columns = [], array $where = [], string $joins = '')
    {
        // if columns are passed as param, sets column to the param, otherwise sets column to return value of the function columns
        $columns = count($columns) ? $columns : $this->columns();
        $joins = $this->joins();

        return parent::get($columns, $where, $joins);
    }


    public function getById(int $id, string $joins = '')
    {
        $joins = $this->joins();

        $result = parent::getById($id, $joins);
        $result['id'] = $id;
        return $result;
    }


    /**
     * Fetches all Survey and Patient columns from the db of the survey with tha same token as the one passed as parameter.
     * 
     * @param string $token The token of the Survey to fetch.
     */
    public function getByToken(string $token)
    {
        $columns = [...$this->columns(), '`surveys`.`questions`'];
        $where = ['token' => $token];
        return $this->get($columns, $where)[0];
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


    public function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }
}
