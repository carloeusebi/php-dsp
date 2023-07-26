<?php

namespace app\core\utils;

class Response
{
    static function statusCode(int $http_code): void
    {
        http_response_code($http_code);
    }

    static function redirect(string $url): void
    {
        header("Location: $url");
    }

    static function response(int $http_code, array|string $messages = []): never
    {
        self::statusCode($http_code);
        if (!empty($messages)) {
            echo json_encode($messages, JSON_NUMERIC_CHECK);
        }
        exit();
    }
}
