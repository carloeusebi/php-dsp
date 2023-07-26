<?php

namespace app\app;

use Error;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

use app\db\Database;
use app\core\Router;
use app\core\Session;
use app\core\Controller;
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
        } catch (InvalidPathException) {
            // If Dotenv can't find the .env file it won't throw an exception
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


    public function run(): void
    {
        try {
            $this->router->resolve();
        } catch (RouteNotFoundException) {
            Response::statusCode(404);
            if (!Request::isApi())
                $this->router->renderView('404');
            exit();
        }
    }


    public function logIssueToDb(int $code, string $error = ''): bool
    {
        $date = date("y-m-d H:i:s", time());

        $message = match ($code) {
            1 => "An email was successfully sent.",
            2 => "Form was submitted with honey box checked.",
            3 => "Form was submitted with an undeliverable email.",
            4 => $error,
            5 => $error,
            default => "Something went unexpected.",
        };

        $query = "INSERT INTO logs (code, message, date) VALUES (:code, :message, :date)";

        try {
            $statement = $this->db->prepare($query);

            $statement->bindValue('code', $code);
            $statement->bindValue('message', $message);
            $statement->bindValue('date', $date);

            $statement->execute();
            return true;
        } catch (Error) {
            return false;
        }
    }
}
