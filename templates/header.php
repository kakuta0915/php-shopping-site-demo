<?
// __DIR__ は現在のファイル（index.php）があるディレクトリを表す
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/src/helpers.php';

Session::start();

$navCategories = DB::fetchAll(
  //  親カテゴリだけ取得し、表示順に並べる
  'SELECT id, name, slug FROM categories WHERE parent_id IS NULL ORDER BY sort_order'
);

// カート件数を初期化
$cartCount = 0;
if (Auth::check()) {
  $cartCount = (int) DB::fetchColumn(
    // カートに何も入っていない場合、SUM(quantity)はNULLを返す
    // NULL → 0 に変換している
    'SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE user_id = ?',
    // 現在ログイン中のユーザーIDを取得
    [Auth::id()]
  );
}
$pageTitle = ($pageTitle ?? 'ページ') . '|' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($pageTitle) ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
  <!-- ===== SITE HEADER ===== -->
  <header class="site-header">
    <div class="container">
      div
    </div>
  </header>
</body>

</html>