<?php

namespace app\app;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

use app\db\Database;
use app\core\Router;
use app\core\Session;
use app\core\Controller;
use app\core\exceptions\ForbiddenException;
use app\core\exceptions\RouteNotFoundException;
use app\core\utils\Request;
use app\core\utils\Response;
use app\models\Admin;
use app\models\Patient;
use app\models\Survey;
use app\models\Question;

class App
{
    public static string $ROOT_DIR;
    public static App $app;
    public ?Controller $controller;
    public Router $router;
    public Session $session;
    public Database $db;
    public Admin $admin;
    public Patient $patient;
    public Survey $survey;
    public Question $question;


    public function __construct()
    {
        try {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__));
            $dotenv->load();
        } catch (\Exception $exception) {
            // If Dotenv can't find the .env file it won't throw an exception
            \app\core\exceptions\ErrorHandler::log($exception);
        }

        self::$app = $this;
        self::$ROOT_DIR = dirname(__DIR__);

        $this->db = new Database();
        $this->router = new Router();
        $this->session = new Session();
        $this->admin = new Admin();
        $this->patient = new Patient();
        $this->question = new Question();
        $this->survey = new Survey();
    }


    public function run()
    {
        try {
            return $this->router->resolve();
        } catch (RouteNotFoundException | ForbiddenException $e) {
            $code = intval($e->getCode());
            Response::statusCode($code);
            if ($e instanceof RouteNotFoundException && !Request::isApi())
                $this->router->renderView('404');
        }
    }


    public function logIssueToDb(string $message, string $name = '', string $email = '')
    {
        try {
            $statement = $this->db->prepare('INSERT INTO `issues` (name, email, message) VALUES (:name, :email, :message)');
            $statement->bindValue('name', $name);
            $statement->bindValue('email', $email);
            $statement->bindValue('message', $message);

            $statement->execute();

            return true;
        } catch (\Exception $exception) {
            \app\core\exceptions\ErrorHandler::log($exception);
            return false;
        }
    }
}
