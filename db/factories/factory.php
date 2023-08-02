<?php

namespace app\db\factories;

require_once __DIR__ . '/../../vendor/autoload.php';

use app\app\App;
use app\db\factories\BaseFactory;


set_exception_handler('app\core\exceptions\ErrorHandler::handleCliException');

function file_is_class(string $file_name): bool
{
    return ctype_upper(substr($file_name, 0, 1));
}

$app = new App();

$files = array_slice(scandir(__DIR__), 2);

foreach ($files as  $file) {
    if (!file_is_class($file) || $file === 'BaseFactory.php')
        continue;

    require_once __DIR__ . "/$file";
    $class_name = pathinfo($file, PATHINFO_FILENAME);

    /**
     * @var BaseFactory
     */
    $instance = new $class_name();

    try {
        $instance->generateAndInsert();
    } catch (\Exception $exception) {
        $code = $exception->getCode();
        if ($code === '42S02')
            echo 'No ' . $class_name::TABLE_NAME . " table found, make sure to run migrations first. Check README.md\n";
        else {
            echo "Code: $code\n";
            echo "Message: {$exception->getMessage()}\n";
        }
    }
}
