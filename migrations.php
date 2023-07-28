<?php

use app\app\App;

require_once __DIR__ . '/vendor/autoload.php';

set_exception_handler('app\core\exceptions\ErrorHandler::handleCliException');

$app = new App();

$app->db->applyMigrations();
