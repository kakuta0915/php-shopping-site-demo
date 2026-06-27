<?php
// ============================================================
// DB - PDO シングルトン
// 使い方: $db = DB::getInstance();
// $db->fetch('SELECT * FROM users WHERE id = ?', [$id]);
// ============================================================

class DB
{
  private static ?PDO $pdo = null;

  // 外部からの new を禁止
  private function __construct() {}
  private function __clone() {}

  public static function getInstance(): PDO
  {
    if (self::$pdo === null) {
      $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
      );

      self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,  // プリペアドステートメントを確実に使用
      ]);
    }

    return self::$pdo;
  }

  // SELECT (複数行)
  public static function fetchAll(string $sql, array $params = []): array
  {
    $stmt = self::getInstance()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  // SELECT (1行) ────────────────────────────────────────────
  public static function fetch(string $sql, array $params = []): array|false
  {
    $stmt = self::getInstance()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
  }

  // SELECT (スカラー値)
  public static function fetchColumn(string $sql, array $params = []): mixed
  {
    $stmt = self::getInstance()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
  }

  // INSERT / UPDATE / DELETE ────────────────────────────────
  public static function execute(string $sql, array $params = []): int
  {
    $stmt = self::getInstance()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
  }


  // INSERT後のAUTO_INCREMENTを返す ──────────────────────────
  public static function insert(string $sql, array $params = []): string
  {
    self::execute($sql, $params);
    return self::getInstance()->lastInsertId();
  }


  // トランザクション ─────────────────────────────────────────
  // DBクラスのbeginTransactionメソッド（自作）
  public static function beginTransaction(): void
  {
    // この中のbeginTransactionは、PDOクラスにもともと用意されているメソッド
    self::getInstance()->beginTransaction();
  }

  public static function commit(): void
  {
    self::getInstance()->commit();
  }

  public static function rollBack(): void
  {
    self::getInstance()->rollBack();
  }
}
