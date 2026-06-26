<?php
// ============================================================
// Auth - 認証ヘルパー
// ============================================================

class Auth
{
  // ログイン状態チェック
  public static function check(): bool
  {
    return Session::has('user_id');
  }

  // 管理者かどうか
  public static function isAdmin(): bool
  {
    return Session::get('user_role') === 'admin';
  }

  // ログイン中のユーザー情報
  public static function user(): ?array
  {
    // check() の結果が false なら、このメソッドを終了する
    if (!self::check()) return null;

    return [
      'id' => Session::get('user_id'),
      'name' => Session::get('user_name'),
      'role' => Session::get('user_role'),
    ];
  }

  public static function id(): ?int
  {
    return Session::get('user_id');
  }

  // セッションにユーザー情報を保存
  public static function login(array $user): void
  {
    Session::regenerate(); // セッション固定攻撃対策
    Session::set('user_id', (int)$user['id']);
    Session::set('user_name', $user['name']);
    Session::set('user_role', $user['role']);
  }

  public static function logout(): void
  {
    Session::destroy();
  }

  // 未ログインならリダイレクト
  public static function require(): void
  {
    if (!self::check()) {
      Session::flash('error', 'ログインが必要です。');
      header('Location: /login.php');
      exit;
    }
  }

  // 管理者でなければリダイレクト
  public static function requireAdmin(): void
  {
    self::require();
    if (!self::isAdmin()) {
      header('Location: /index.php');
      exit;
    }
  }
}
