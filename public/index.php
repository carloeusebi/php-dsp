<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\app\App;

set_exception_handler('app\core\exceptions\ErrorHandler::handleException');

$app = new App();

require_once $app::$ROOT_DIR . '/routes/web.php';
require_once $app::$ROOT_DIR . '/routes/api.php';

$app->run();
