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


  public static function resetCookieExpiration()
  {
    $token = $_COOKIE['TOKEN'];
    self::setCookie($token);
  }


  public static function setCookie(string $token)
  {
    /**
     * @var int $ninety_minutes Cookie expiration.
     */
    $ninety_minutes = 5400;
    setcookie('TOKEN', $token, time() + $ninety_minutes, '/', '', true, true,);
  }
}
