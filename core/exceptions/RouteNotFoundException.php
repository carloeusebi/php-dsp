<?php

namespace app\core\exceptions;

class RouteNotFoundException extends \Exception
{
    protected $message = '404 not found';
}
