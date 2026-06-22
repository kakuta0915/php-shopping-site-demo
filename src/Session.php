<?php
// ============================================================
// Session - セッション管理
// ============================================================

class Session
{
  public static function start(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_name(SESSION_NAME);
      session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'secure' => false, //本番では true (HTTPS)
        'httponly' => true, // JSからアクセス不可
        'samesite' => 'Lax',
      ]);

      session_start();
    }
  }

  public static function set(string $key, mixed $value): void
  {
    $_SESSION[$key] = $value;
  }

  public static function get(string $key, mixed $default = null): mixed
  {
    // $_SESSION[$key] に値が保存されていれば、その値を返し、保存されていなければ、$default を返す。
    return $_SESSION[$key] ?? $default;
  }

  public static function has(string $key): bool
  {
    return isset($_SESSION[$key]);
  }

  public static function delete(string $key): void
  {
    // unset() は、変数や配列の要素を削除する関数
    unset($_SESSION[$key]);
  }

  // フラッシュメッセージ (1回だけ表示されるメッセのこと)
  public static function flash(string $key, string $message): void
  {
    $_SESSION['_flash'][$key] = $message;
  }

  // メッセージを取得後、削除する処理
  public static function getFlash(string $key): ?string
  {
    // 削除前に$msgにコピー（1回だけ表示させるため削除する必要がある）
    $msg = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $msg;
  }

  // セッションIDを新しく作り直す処理
  public static function regenerate(): void
  {
    session_regenerate_id(true);
  }

  // セッションを終了する処理
  public static function destroy(): void
  {
    $_SESSION = [];
    session_destroy();
  }

  // CSRFトークンの生成・取得
  public static function csrfToken(): string
  {
    // セッションに _csrf がまだ無い時、trueを返す
    if (!isset($_SESSION['_csrf'])) {
      // random_bytes(32): 安全なランダムデータを32バイト作る
      // bin2hex(): それを文字列（英数字）に変換する
      $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
  }

  // 送られてきたトークンが正しいかチェックする処理
  public static function verifyCsrf(string $token): bool
  {
    // hash_equals: 安全な文字列比較 (=== は攻撃される為)
    return hash_equals($_SESSION['_csrf'] ?? '', $token);
  }
}
