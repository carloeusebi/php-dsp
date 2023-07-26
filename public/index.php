<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\app\App;
use Dotenv\Dotenv;

set_exception_handler("app\core\ErrorHandler::handleException");

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$app = new App(dirname(__DIR__));

require_once $app::$ROOT_DIR . '/routes/web.php';
require_once $app::$ROOT_DIR . '/routes/api.php';

$app->run();
