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
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/header.css">
</head>

<body>
  <!-- ===== SITE HEADER ===== -->
  <header class="site-header">
    <div class="container">
      <div class="header-inner">
        <a href="/index.php" class="site-logo">PHP Shopping Site <span>Demo</span></a>

        <!-- PC: 検索フォーム -->
        <form action="/products.php" method="GET" class="header-search">
          <input type="text" name="q" placeholder="商品を検索..." value="<?= h($_GET['q'] ?? '')  ?>">
          <button type="submit">検索</button>
        </form>

        <!-- PC: ヘッダーナビ -->
        <nav class="header-nav">
          <?php if (Auth::check()): ?>
            <a href="/mypage.php">
              <span class="nav-icon">👤</span>
              <!-- Auth::user() が返した結果から name を取り出している -->
              <span><?= h(Auth::user()['name']) ?></span>
            </a>
            <?php if (Auth::isAdmin()): ?>
              <a href="/admin/index.php">
                <span class="nav-icon">⚙️</span>
                <span>管理画面</span>
              </a>
            <?php endif; ?>
            <a href="/logout.php">
              <span class="nav-icon">🔓</span>
              <span>ログアウト</span>
            </a>
          <?php else: ?>
            <a href="/login.php">
              <span class="nav-icon">🔒</span>
              <span>ログイン</span>
            </a>
            <a href="/register.php">
              <span class="nav-icon">✏️</span>
              <span>会員登録</span>
            </a>
          <?php endif; ?>
          <a href="/cart.php" class="cart-badge"
            data-count="<?= $cartCount > 0 ? $cartCount : '' ?>">
            <span class="nav-icon">🛒</span>
            <span>カート</span>
          </a>
        </nav>

        <!-- Mobile: カートアイコン（常時表示） -->
        <a href="/cart.php" class="cart-badge" data-count="<?= $cartCount > 0 ? $cartCount : '' ?>"
          style="display: none;" id="mobileCart">
          <span style="font-size:1.5rem;">🛒</span>
        </a>

        <!-- Mobile: ハンバーガーボタン -->
        <button class="hamburger" id="hamburgerBtn" aria-label="メニューを開く">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </header>

  <!-- ===== MOBILE DRAWER ===== -->
  <div class="mobile-drawer" id="mobileDrawer">
    <div class="drawer-overlay" id="drawerOverlay"></div>
    <div class="drawer-panel">
      <div class="drawer-header">
        <a href="/index.php" class="site-logo">My<span>Shop</span></a>
        <button class="drawer-close" id="drawerClose">✕</button>
      </div>

      <!-- ドロワー内検索 -->
      <form action="/products.php" method="GET" class="drawer-search">
        <input type="text" name="q" placeholder="商品を検索..."
          value="<?= h($_GET['q'] ?? '') ?>">
        <button type="submit">検索</button>
      </form>

      <!-- ドロワーナビ -->
      <nav class="drawer-nav">
        <a href="/products.php"><span class="nav-icon">🏷️</span>すべての商品</a>
        <?php foreach ($navCategories as $cat): ?>
          <a href="/products.php?category=<?= h($cat['slug']) ?>">
            <span class="nav-icon">›</span><?= h($cat['name']) ?>
          </a>
        <?php endforeach; ?>
        <a href="/cart.php">
          <span class="nav-icon">🛒</span>カート
          <?php if ($cartCount > 0): ?>
            <span style="margin-left:auto;background:var(--clr-accent);color:#fff;
            font-size:.7rem;padding:2px 7px;border-radius:10px;">
              <?= $cartCount ?>
            </span>
          <?php endif; ?>
        </a>
        <?php if (Auth::check()): ?>
          <a href="/mypage.php"><span class="nav-icon">👤</span><?= h(Auth::user()['name']) ?></a>
          <a href="/mypage_orders.php"><span class="nav-icon">📦</span>注文履歴</a>
          <?php if (Auth::isAdmin()): ?>
            <a href="/admin/index.php"><span class="nav-icon">⚙️</span>管理画面</a>
          <?php endif; ?>
          <a href="/logout.php"><span class="nav-icon">🔓</span>ログアウト</a>
        <?php else: ?>
          <a href="/login.php"><span class="nav-icon">🔒</span>ログイン</a>
          <a href="/register.php"><span class="nav-icon">✏️</span>会員登録</a>
        <?php endif; ?>
      </nav>
    </div>
  </div>

  <!-- ===== CATEGORY NAV ===== -->
  <nav class="category-nav">
    <div class="container">
      <ul>
        <li>
          <a href="/products.php"
            class="<?= empty($_GET['category']) && basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : '' ?>">
            すべて
          </a>
        </li>
        <?php foreach ($navCategories as $cat): ?>
          <li>
            <a href="/products.php?category=<?= h($cat['slug']) ?>"
              class="<?= ($_GET['category'] ?? '') === $cat['slug'] ? 'active' : '' ?>">
              <?= h($cat['name']) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </nav>

  <!-- フラッシュメッセージ -->
  <?php
  $flashSuccess = Session::getFlash('success');
  $flashError   = Session::getFlash('error');
  ?>
  <?php if ($flashSuccess): ?>
    <div class="container">
      <div class="alert alert-success" style="margin-top:16px;">✓ <?= h($flashSuccess) ?></div>
    </div>
  <?php endif; ?>
  <?php if ($flashError): ?>
    <div class="container">
      <div class="alert alert-error" style="margin-top:16px;">✗ <?= h($flashError) ?></div>
    </div>
  <?php endif; ?>

  <main class="page-body">
    コンテンツ
    <script src="/assets/js/header.js"></script>
</body>

</html>