<?php

namespace core;

use core\utils\Response;
use ErrorException;
use Throwable;

class ErrorHandler
{

    static function handleApiException(Throwable $exception)
    {
        Response::response(500, [
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine(),
        ]);
    }

    static function handleCliException(Throwable $exception): never
    {
        echo "\033[2J\033[;H";
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
        throw new ErrorException($errStr, 0, $errN, $errFile, $errLine);
    }
}
