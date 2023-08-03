<?php

namespace app\core;

use app\app\App;
use app\core\exceptions\RouteNotFoundException;
use app\core\utils\Request;
use app\core\utils\Response;

class Router
{
    private array $routes = [];
    private string $layout = 'main';

    public function get(string $url, array $callback): void
    {
        $this->routes['get'][$url] = $callback;
    }


    public function post(string $url, array $callback): void
    {
        $this->routes['post'][$url] = $callback;
    }


    public function delete(string $url, array $callback): void
    {
        $this->routes['delete'][$url] = $callback;
    }


    public function routes(): array
    {
        return $this->routes;
    }


    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }


    public function resolve()
    {
        $path = Request::getPath();
        $method = Request::getMethod();

        $callback = $this->routes[$method][$path] ?? false;

        // todo remove for production; to allow cors to pass through during testing
        if (Request::isOption()) {
            Response::response(200);
        }

        if (!$callback) {
            throw new RouteNotFoundException();
        }

        /**
         * @var Controller
         */
        $controller = new $callback[0]();
        $controller->action = $callback[1];
        App::$app->controller = $controller;

        // execute controller's middlewares
        $middlewares = $controller->getMiddlewares();
        foreach ($middlewares as $middleware) {
            $middleware->execute();
        }

        // call the controller function
        return $controller->{$controller->action}();
    }

    public function renderView(string $page, array $params = []): void
    {
        foreach ($params as $param => $value) {
            $$param = $value;
        }

        ob_start();

        include_once(App::$ROOT_DIR . "/resources/views/$page.view.php");

        $content = ob_get_clean();

        include_once(App::$ROOT_DIR . "/resources/views/layouts/$this->layout.php");
    }
}
