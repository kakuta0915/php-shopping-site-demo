-- ============================================================
-- PHP Shopping Site Demo Schema
-- MySQL 8.0+  /  charset: utf8mb4
-- ============================================================

-- php_shopping_site_demoというDB作成 （NOT EXISTS: 存在する場合作らない）
CREATE DATABASE IF NOT EXISTS php_shopping_site_demo
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE php_shopping_site_demo;

-- --------------------------------------------------------
-- users
-- role: 'customer' | 'admin'
-- --------------------------------------------------------
CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100)  NOT NULL,
  email         VARCHAR(255)  NOT NULL UNIQUE,
  password_hash VARCHAR(255)  NOT NULL,
  -- ユーザーの権限（役割）」を決める設定  (role: 役割, ENUM: この中から1しか選べない)
  role          ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- categories (self-join で親子対応)
-- --------------------------------------------------------
CREATE TABLE categories (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parent_id INT UNSIGNED DEFAULT NULL,
  name      VARCHAR(100) NOT NULL,
  slug      VARCHAR(100) NOT NULL UNIQUE,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- products
-- --------------------------------------------------------
CREATE TABLE products (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED DEFAULT NULL,
  name        VARCHAR(255)   NOT NULL,
  description TEXT           NOT NULL DEFAULT '',
  price       DECIMAL(10,2)  NOT NULL,
  stock       INT UNSIGNED   NOT NULL DEFAULT 0,
  is_active   TINYINT(1)     NOT NULL DEFAULT 1,
  created_at  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  INDEX idx_category (category_id),
  INDEX idx_active   (is_active)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- product_images
-- --------------------------------------------------------
CREATE TABLE product_images (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  path       VARCHAR(500) NOT NULL,
  is_primary TINYINT(1)   NOT NULL DEFAULT 0,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- cart_items (ログイン済みユーザーのみ)
-- --------------------------------------------------------
CREATE TABLE cart_items (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity   INT UNSIGNED NOT NULL DEFAULT 1,
  updated_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_product (user_id, product_id),
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- orders
-- status: 'pending' | 'paid' | 'shipped' | 'delivered' | 'cancelled'
-- --------------------------------------------------------
CREATE TABLE orders (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id          INT UNSIGNED   NOT NULL,
  total_price      DECIMAL(10,2)  NOT NULL,
  status           ENUM('pending','paid','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  shipping_name    VARCHAR(100)   NOT NULL,
  shipping_address TEXT           NOT NULL,
  shipping_tel     VARCHAR(20)    NOT NULL DEFAULT '',
  note             TEXT           NOT NULL DEFAULT '',
  ordered_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
  INDEX idx_user   (user_id),
  INDEX idx_status (status)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- order_items (注文時の価格スナップショット)
-- --------------------------------------------------------
CREATE TABLE order_items (
  id         INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
  order_id   INT UNSIGNED  NOT NULL,
  product_id INT UNSIGNED  DEFAULT NULL,
  name       VARCHAR(255)  NOT NULL,          -- 注文時の商品名を保存
  unit_price DECIMAL(10,2) NOT NULL,          -- 注文時の単価を保存
  quantity   INT UNSIGNED  NOT NULL,
  FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
  INDEX idx_order (order_id)
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- reviews
-- --------------------------------------------------------
CREATE TABLE reviews (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  rating     TINYINT      NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment    TEXT         NOT NULL DEFAULT '',
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_product (user_id, product_id),
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  INDEX idx_product (product_id)
) ENGINE=InnoDB;

-- ============================================================
-- Seed data
-- ============================================================
-- Admin user (password: admin1234)
INSERT INTO users (name, email, password_hash, role) VALUES
('管理者', 'admin@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample categories
INSERT INTO categories (parent_id, name, slug, sort_order) VALUES
(NULL, 'トップス',     'tops',       1),
(NULL, 'ボトムス',     'bottoms',    2),
(NULL, 'アウター',     'outer',      3),
(NULL, 'アクセサリー', 'accessories',4);

INSERT INTO categories (parent_id, name, slug, sort_order) VALUES
(1, 'Tシャツ',      't-shirts',  1),
(1, 'シャツ',        'shirts',    2),
(2, 'デニム',        'denim',     1),
(2, 'チノパン',      'chinos',    2),
(3, 'ジャケット',    'jackets',   1),
(3, 'コート',        'coats',     2);

-- Sample products
INSERT INTO products (category_id, name, description, price, stock) VALUES
(5,  'ベーシックTシャツ（ホワイト）', 'オーガニックコットン100%。毎日着たいシンプルなTシャツ。', 2980, 50),
(5,  'ベーシックTシャツ（ブラック）', 'オーガニックコットン100%。落ち着いたブラックカラー。', 2980, 40),
(6,  'オックスフォードシャツ', '定番のオックスフォード素材。ビジネスカジュアルにも対応。', 5980, 30),
(7,  'スリムデニム', 'ストレッチ素材でスッキリシルエット。', 7980, 25),
(8,  'チノパン（ベージュ）', '上品な素材感のチノパン。テーパードシルエット。', 6980, 35),
(9,  'テーラードジャケット', '細部までこだわったテーラードジャケット。', 19800, 15),
(10, 'ウールコート', '上質なウール素材のロングコート。',39800, 10);

-- Sample product images (placeholder paths)
INSERT INTO product_images (product_id, path, is_primary, sort_order) VALUES
(1, '/assets/uploads/product_1_1.jpg', 1, 0),
(2, '/assets/uploads/product_2_1.jpg', 1, 0),
(3, '/assets/uploads/product_3_1.jpg', 1, 0),
(4, '/assets/uploads/product_4_1.jpg', 1, 0),
(5, '/assets/uploads/product_5_1.jpg', 1, 0),
(6, '/assets/uploads/product_6_1.jpg', 1, 0),
(7, '/assets/uploads/product_7_1.jpg', 1, 0);


