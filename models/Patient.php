<?php

namespace app\models;

use app\db\DbModel;

class Patient extends DbModel
{
    public $id;
    public $fname;
    public $lname;
    public $age;
    public $birthday;
    public $birthplace;
    public $address;
    public $fiscalcode;
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

    public function attributes(): array
    {
        return [
            'fname', 'lname', 'age', 'birthday', 'birthplace', 'address', 'fiscalcode', 'begin', 'email', 'phone', 'consent', 'weight', 'height', 'job', 'sex', 'cohabitants', 'username', 'id'
        ];
    }

    public function labels(): array
    {
        return [
            'id' => 'id',
            'fname' => 'Nome',
            'lname' => 'Cognome',
            'age' => 'Età',
            'birthday' => 'Data di nascita',
            'birthplace' => 'Luogo di nascita',
            'address' => 'Indirizzo',
            'fiscalcode' => 'Codice Fiscale',
            'begin' => 'Data di inizio Terapia',
            'email' => 'Email',
            'phone' => 'Numero di Telefono',
            'consent' => 'Consenso',
            'weight' => 'Peso',
            'height' => 'Altezza',
            'job' => 'Occupazione',
            'sex' => 'Sesso',
            'cohabitants' => 'Conviventi',
            'username' => 'Username'
        ];
    }

    public function save(): array
    {
        $errors = [];

        $this->begin = $this->begin  ? $this->begin : date("Y-m-d", time());
        $fileToUpload = $_FILES['consent'] ?? null;

        if ($this->sex)
            $this->sex = strtoupper($this->sex);

        if ($this->checkIfExists()) $errors['exists'] = 'Un Paziente con questo nome esiste già!!';

        // check for errors
        if (!$this->fname) $errors['fname'] = "Il nome è obbligatorio.";
        if (!$this->lname) $errors['lname'] = "Il cognome è obbligatorio.";
        if (!$this->birthday) $errors['birthday'] = "La data di nascita è obbligatoria.";
        if (!$this->isRealDate($this->birthday)) $errors['birthday'] = "Data di nascita non valida";
        if (!$this->begin) $errors['begin'] = "La data di inizio terapia è obbligatoria.";
        if (!$this->isRealDate($this->begin)) $errors['begin'] = "Data di inizio terapia non è valida";

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


    protected function checkIfExists(): bool
    {
        $patients = self::get();
        foreach ($patients as $patient) {
            if ($this->fname === $patient['fname'] && $this->lname === $patient['lname'] && $this->id != $patient['id']) return true;
        }
        return false;
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
