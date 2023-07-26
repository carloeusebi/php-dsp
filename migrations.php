<?php

use app\app\App;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

set_error_handler("app\core\ErrorHandler::handleError");
set_exception_handler("app\core\ErrorHandler::handleCliException");

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = new App(__DIR__);

$app->db->applyMigrations();
