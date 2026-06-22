<?php
// ============================================================
// アプリ全体の設定定数
// ============================================================

// -- Database --
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'php_shopping_site_demo');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// -- App --
define('APP_NAME',  'PHP Shopping Site Demo');
define('APP_URL',   'http://localhost:8080');  // php -S localhost:8080 -t public
define('APP_ENV',   'development');             // 本番では 'production'

// -- Security --
define('SESSION_NAME',    'php_shopping_site_demo');
define('SESSION_LIFETIME', 3600 * 24 * 7);     // 7日

// -- Upload --
define('UPLOAD_DIR',  __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL',  APP_URL . '/assets/uploads/');
define('UPLOAD_MAX_SIZE',  5 * 1024 * 1024);   // 5MB
define('UPLOAD_ALLOWED',   ['image/jpeg', 'image/png', 'image/webp']);

// -- Pagination --
define('PER_PAGE', 12);

// -- Error display (開発中のみ表示) --
if (APP_ENV === 'development') {
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
} else {
  ini_set('display_errors', 0);
  error_reporting(0);
}

// -- Timezone --
date_default_timezone_set('Asia/Tokyo');
