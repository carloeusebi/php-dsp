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
   * Attempts login provided with an username and a password.
   * 
   * @return array|false Returns the logged in Admin if login is successful, false otherwise.
   */
  static function attemptLogin(string $username, string $password): array|false
  {
    $admins = App::$app->admin->get();

    foreach ($admins as $admin) {
      if ($username === $admin['username']) {
        if (password_verify($password, $admin['password'])) {
          return $admin;
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

  static function generateToken(array $model): string
  {
    $randomBytes = random_bytes(32);
    $token = hash_hmac('sha256', $randomBytes, bin2hex($randomBytes));

    Database::table(self::TABLE)->insert([
      'tokenable_type' => 'Admin',
      'tokenable_id' => $model['id'],
      'name' => 'authToken',
      'token' => $token,
      'expires_at' => date('Y-m-d H:i:s', time() + 7200),
    ]);

    return $token;
  }


  static function validate(): array|false
  {
    $token = self::getAuthToken();

    if (!$token) return false;

    return Database::table(self::TABLE)
      ->where('token', '=', $token, 'AND')
      ->whereRaw('expires_at > NOW()', '')
      ->get()[0];
  }


  static function refresh(array $token)
  {
    $now = date('Y-m-d H:i:s', time());
    $new_expiration = date('Y-m-d H:i:s', time() + 7200);

    $id = $token['id'];

    Database::table(self::TABLE)->update($id, ['last_used_at' => $now, 'expires_at' => $new_expiration]);
  }


  protected static function getAuthToken(): string
  {
    return str_replace('Bearer ', '', apache_request_headers()['Authorization']) ?? '';
  }
}
