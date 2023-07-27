<?php

namespace app\models;

use app\core\Mail;
use app\db\DbModel;
use app\core\utils\CodiceFiscale;

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
    public $consent;
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
            'fname', 'lname', 'age', 'birthday', 'birthplace', 'address', 'codice_fiscale', 'begin', 'email', 'phone', 'consent', 'weight', 'height', 'job', 'sex', 'cohabitants', 'id'
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
            'consent' => 'Consenso',
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


    public function save(): array
    {
        $errors = [];

        $this->begin = $this->begin  ? $this->begin : date("Y-m-d", time());
        $fileToUpload = $_FILES['consent'] ?? null;

        if ($this->sex)
            $this->sex = strtoupper(substr($this->sex, 0, 1)); // if sex should arrive with more than one characters, it takes only first char to uppercase

        // validates the codice fiscale
        $is_invalid_cf = false;
        if ($this->codice_fiscale) $is_invalid_cf = CodiceFiscale::validate($this->codice_fiscale);

        // check for errors
        if (!$this->fname) $errors['fname'] = "Il nome è obbligatorio.";
        if (!$this->lname) $errors['lname'] = "Il cognome è obbligatorio.";
        if (!$this->birthday) $errors['birthday'] = "La data di nascita è obbligatoria.";
        if (!$this->isRealDate($this->birthday)) $errors['birthday'] = "Data di nascita non valida";
        if ($this->isAgeInvalid()) $errors['age'] = "{$this->age} non è un'età valida";
        if (!$this->begin) $errors['begin'] = "La data di inizio terapia è obbligatoria.";
        if (!$this->isRealDate($this->begin)) $errors['begin'] = "Data di inizio terapia non è valida";
        if ($this->email && Mail::isUndeliverable($this->email)) $errors['email'] = Mail::UNDELIVERABLE_ERROR_MESSAGE;
        if ($is_invalid_cf) $errors['codice_fiscale'] = $is_invalid_cf;
        if ($fileToUpload) {
            if ($this->isPdf($fileToUpload)) $errors['not-pdf'] = "Si possono caricare solamente files in formato PDF";
            if (!isset($fileToUpload['name']) || $fileToUpload['name'] === '') $errors['invalid-name'] = "Nome del file non valido";
        }

        if (empty($errors)) {
            // file upload; only if there are no errors
            $this->consent = $fileToUpload ? $this->uploadFile($fileToUpload) : $this->consent;

            if ($this->id) self::update();
            else self::create();
        }

        return $errors;
    }

    protected function isAgeInvalid()
    {
        return $this->age < 0 || $this->age > 120;
    }


    protected function isPdf($file): bool
    {
        return isset($file['type']) && $file['type'] !== 'application/pdf' && ($file['type']) !== '';
    }


    protected function isRealDate(string $date): bool
    {
        if (!strtotime($date))
            return false;
        list($year, $month, $day) = explode('-', $date);

        return checkdate($month, $day, $year);
    }
}
