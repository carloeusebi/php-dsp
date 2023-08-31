<?php

namespace app\core\middlewares;

use app\app\App;
use app\controllers\helpers\Auth;
use app\core\exceptions\ForbiddenException;
use app\core\utils\Request;
use app\db\Database;

class AdminMiddleware extends BaseMiddleware
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

      $aut_token = Auth::validate($request);
      if (!$aut_token) {
        throw new ForbiddenException();
      } else {
        // refresh token
        Auth::refresh($aut_token);
      }
    }
  }
}
