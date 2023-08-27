<?php

namespace app\core\exceptions;

use app\app\App;
use Throwable;
use app\core\utils\Response;

class ErrorHandler
{

    static function handleException(Throwable $exception)
    {
        // logs every exception for debugging purposes
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
        self::log($exception);
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

        // Verifalia's vendor package throws lots of warning or notices, this way we just ignore them
        if (str_contains($errFile, "verifalia")) {
            return false;
        }

        throw new \ErrorException($errStr, 0, $errN, $errFile, $errLine);
    }

    /**
     * Logs the details of a Throwable object to a file.
     * @param Throwable $e The Throwable object to log.
     */
    static function log(Throwable $e)
    {
        try {
            // Create the storage folder if it doesn't exist.
            $storage_path = App::$ROOT_DIR . '/storage';
            if (!file_exists($storage_path)) {
                mkdir($storage_path);
            }

            // Create the log folder if it doesn't exist.
            $log_folder_path = $storage_path . '/logs';
            if (!file_exists($log_folder_path)) {
                mkdir($log_folder_path);
            }

            // Create the log file if it doesn't exist.
            $file_path = $log_folder_path . '/errors.log';
            if (!file_exists($file_path)) {
                touch($file_path);
            }

            $date = date('d-m-Y H:i:s', time());

            $content = file_get_contents($file_path);
            $prepend = "$date - Code: {$e->getCode()} - Message: {$e->getMessage()} - File: {$e->getFile()} - Line: {$e->getLine()}" . PHP_EOL;
            file_put_contents($file_path, $prepend . $content);
        } catch (\Exception) {
            return false;
        }
    }
}
