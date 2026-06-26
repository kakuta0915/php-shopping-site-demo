<?php
// ============================================================
// helpers.php - 汎用関数
// ============================================================

// XSS対策: HTMLエスケープ
function h(mixed $str): string
{
  return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

// 価格フォーマット (例: 1980 → ¥1,980)
function price(int|float $amount): string
{
  // number_format(): 3桁ごとにカンマを付けるPHPの組み込み関数
  return '¥' . number_format($amount);
}

// ページネーション計算
function paginate(int $total, int $page, int $perPage = PER_PAGE): array
{

  $totalPages = (int)ceil($total / $perPage);
  $page = max(1, min($page, $totalPages));
  $offset = ($page - 1) * $perPage;

  return [
    'total'       => $total,
    'per_page'    => $perPage,
    'current'     => $page,
    'total_pages' => $totalPages,
    'offset'      => $offset,
    'has_prev'    => $page > 1,
    'has_next'    => $page < $totalPages,
  ];
}

// クエリ文字列を維持しながらページ番号だけ変える
function pageUrl(int $page): string
{
  $params = $_GET;
  $params['page'] = $page;
  return '?' . http_build_query($params);
}

// 商品画像URLを返す（なければプレースホルダー）
function productImageUrl(?string $path): string
{
  // 画像のパスがあり、さらにその画像ファイルが実際に存在するか
  if ($path && file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
    return h($path);
  }
  return '/assets/images/no_image.svg';
}

// ★ レーティング表示 (1-5)
function stars(int $rating): string
{
  $filled = str_repeat('★', $rating);
  $empty  = str_repeat('☆', 5 - $rating);
  return '<span class="stars">' . $filled . $empty . '</span>';
}

// リダイレクト
function redirect(string $url, ?string $flash = null, string $flashKey = 'success'): never
{
  if ($flash) {
    Session::flash($flashKey, $flash);
  }
  header('Location: ' . $url);
  exit;
}

// POSTかどうか
function isPost(): bool
{
  return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// CSRFトークン hidden フィールドを出力
function csrfField(): string
{
  $token = Session::csrfToken();
  return '<input type="hidden" name="_csrf" value="' . h($token) . '">';
}

// CSRFを検証してNGならアボート
function verifyCsrfOrAbort(): void
{
  $token = $_POST['_csrf'] ?? '';
  if (!Session::verifyCsrf($token)) {
    http_response_code(403);
    exit('403 Forbidden');
  }
}

// 入力値を安全にトリム
function input(string $key, mixed $default = ''): mixed
{
  $val = $_POST[$key] ?? $_GET[$key] ?? $default;
  return is_string($val) ? trim($val) : $val;
}

// 画像アップロード処理
// 成功: 保存したパス文字列を返す / 失敗: 例外をthrow
function uploadImage(array $file, string $prefix = 'img'): string
{
  if ($file['error'] !== UPLOAD_ERR_OK) {
    throw new RuntimeException('アップロードエラー: ' . $file['error']);
  }
  if ($file['size'] > UPLOAD_MAX_SIZE) {
    throw new RuntimeException('ファイルサイズが大きすぎます（5MB以内）');
  }

  $mime = mime_content_type($file['tmp_name']);
  if (!in_array($mime, UPLOAD_ALLOWED)) {
    throw new RuntimeException('対応していないファイル形式です');
  }

  $ext      = match ($mime) {
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
  };
  $filename = $prefix . '_' . uniqid() . '.' . $ext;
  $destPath = UPLOAD_DIR . $filename;

  if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    throw new RuntimeException('ファイルの保存に失敗しました');
  }

  return '/assets/uploads/' . $filename;
}

// オートロード (src/ 以下のクラスを自動読み込み)
spl_autoload_register(function (string $class): void {
  $base = dirname(__DIR__) . '/src/';
  $dirs = ['', 'Model/', 'Controller/'];
  foreach ($dirs as $dir) {
    $file = $base . $dir . $class . '.php';
    if (file_exists($file)) {
      require_once $file;
      return;
    }
  }
});
