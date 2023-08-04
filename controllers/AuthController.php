<?php

namespace app\controllers;

use app\app\App;
use app\controllers\helpers\Auth;
use app\core\Controller;
use app\core\middlewares\AdminMiddleware;
use app\core\utils\Request;
use app\core\utils\Response;

class AuthController extends Controller
{
    protected const ADMIN = 'ADMIN';

    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware(['validate']));
    }


    public function login(): void
    {
        $data = Request::getBody();

        extract($data);

        if (!isset($username) || !isset($password)) {
            Response::response(400, ['Error' => 'Missing username or password']);
        }

        $is_auth = App::$app->admin->attemptLogin($username, $password);

        if (!$is_auth) {
            Response::response(401, ['message' => 'invalid-credentials']);
        }

        $token = Auth::generateToken(self::ADMIN);
        Auth::setCookie($token);
        $_SESSION['TOKEN'] = $token;

        $message = [
            'user' => self::ADMIN,
            'session_id' => session_id()
        ];
        Response::response(200, $message);
    }


    public function logout(): void
    {
        unset($_COOKIE['TOKEN']);
        unset($_SESSION['TOKEN']);
        session_destroy();
        Response::statusCode(204);
    }


    public function validate(): void
    {
        // this function just resets cookie expiration, it is protected by the middleware so it handles the authentication
        $token = $_COOKIE['TOKEN'];
        Auth::setCookie($token);
    }
}
