<?php

namespace app\models;

use app\db\DbModel;
use app\core\utils\CodiceFiscale;
use app\core\utils\Utils;

class Patient extends DbModel
{
    public $id;
    public $fname;
    public $lname;
    public $age;
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
    public $sex;
    public $cohabitants;
    public $username;

    protected array $fields_to_decode = [];

    public static function tableName(): string
    {
        return 'patients';
    }

    static function attributes(): array
    {
        return [
            'fname', 'lname', 'age', 'birthday', 'birthplace', 'address', 'codice_fiscale', 'begin', 'email', 'phone', 'weight', 'height', 'job', 'sex', 'cohabitants', 'id'
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
            'job' => 'Occupazione',
            'sex' => 'Sesso',
            'cohabitants' => 'Conviventi',
        ];
    }


    protected static function joins(): string
    {
        return '';
    }

    /**
     * Fetch patients and updates their age 
     */
    public function get(string $fields = '*'): array
    {
        // calculates and updates the age of the patients
        return array_map(function ($patient) {
            $age = Utils::calculateAge($patient['birthday']);
            if ($age !== $patient['age']) {
                $patient['age'] = $age;
                parent::load($patient);
                parent::update();
            }
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
        if (!$this->birthday) $errors['birthday'] = "La data di nascita è obbligatoria.";
        if (!$this->isRealDate($this->birthday)) $errors['birthday'] = "Data di nascita non valida";
        if ($this->isAgeInvalid()) $errors['age'] = "{$this->age} non è un'età valida";

        // date of therapy start
        $this->begin = $this->begin  ? $this->begin : date("Y-m-d", time()); // if no previous date was submitted start of therapy is considered now
        if (!$this->begin) $errors['begin'] = "La data di inizio terapia è obbligatoria.";
        if (!$this->isRealDate($this->begin)) $errors['begin'] = "Data di inizio terapia non è valida";

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
    protected function isRealDate(string $date): bool
    {
        if (!strtotime($date))
            return false;
        list($year, $month, $day) = explode('-', $date);

        return checkdate($month, $day, $year);
    }

    /**
     * Checks if the age is valid
     */
    protected function isAgeInvalid()
    {
        return $this->age < 0 || $this->age > 120;
    }
}
