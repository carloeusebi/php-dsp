<?php

namespace app\core\middlewares;

use app\core\utils\Request;

abstract class BaseMiddleware
{
  abstract public function execute(Request $request): void;
}
