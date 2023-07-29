<?php

namespace app\core\exceptions;

use Throwable;
use app\core\utils\Response;

class ErrorHandler
{

    static function handleException(Throwable $exception)
    {
        self::log($exception);
        Response::response(500, [
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine(),
        ]);
    }

    static function handleCliException(Throwable $exception): never
    {
        // echo "\033[2J\033[;H";
        echo "PHP Fatal error:\n";
        echo "Code: {$exception->getCode()}\n";
        echo "Message: {$exception->getMessage()}\n";
        echo "File: {$exception->getFile()}\n";
        echo "Line: {$exception->getLine()}\n";
        die();
    }

    static function handleError(
        int $errN,
        string $errStr,
        string $errFile,
        int $errLine
    ): bool {

        if (strpos($errStr, "Implicit conversion from float 0.5 to int loses precision") !== false) {
            // Ignore this specific warning
            return false;
        }

        throw new \ErrorException($errStr, 0, $errN, $errFile, $errLine);
    }

    static function log(Throwable $e)
    {
        $file_path = __DIR__ . '/../../errors_log.txt';

        // creates file if doesn't exists
        $file_handler = fopen($file_path, 'a+');
        fclose($file_handler);

        $date = date('d-m-Y H:i:s', time());

        $content = file_get_contents($file_path);
        $prepend = "$date - Code: {$e->getCode()} - Message: {$e->getMessage()} - File: {$e->getFile()} - Line: {$e->getLine()}" . PHP_EOL;
        file_put_contents($file_path, $prepend . $content);
    }
}
