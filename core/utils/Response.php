<?php

namespace app\core\utils;

use app\app\App;

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

    static function json(int $http_code, array|string $messages)
    {
        header('Content-Type: application/json');

        self::statusCode($http_code);
        $json =  json_encode($messages, JSON_INVALID_UTF8_IGNORE);
        if ($json) echo $json;
        else self::statusCode(500);
    }
}
