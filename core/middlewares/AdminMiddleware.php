<?php

namespace app\core\middlewares;

use app\app\App;
use app\core\utils\Response;

class AdminMiddleware extends BaseMiddleware
{
  protected array $actions = [];


  public function __construct(array $actions = [])
  {
    $this->actions = $actions;
  }


  public function execute(): void
  {

    // if the controller's method is inside the actions array it executes the validation. An empty actions array means that all controller's methods are protected by the middleware
    if (empty($this->actions) || in_array(App::$app->controller->action, $this->actions)) {

      if (!self::validateToken()) {

        Response::response(401, 'Failed to authenticate');
      }
    }
  }


  protected static function validateToken(): bool
  {
    return isset($_COOKIE['TOKEN']) && isset($_SESSION['TOKEN']) && $_COOKIE['TOKEN'] === $_SESSION['TOKEN'];
  }
}
