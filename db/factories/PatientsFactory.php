<?php

use app\app\App;
use app\core\utils\Utils;
use app\db\factories\BaseFactory;

class PatientsFactory extends BaseFactory
{
    public const TABLE_NAME = 'patients';

    private array $first_names = ['Luca', 'Giulia', 'Alessandro', 'Sofia', 'Lorenzo', 'Chiara', 'Matteo', 'Giorgia', 'Davide', 'Martina', 'Marco', 'Elena', 'Federico', 'Valentina', 'Francesco', 'Aurora', 'Leonardo', 'Alessia', 'Antonio', 'Bianca'];
    private array $last_names = ['Rossi', 'Bianchi', 'Ferrari', 'Esposito', 'Ricci', 'Marini', 'Conti', 'Galli', 'Barbieri', 'Romano', 'Colombo', 'Marchetti', 'Costa', 'De Luca', 'Mancini', 'Rinaldi', 'Moretti', 'Fabbri', 'Bellini', 'Rizzo', 'Leone'];

    private array $sexes = ['M', 'F'];
    private array $birthplaces = ['Milano', 'Roma', 'Napoli', 'Torino', 'Palermo', 'Firenze', 'Bologna', 'Genova', 'Venezia', 'Verona', 'Bari', 'Catania', 'Trieste', 'Perugia', 'Messina', 'Brescia', 'Reggio Calabria', 'Parma', 'Modena', 'Cagliari'];
    private array $addresses = ['Via Garibaldi 1', 'Piazza Vittorio Emanuele 2', 'Corso Italia 3', 'Viale dei Fiori 4', 'Largo XX Settembre 5'];
    private array $phones = ['333-1234567', '347-9876543', '320-4567890', '329-5678901', '338-9876543'];
    private array $jobs = ['Ingegnere', 'Insegnante', 'Medico', 'Avvocato', 'Artista', 'Chef', 'Scrittore', 'Studente', 'Architetto', 'Pilota', 'Infermiere', 'Psicologo', 'Fotografo', 'Designer', 'Musicista', 'Ricercatore'];


    public function generateAndInsert(): void
    {
        $number_of_patients = readline("How many patients do you want to generate? ");

        for ($i = 1; $i <= $number_of_patients; $i++) {
            $fname = $this->randomItem($this->first_names);
            $lname = $this->randomItem($this->last_names);
            $sex = $this->randomItem($this->sexes);
            $birthday = $this->randomDate(strtotime('-90 years'), strtotime('-18 years'));
            $age = Utils::calculateAge($birthday);
            $birthplace = $this->randomItem($this->birthplaces);
            $address = $this->randomItem($this->addresses);
            $begin = $this->randomDate(strtotime('-5 years'), strtotime('now'));
            $email = strtolower($fname) . strtolower($lname) . "@example.com";
            $phone = $this->randomItem($this->phones);
            $codice_fiscale = 'CF' . str_pad($i, 14, '0', STR_PAD_LEFT); // Generating a unique code for demonstration
            $weight = mt_rand(50, 100);
            $height = mt_rand(150, 190);
            $job = $this->randomItem($this->jobs);
            $cohabitants = 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim, error praesentium corporis, fuga earum nisi dolor porro cumque accusamus nesciunt voluptatibus, eos commodi pariatur recusandae suscipit? Maxime aliquid earum voluptatum.';

            $sql = "INSERT INTO " . self::TABLE_NAME . " (`fname`, `lname`, `age`, `sex`, `birthday`, `birthplace`, `address`, `codice_fiscale`, `begin`, `email`, `phone`,  `weight`, `height`, `job`, `cohabitants`) VALUES ('$fname', '$lname', '$age', '$sex', '$birthday', '$birthplace', '$address', '$codice_fiscale', '$begin', '$email', '$phone',  '$weight', '$height', '$job', '$cohabitants')";

            App::$app->db->execute($sql);

            $this->printProgressBar($i, $number_of_patients);
        }

        if ($number_of_patients)
            echo "\n$number_of_patients random patients generated successfully!" . PHP_EOL;
        else
            echo "\nNo Patients generated" . PHP_EOL;
    }
}
