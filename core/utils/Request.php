<?php

namespace app\core\utils;

class Request
{
    static function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }


    static function getPath(): string
    {
        return parse_url($_SERVER['REQUEST_URI'])['path'];
    }


    static function isGet(): bool
    {
        return self::getMethod() === 'get';
    }


    static function isPost(): bool
    {
        return self::getMethod() === 'post';
    }


    static function isOption(): bool
    {
        return self::getMethod() === 'options';
    }

    /**
     * @return bool true if the uri passed as arg is the current request uri
     */
    static function urlIs(string $uri)
    {
        return $_SERVER['REQUEST_URI'] === $uri;
    }


    static function isApi(): bool
    {
        return substr($_SERVER['REQUEST_URI'], 0, 4) === '/api';
    }


    static function get(string $key): string
    {
        return self::getBody()[$key] ?? '';
    }


    static function getBody(): ?array
    {
        $data = [];

        if (self::isGet()) {
            foreach ($_GET as $key => $value) {
                $data[$key] = htmlspecialchars($value);
            }
        } elseif (self::isPost()) {
            foreach ($_POST as $key => $value) {
                $data[$key] = htmlspecialchars($value);
            }
        }

        if (!$data) {
            $data = (array) json_decode(file_get_contents("php://input"), true);
        }

        return $data;
    }
}
