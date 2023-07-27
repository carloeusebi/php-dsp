<?php

namespace app\tests;

use app\app\App;
use app\core\Controller;
use app\core\Router;
use app\core\exceptions\RouteNotFoundException;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    protected ?App $app;
    private Router $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->router = new Router();
        $_SERVER['REQUEST_METHOD'] = 'get';
        $_SERVER['REQUEST_URI'] = '/existing_route';
    }

    /** @test */
    public function testRoute(): void
    {
        $this->router->get('/home', ['Controller', 'index']);
        $this->router->post('/login', ['Controller', 'login']);
        $this->router->delete('/patient', ['Controller', 'delete']);
        $expected = [
            'get' => [
                '/home' => ['Controller', 'index']
            ],
            'post' => [
                '/login' => ['Controller', 'login']
            ],
            'delete' => [
                '/patient' => ['Controller', 'delete']
            ],

        ];
        $this->assertEquals($expected, $this->router->routes());
    }

    /** @test
     * @dataProvider routeNotFoundCases
     * @runInSeparateProcess
     */
    public function testRouteNotFound(string $request_uri, string $request_method): void
    {
        $_SERVER['REQUEST_URI'] = $request_uri;
        $_SERVER['REQUEST_METHOD'] = $request_method;
        $controller = new class()
        {
            public function mock(): bool
            {
                return true;
            }
        };
        // create a new router instance to empty the routes
        $router = new Router();
        $this->router->get('/home', [$controller::class, 'index']);
        $this->router->post('/login', ['Controller', 'index']);

        $this->expectException(RouteNotFoundException::class);
        $router->resolve();
    }

    /**@test */
    public function testResolve(): void
    {
        $controller = new class() extends Controller
        {
            public function index(): string
            {
                return 'test';
            }
        };
        $this->router->get('/existing_route', [$controller::class, 'index']);
        $result = $this->router->resolve();
        $this->assertEquals($result, 'test');
    }


    static function routeNotFoundCases(): array
    {
        return [
            ['/user', 'get'], //assert route does not exist
            ['/home', 'post'], //assert route exists but wrong method
            ['/home', 'get'] // assert route exists but controller's method does not
        ];
    }
}
