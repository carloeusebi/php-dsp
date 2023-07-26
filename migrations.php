<?php

use app\app\App;

require_once __DIR__ . '/vendor/autoload.php';

set_error_handler("app\core\ErrorHandler::handleError");
set_exception_handler("app\core\ErrorHandler::handleCliException");

$app = new App();

$app->db->applyMigrations();
