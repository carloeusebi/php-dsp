<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\App;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$app = new App(dirname(__DIR__));

require_once $app::$ROOT_DIR . '/routes/web.php';
require_once $app::$ROOT_DIR . '/routes/api.php';

$app->run();
