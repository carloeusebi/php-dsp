<?php

namespace core\middlewares;

abstract class BaseMiddleware
{
  abstract public function execute(): void;
}
