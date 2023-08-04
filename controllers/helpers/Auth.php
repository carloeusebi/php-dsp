<?php

namespace app\controllers\helpers;

use app\controllers\AuthController;

class Auth extends AuthController
{
  private const ADMIN_TOKEN = "ADM_";
  private const USER_TOKEN = "USR_";

  protected static function generateToken(string $type, int $id = 0): string
  {
    $prefix = ($type === self::ADMIN) ? self::ADMIN_TOKEN : self::USER_TOKEN;
    $randomBytes = random_bytes(32);
    $token = $prefix . $id . hash_hmac('sha256', $prefix, bin2hex($randomBytes));

    return $token;
  }

  protected static function setCookie(string $token)
  {
    setcookie('TOKEN', $token, time() + 3600, '/', '', true, true,);
  }
}
