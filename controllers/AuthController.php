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
    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware(['validate']));
    }


    public function login(): void
    {
        $data = Request::getBody();

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        if (!isset($email) || !isset($password)) {
            Response::response(400, ['Error' => 'Missing email or password']);
        }
        $admin = Auth::attemptLogin($email, $password);
        if (!$admin) {
            Response::response(401, ['message' => 'invalid-credentials']);
        }
        $token = Auth::generateToken($admin);

        Response::response(200, ['user' => $admin]);
    }


    public function logout(): void
    {
        Auth::logout();
        Response::statusCode(204);
    }


    /**
     * Mocks Laravel Sanctum, to allow the frontend Vue app to work with both this vanilla PHP app and Laravel.
     */
    public function mockSanctum(): void
    {
        Response::statusCode(200);
    }
}
