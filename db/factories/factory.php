<?php

namespace app\db\factories;

use app\app\App;

require_once __DIR__ . '/../../vendor/autoload.php';

set_exception_handler('app\core\exceptions\ErrorHandler::handleCliException');

$app = new App();

$number_of_patients = readline("Enter number of patients to generate: ");

$patients_factory = new PatientsFactory();
$patients_factory->generateAndInsert($number_of_patients);
