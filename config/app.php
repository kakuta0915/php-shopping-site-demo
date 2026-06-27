<?php
// ============================================================
// アプリ全体の設定定数
// ============================================================
// ============================================================

// -- Database（環境変数 → ローカル開発用デフォルト）--
define('DB_HOST',    $_ENV['MYSQLHOST']     ?? $_ENV['DB_HOST']     ?? '127.0.0.1');
define('DB_PORT',    $_ENV['MYSQLPORT']     ?? $_ENV['DB_PORT']     ?? '3306');
define('DB_NAME',    $_ENV['MYSQLDATABASE'] ?? $_ENV['DB_NAME']     ?? 'ec_site');
define('DB_USER',    $_ENV['MYSQLUSER']     ?? $_ENV['DB_USER']     ?? 'root');
define('DB_PASS',    $_ENV['MYSQLPASSWORD'] ?? $_ENV['DB_PASS']     ?? '');
define('DB_CHARSET', 'utf8mb4');

// -- App --
define('APP_NAME', $_ENV['APP_NAME'] ?? 'PHP Shopping Site Demo');
define('APP_URL',  $_ENV['APP_URL']  ?? 'http://localhost:8080');
define('APP_ENV',  $_ENV['APP_ENV']  ?? 'development');

// -- Security --
define('SESSION_NAME',     'ec_session');
define('SESSION_LIFETIME',  3600 * 24 * 7);

// -- Upload --
define('UPLOAD_DIR',      dirname(__DIR__) . '/assets/uploads/');
define('UPLOAD_URL',      '/assets/uploads/');
define('UPLOAD_MAX_SIZE',  5 * 1024 * 1024);
define('UPLOAD_ALLOWED',   ['image/jpeg', 'image/png', 'image/webp']);

// -- Pagination --
define('PER_PAGE', 12);

// -- エラー表示（本番は非表示）--
if (APP_ENV === 'production') {
  ini_set('display_errors', 0);
  error_reporting(0);
} else {
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
}

date_default_timezone_set('Asia/Tokyo');
