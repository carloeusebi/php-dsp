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

    /**
     * @var array Contains regular expression patterns for route parameters
     */
    private array $param_patterns = ['{id}' => '(\d+)', '{token}' => '([0-9a-fA-F]+)'];


    public function get(string $url, array $callback): void
    {
        $this->addRoute('get', $url, $callback);
    }


    public function post(string $url, array $callback): void
    {
        $this->addRoute('post', $url, $callback);
    }


    public function put(string $url, array $callback): void
    {
        $this->addRoute('put', $url, $callback);
    }


    public function delete(string $url, array $callback): void
    {
        $this->addRoute('delete', $url, $callback);
    }


    private function addRoute(string $method, string $url, array $callback)
    {
        // Replace parameter placeholders with regex patterns
        foreach ($this->param_patterns as $param => $pattern) {
            $url = str_replace($param, $pattern, $url);
        }
        $this->routes[$method][$url] = $callback;
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
        $request = App::$app->request;

        $path = $request->getPath();
        $method = $request->getMethod();

        // todo remove for production; to allow cors to pass through during testing
        if ($request->isOption()) {
            return Response::statusCode(200);
        }

        [$callback, $params] = $this->findMatchingRoute($method, $path);

        if (!$callback) {
            throw new RouteNotFoundException();
        }

        /**
         * @var Controller
         */
        $controller = new $callback[0]();
        $controller->action = $action = $callback[1];
        App::$app->controller = $controller;

        // execute controller's middlewares
        $middlewares = $controller->getMiddlewares();
        foreach ($middlewares as $middleware) {
            $middleware->execute($request);
        }

        $params = [...$params, $request];

        return call_user_func_array([$controller, $action], $params);
    }

    /**
     * Finds a matching route for the given method and path.
     *
     * This method searches the registered routes for a match against the provided HTTP method and request path.
     * It compares the route patterns using regular expressions and returns the associated callback along with any extracted parameter values.
     * 
     * @return array|false The callback and parameter values or false if no match
     */
    private function findMatchingRoute(string $method, string $path): array|false
    {
        foreach ($this->routes[$method] as $route => $callback) {
            // Convert the route pattern into a regular expression
            $pattern = str_replace('/', '\/', $route);

            // Check if the path matches the regular expression pattern
            if (preg_match('/^' . $pattern . '$/', $path, $matches)) {
                // Remove the full match from the $matches array
                array_shift($matches);
                return [$callback, $matches];
            }
        }

        return false;
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
