<?php

namespace app\core\middlewares;

use app\app\App;
use app\core\exceptions\ForbiddenException;
use app\core\utils\Request;

class PatientMiddleware extends BaseMiddleware
{
  protected array $actions = [];


  public function __construct(array $actions = [])
  {
    $this->actions = $actions;
  }


  public function execute(Request $request): void
  {
    // if the controller's method is inside the actions array it executes the validation. An empty actions array means that all controller's methods are protected by the middleware
    if (empty($this->actions) || in_array(App::$app->controller->action, $this->actions)) {

      if (!self::validateToken($request)) {
        throw new ForbiddenException();
      }
    }
  }


  protected static function validateToken(Request $request): bool
  {
    $token = $request->get('token');

    if (!$token) return false;

    $result =  App::$app->survey->getByToken($token);

    return count($result);
  }
}
