<?php

use app\App;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

set_error_handler("core\ErrorHandler::handleError");
set_exception_handler("core\ErrorHandler::handleCliException");

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = new App(__DIR__);

$app->db->applyMigrations();
