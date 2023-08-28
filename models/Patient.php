<?php

namespace app\models;

use app\app\App;
use app\db\DbModel;
use app\core\utils\CodiceFiscale;

class Patient extends DbModel
{
    public $id;
    public $fname;
    public $lname;
    public $birthday;
    public $birthplace;
    public $address;
    public $codice_fiscale;
    public $begin;
    public $email;
    public $phone;
    public $weight;
    public $height;
    public $job;
    public $qualification;
    public $sex;
    public $drugs;
    public $cohabitants;

    protected array $fields_to_decode = [];

    public static function tableName(): string
    {
        return 'patients';
    }

    static function attributes(): array
    {
        return [
            'fname', 'lname', 'birthday', 'birthplace', 'address', 'codice_fiscale', 'begin', 'email', 'phone', 'weight', 'height', 'job', 'qualification', 'sex', 'cohabitants', 'drugs', 'id'
        ];
    }

    static function labels(): array
    {
        return [
            'id' => 'id',
            'fname' => 'Nome',
            'lname' => 'Cognome',
            'age' => 'Età',
            'birthday' => 'Data di nascita',
            'birthplace' => 'Luogo di nascita',
            'address' => 'Indirizzo',
            'codice_fiscale' => 'Codice Fiscale',
            'begin' => 'Data di inizio Terapia',
            'email' => 'Email',
            'phone' => 'Numero di Telefono',
            'weight' => 'Peso',
            'height' => 'Altezza',
            'qualification' => 'Titolo di Studio',
            'job' => 'Occupazione',
            'sex' => 'Sesso',
            'cohabitants' => 'Conviventi',
            'drugs' => 'Farmaci'
        ];
    }


    protected static function joins(): string
    {
        return '';
    }

    /**
     * Fetches patient and file relation
     */
    public function get(array $columns = [], array $where = [], string $joins = '', string $order = 'ORDER BY `id` ASC'): array
    {
        return array_map(function ($patient) {
            // file_patient relations
            $file_where = ['patient_id' => $patient['id']];
            $patient['files'] = App::$app->file->get(where: $file_where);
            return $patient;
        }, parent::get());
    }


    public function save(): array
    {
        $errors = [];

        /**
         * Checks for errors and data manipulation
         */

        // name
        if (!$this->fname) $errors['fname'] = "Il nome è obbligatorio.";
        if (!$this->lname) $errors['lname'] = "Il cognome è obbligatorio.";

        //if sex should arrive with more than one characters, it takes only first char to uppercase
        if ($this->sex)
            $this->sex = strtoupper(substr($this->sex, 0, 1));

        //birthday
        if ($this->birthday && ($this->isNotRealDate($this->birthday) || $this->ageIsInvalid($this->birthday))) $errors['birthday'] = "Data di nascita non valida.";

        // date of therapy start
        $this->begin = $this->begin  ? $this->begin : date("Y-m-d", time()); // if no previous date was submitted start of therapy is considered now
        if (!$this->begin) $errors['begin'] = "La data di inizio terapia è obbligatoria.";
        if ($this->isNotRealDate($this->begin)) $errors['begin'] = "Data di inizio terapia non è valida.";
        if ($this->isDateInFuture($this->begin)) $errors['begin'] = 'La data di inizio terapia non può essere nel futuro.';

        // email
        if ($this->email && Mail::isUndeliverable($this->email, false, true)) $errors['email'] = Mail::UNDELIVERABLE_ERROR_MESSAGE;

        // validates the codice fiscale
        $is_invalid_cf = false;
        if ($this->codice_fiscale)
            $is_invalid_cf = CodiceFiscale::validate($this->codice_fiscale);
        if ($is_invalid_cf) $errors['codice_fiscale'] = $is_invalid_cf;

        if (empty($errors)) {
            if ($this->id) self::update();
            else self::create();
        }

        return $errors;
    }

    /**
     * Checks if the given date is an actual real date that won't break the database
     */
    protected function isNotRealDate(string $date): bool
    {
        if (!strtotime($date))
            return false;
        list($year, $month, $day) = explode('-', $date);

        return !checkdate($month, $day, $year);
    }

    protected function isDateInFuture(string $date)
    {
        $now = time();
        $date_to_check = strtotime($date);

        return $date_to_check > $now;
    }

    protected function ageIsInvalid(string $birthday)
    {
        $age = calculateAge($birthday);
        return $age <= 0 || $age > 120;
    }
}
