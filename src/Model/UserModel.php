<?php
class UserModel
{
  public static function findByEmail(string $email): array|false
  {
    // ユーザーが入力した$emailで検索
    return DB::fetch('SELECT * FROM users WHERE email = ?', [$email]);
  }

  public static function findById(int $id): array|false
  {
    return DB::fetch('SELECT * FROM users WHERE id = ?', [$id]);
  }

  public static function create(string $name, string $email, string $password): string
  {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    return DB::insert('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)', [$name, $email, $hash]);
  }

  public static function emailExists(string $email): bool
  {
    // COUNT(*): 条件に一致する数を返す
    return (bool) DB::fetchColumn('SELECT COUNT(*) FROM users WHERE email = ?', [$email]);
  }

  public static function update(int $id, array $data): void
  {
    DB::execute('UPDATE users SET name = ?, email = ? WHERE id = ?', [$data['name'], $data['email'], $id]);
  }

  public static function updatePassword(int $id, string $password): void
  {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    DB::execute('UPDATE users SET password_hash = ? WHERE id = ?', [$hash, $id]);
  }
}
