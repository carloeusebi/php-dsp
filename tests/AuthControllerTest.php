<?php

namespace app\tests;

use Mockery;
use app\controllers\AuthController;
use app\app\App;
use PHPUnit\Framework\TestCase;

class AuthControllerTest extends TestCase
{
    private AuthController $auth;
    protected ?App $app;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = new AuthController();
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $app = isset(App::$app) ? App::$app : new App();

        /**
         * @var \Mockery\MockInterface|\Mockery\LegacyMockInterface
         */
        $adminMock = Mockery::mock('app\models\Admin');
        $adminMock->shouldReceive('get')->andReturn([[
            'username' => 'correct_username',
            'password' => 'correct_password'
        ]]);
        $app->admin = $adminMock;

        $response_mock = Mockery::mock('overload:app\core\utils\Response');
        $response_mock->shouldReceive('response')->andReturn(true);
        $response_mock->shouldReceive('statusCode')->andReturn(true);
    }

    /** 
     * @test
     * @runInSeparateProcess
     */
    public function testSuccessfulLoginAndLogout(): void
    {
        $_POST['username'] = 'correct_username';
        $_POST['password'] = 'correct_password';

        $this->auth->login();

        // assert successful login by checking if the token is present in the super global SESSION
        $token = $_SESSION['TOKEN'];
        $this->assertEquals('ADM', substr($token, 0, 3));

        $this->auth->logout();
        $this->assertFalse(isset($_SESSION['TOKEN']));

        Mockery::close();
    }

    /**
     * @test
     * @runInSeparateProcess
     * @dataProvider credentials
     */
    public function testFailedLogin(string $username, string $password)
    {
        $_POST['username'] = $username;
        $_POST['password'] = $password;

        $this->auth->login();

        $this->assertFalse(isset($_SESSION['TOKEN']));
    }

    static function credentials(): array
    {
        return [
            ['correct_username', 'wrong_password'],
            ['wrong_username', 'wrong_password'],
            ['wrong_username', 'wrong_password']
        ];
    }
}
