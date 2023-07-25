<?php

namespace core;

use app\App;
use core\middlewares\BaseMiddleware;

abstract class Controller
{
    public string $action;
    protected array $params = [];

    /**
     * @var BaseMiddleware[] 
     */
    protected array $middlewares = [];


    public function render(string $view, $params = [])
    {
        App::$app->router->renderView($view, $params);
    }


    protected function addToParams($param, $value): Controller
    {
        $this->params[$param] = $value;
        return $this;
    }


    protected function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }


    /**
     * @return BaseMiddleware[]
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }
}
