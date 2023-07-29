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

    static function response(int $http_code, array|string $messages = []): void
    {
        self::statusCode($http_code);
        if (!empty($messages)) {
            $json =  json_encode($messages, JSON_INVALID_UTF8_IGNORE);
            if ($json) echo $json;
            else self::statusCode(500);
        }
        exit;
    }
}
