<?php

namespace app\db\factories;

use app\app\App;
use DateTime;

class PatientsFactory
{
    private const TABLE_NAME = 'patients';

    private $first_names = ['Luca', 'Giulia', 'Alessandro', 'Sofia', 'Lorenzo', 'Chiara', 'Matteo', 'Giorgia', 'Davide', 'Martina', 'Marco', 'Elena', 'Federico', 'Valentina', 'Francesco', 'Aurora', 'Leonardo', 'Alessia', 'Antonio', 'Bianca'];
    private $last_names = ['Rossi', 'Bianchi', 'Ferrari', 'Esposito', 'Ricci', 'Marini', 'Conti', 'Galli', 'Barbieri', 'Romano', 'Colombo', 'Marchetti', 'Costa', 'De Luca', 'Mancini', 'Rinaldi', 'Moretti', 'Fabbri', 'Bellini', 'Rizzo', 'Leone'];

    private $sexes = ['M', 'F'];
    private $birthplaces = ['Milano', 'Roma', 'Napoli', 'Torino', 'Palermo', 'Firenze', 'Bologna', 'Genova', 'Venezia', 'Verona', 'Bari', 'Catania', 'Trieste', 'Perugia', 'Messina', 'Brescia', 'Reggio Calabria', 'Parma', 'Modena', 'Cagliari'];
    private $addresses = ['Via Garibaldi 1', 'Piazza Vittorio Emanuele 2', 'Corso Italia 3', 'Viale dei Fiori 4', 'Largo XX Settembre 5'];
    private $phones = ['333-1234567', '347-9876543', '320-4567890', '329-5678901', '338-9876543'];
    private $jobs = ['Ingegnere', 'Insegnante', 'Medico', 'Avvocato', 'Artista', 'Chef', 'Scrittore', 'Studente', 'Architetto', 'Pilota', 'Infermiere', 'Psicologo', 'Fotografo', 'Designer', 'Musicista', 'Ricercatore'];

    /**
     * Generates 100 random entries
     */
    private function randomDate(int $start, int $end): string
    {
        $timestamp = mt_rand($start, $end);
        return date('Y-m-d', $timestamp);
    }


    /**
     * Calculates age based on birthday;
     */
    private function calculateAge(string $birthday): int
    {
        $today = new DateTime();
        $birth_date = new DateTime($birthday);
        $age_interval = $today->diff($birth_date);
        return $age_interval->y;
    }

    /**
     * Return a random item fro an array
     */
    private function randomItem(array $arr): string
    {
        return $arr[array_rand($arr)];
    }

    /**
     * Function to print a simple text-based progress bar
     */
    private function printProgressBar(int $current, int $total, int $barWidth = 50): void
    {
        $progress = $current / $total;
        $barLength = (int) ($progress * $barWidth);

        echo "\r[";
        echo str_repeat('#', $barLength);
        echo str_repeat(' ', $barWidth - $barLength);
        echo "] " . round($progress * 100, 2) . "%";
    }


    public function generateAndInsert(int $number_of_patients): void
    {
        for ($i = 1; $i <= $number_of_patients; $i++) {
            $fname = $this->randomItem($this->first_names);
            $lname = $this->randomItem($this->last_names);
            $sex = $this->randomItem($this->sexes);
            $birthday = $this->randomDate(strtotime('-90 years'), strtotime('-18 years'));
            $age = $this->calculateAge($birthday);
            $birthplace = $this->randomItem($this->birthplaces);
            $address = $this->randomItem($this->addresses);
            $begin = $this->randomDate(strtotime('-5 years'), strtotime('now'));
            $email = strtolower($fname) . strtolower($lname) . "@example.com";
            $phone = $this->randomItem($this->phones);
            $consent = '';
            $codice_fiscale = 'CF' . str_pad($i, 14, '0', STR_PAD_LEFT); // Generating a unique code for demonstration
            $weight = mt_rand(50, 100);
            $height = mt_rand(150, 190);
            $job = $this->randomItem($this->jobs);
            $cohabitants = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim, error praesentium corporis, fuga earum nisi dolor porro cumque accusamus nesciunt voluptatibus, eos commodi pariatur recusandae suscipit? Maxime aliquid earum voluptatum.';

            $sql = "INSERT INTO " . self::TABLE_NAME . " (`fname`, `lname`, `age`, `sex`, `birthday`, `birthplace`, `address`, `codice_fiscale`, `begin`, `email`, `phone`, `consent`, `weight`, `height`, `job`, `cohabitants`)
                    VALUES (:fname, :lname, :age, :sex, :birthday, :birthplace, :address, :codice_fiscale, :begin, :email, :phone, :consent, :weight, :height, :job, :cohabitants)";

            $statement = App::$app->db->prepare($sql);

            $statement->bindValue('fname', $fname);
            $statement->bindValue('lname', $lname);
            $statement->bindValue('age', $age);
            $statement->bindValue('sex', $sex);
            $statement->bindValue('birthday', $birthday);
            $statement->bindValue('birthplace', $birthplace);
            $statement->bindValue('address', $address);
            $statement->bindValue('codice_fiscale', $codice_fiscale);
            $statement->bindValue('begin', $begin);
            $statement->bindValue('email', $email);
            $statement->bindValue('phone', $phone);
            $statement->bindValue('consent', $consent);
            $statement->bindValue('weight', $weight);
            $statement->bindValue('height', $height);
            $statement->bindValue('job', $job);
            $statement->bindValue('cohabitants', $cohabitants);

            $statement->execute();

            $this->printProgressBar($i, $number_of_patients);
        }

        echo "\n$number_of_patients random patients generated successfully!" . PHP_EOL;
    }
}
