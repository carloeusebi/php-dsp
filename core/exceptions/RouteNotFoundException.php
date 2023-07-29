<?php

namespace app\core\exceptions;

class RouteNotFoundException extends \Exception
{
    protected $code = 404;
    protected $message = 'Page not found';
}
