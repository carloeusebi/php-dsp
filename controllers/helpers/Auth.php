<?php

namespace app\controllers\helpers;

use app\app\App;
use app\controllers\AuthController;
use app\core\Model;
use app\db\Database;

class Auth
{

  private const TABLE = 'personal_access_tokens';

  /**
   * Attempts login provided with an email and a password.
   * 
   * @return array|false Returns the logged in Admin if login is successful, false otherwise.
   */
  static function attemptLogin(string $email, string $password): array|false
  {
    $users = App::$app->user->get();

    foreach ($users as $user) {
      if ($email === $user['email']) {
        if (password_verify($password, $user['password'])) {
          return $user;
        }
      }
    }

    return false;
  }


  static function logout()
  {
    session_destroy();
    Database::execute('TRUNCATE TABLE ' . self::TABLE);
  }

  static function generateToken(array $user): string
  {
    $randomBytes = random_bytes(32);
    $token = hash_hmac('sha256', $randomBytes, bin2hex($randomBytes));

    self::setCookie($token);
    self::logToken($user, $token);

    return $token;
  }


  static function validate(): array|false
  {
    $token = self::getAuthToken();

    if (!$token) return false;

    return Database::table(self::TABLE)
      ->where('token', '=', $token, '')
      ->get();
  }


  static function refresh(array $token)
  {
    $token = self::getAuthToken();
    self::setCookie($token);
  }


  protected static function getAuthToken()
  {
    return $_COOKIE['TOKEN'] ?? null;
  }


  protected static function setCookie(string $token)
  {
    $hour = 3600;
    setcookie('TOKEN', $token, time() + 2 * $hour, '/', '', true, true);
  }


  protected static function logToken(array $user, string $token)
  {
    Database::table(self::TABLE)->insert([
      'tokenable_type' => 'Admin',
      'tokenable_id' => $user['id'],
      'name' => 'authToken',
      'token' => $token,
    ]);
  }
}
