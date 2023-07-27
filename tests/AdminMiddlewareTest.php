<?php

namespace app\test;

use Mockery;
use PHPUnit\Framework\TestCase;
use app\app\App;
use app\core\Controller;
use app\core\middlewares\AdminMiddleware;
use app\models\Admin;

class AdminMiddlewareTest extends TestCase
{
    private App $app;

    protected function setUp(): void
    {
        parent::setUp();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = 'uri';
        //setup router
        $this->app = isset(App::$app) ? App::$app : new App();
    }


    /**
     * @test
     * Assert that a controller with an empty actions array gets its methods blocked by the middleware if not auth
     */
    public function testBlockedIfNotAuthOnEmptyActions(): void
    {
        unset($_COOKIE['TOKEN']);
        unset($_SESSION['TOKEN']);

        $controller = new class() extends Controller
        {
            public function __construct()
            {
                $this->registerMiddleware(new AdminMiddleware([]));
            }
            public function aRandomMethod()
            {
                return true;
            }
        };

        $this->app->router->get('uri', [$controller::class, 'aRandomMethod']);

        // assert the an exception with 403 code get thrown
        $this->expectExceptionCode(403);
        $this->app->router->resolve();
    }

    /**
     * @test
     * Assert that a controller with a blocked action gets its action blocked by the middleware
     */
    public function testBlockedIfNotAuthOnProtectedActions(): void
    {
        unset($_COOKIE['TOKEN']);
        unset($_SESSION['TOKEN']);

        $controller = new class() extends Controller
        {
            public function __construct()
            {
                $this->registerMiddleware(new AdminMiddleware(['protectedMethod']));
            }
            public function protectedMethod(): bool
            {
                return true;
            }
        };
        $this->app->router->get('uri', [$controller::class, 'protectedMethod']);
        $this->expectExceptionCode(403);
        $this->app->router->resolve();
    }

    /**
     * @test
     * Assert that a controller with an unprotected method can be accessed if it has other protected methods;
     */
    public function testLetThroughIfNotAuthOnUnprotectedAction(): void
    {
        unset($_COOKIE['TOKEN']);
        unset($_SESSION['TOKEN']);


        $controller = new class() extends Controller
        {
            public function __construct()
            {
                $this->registerMiddleware(new AdminMiddleware(['protectedMethod']));
            }
            public function protectedMethod()
            {
                return true;
            }
            public function unprotectedMethod()
            {
                return true;
            }
        };
        $this->app->router->get('uri', [$controller::class, 'unprotectedMethod']);
        $result = $this->app->router->resolve();
        $this->assertTrue($result);
    }

    public function testLetThroughIfAuthOnProtectedMethod(): void
    {
        $_COOKIE['TOKEN'] = 'abcde';
        $_SESSION['TOKEN'] = 'abcde';

        $controller = new class() extends Controller
        {
            public function __construct()
            {
                $this->registerMiddleware(new AdminMiddleware([]));
            }
            public function protectedMethod()
            {
                return true;
            }
        };
        $this->app->router->get('uri', [$controller::class, 'protectedMethod']);
        $result = $this->app->router->resolve();
        $this->assertTrue($result);
    }
}
