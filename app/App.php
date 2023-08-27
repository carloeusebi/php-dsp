<?php

namespace app\app;

use Dotenv\Dotenv;

use app\db\Database;
use app\core\Router;
use app\core\Session;
use app\core\Controller;
use app\core\exceptions\ErrorHandler;
use app\core\exceptions\ForbiddenException;
use app\core\exceptions\RouteNotFoundException;
use app\core\utils\Request;
use app\core\utils\Response;
use app\models\Admin;
use app\models\Patient;
use app\models\Survey;
use app\models\Question;
use app\models\File;
use app\models\Tag;

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
    public File $file;
    public Tag $tag;


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

        require self::$ROOT_DIR . '/core/utils/functions.php';

        $this->db = new Database();
        $this->router = new Router();
        $this->session = new Session();

        // models
        $this->admin = new Admin();
        $this->patient = new Patient();
        $this->question = new Question();
        $this->survey = new Survey();
        $this->file = new File();
        $this->tag = new Tag();
    }

    /**
     * Runs the application by resolving the route and handling exceptions.
     * @return mixed The result of the resolved route.
     */
    public function run()
    {
        try {
            // Resolve the requested route using the router.
            return $this->router->resolve();
        } catch (RouteNotFoundException | ForbiddenException $e) {
            $code = intval($e->getCode());
            Response::statusCode($code);
            if ($e instanceof RouteNotFoundException && !Request::isApi())
                $this->router->renderView('404');
        }
    }


    static function logToDb(string $message)
    {
        Database::table('logs')->insert(['message' => $message]);
    }
}
