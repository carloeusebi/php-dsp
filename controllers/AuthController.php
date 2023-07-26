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
    protected const USER = 'USER';

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

        $admins = App::$app->admin->get();

        foreach ($admins as $adm) {
            if ($username === $adm['username'] && $password === $adm['password']) {
                $token = Auth::generateToken(self::ADMIN);
                Auth::setCookie($token);
                $_SESSION['TOKEN'] = $token;

                $message = [
                    'user' => self::ADMIN,
                    'session_id' => session_id()
                ];
                Response::response(200, $message);
            }
        }
        Response::response(401, ['message' => 'invalid-credentials']);
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
