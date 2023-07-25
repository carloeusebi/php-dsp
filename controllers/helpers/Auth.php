<?php

namespace controllers\helpers;

use controllers\AuthController;

class Auth extends AuthController
{
  private const ADMIN_TOKEN = "ADM_";
  private const USER_TOKEN = "USR_";

  protected static function generateToken(string $type, int $id = 0): string
  {
    $prefix = ($type === self::ADMIN) ? self::ADMIN_TOKEN : self::USER_TOKEN;
    $randomBytes = random_bytes(32);
    $token = $prefix . $id . '.' . bin2hex($randomBytes) . '.' . hash_hmac('sha256', $prefix . bin2hex($randomBytes), 'your-secret-key');

    return $token;
  }

  protected static function setCookie(string $token)
  {
    setcookie('TOKEN', $token, time() + 3600, '/', '', true, true,);
  }
}
