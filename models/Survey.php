<?php

namespace app\models;

use app\app\App;
use app\db\Database;
use app\db\DbModel;

class Survey extends DbModel
{
    public $id;
    public $patient_id;
    public $title;
    public $questions;
    public $completed;
    public $created_at;
    public $updated_at;
    public $token;

    protected array $fields_to_decode = ['questions'];


    static function tableName(): string
    {
        return 'surveys';
    }

    static function attributes(): array
    {
        return [
            'patient_id', 'title', 'questions', 'completed', 'id', 'token', 'created_at', 'updated_at',
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
        return ['id', 'patient_id', 'title', 'created_at', 'updated_at', 'completed', 'token'];
    }


    public function get(array $columns = [], array $where = [], string $joins = '', string $order = 'ORDER BY `id` ASC')
    {
        $columns = [...$columns, ...$this->columns()];
        $surveys = parent::get($columns);

        //map array with patient's info
        return array_map(function ($survey) {
            $patient_id = $survey['patient_id'];
            $survey['patient'] = App::$app->patient->getById($patient_id);
            return $survey;
        }, $surveys);
    }


    public function getById(int $id, string $joins = '')
    {
        $survey = parent::getById($id);
        $survey['patient'] = App::$app->patient->getById($id);
        return $survey;
    }


    /**
     * Fetches all Survey and Patient columns from the db of the survey with tha same token as the one passed as parameter.
     * 
     * @param string $token The token of the Survey to fetch.
     */
    public function getByToken(string $token)
    {
        $columns = ['questions'];
        $where = ['token' => $token];

        $survey = $this->get($columns, $where)[0];
        $survey['patient'] = App::$app->patient->getById($survey['patient_id']);
        return $survey;
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
