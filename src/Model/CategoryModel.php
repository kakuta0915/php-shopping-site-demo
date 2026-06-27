<?php
class CategoryModel
{
  public static function all(): array
  {
    // ORDER BY: 並び替える / parent_idの昇順で、更にその中でsort_orderの昇順
    return DB::fetchAll('SELECT * FROM categories ORDER BY parent_id, sort_order');
  }

  public static function roots(): array
  {
    // parent_id が NULL のカテゴリだけ取得し、sort_orderの昇順
    return DB::fetchAll('SELECT *FROM categories WHERE parent_id IS NULL ORDER BY sort_order');
  }

  public static function children(int $parentId): array
  {
    return DB::fetchAll('SELECT * FROM categories WHERE parent_id = ? ORDER BY sort_order', [$parentId]);
  }

  public static function findBySlug(string $slug): array | false
  {
    return DB::fetch('SELECT * FROM categories WHERE slug = ?', [$slug]);
  }

  public static function findById(int $id): array|false
  {
    return DB::fetch('SELECT * FROM categories WHERE id = ?', [$id]);
  }

  public static function create(array $data): string
  {
    return DB::insert(
      // categories テーブルに新しいカテゴリを追加する
      // $data['parent_id'] ?: null → parent_id が空なら NULL を入れる
      // $data['sort_order'] ?? 0 → sort_orderが渡っていない時は 0 
      'INSERT INTO categories (parent_id, name, slug, sort_order) VALUES (?, ?, ?, ?)',
      [$data['parent_id'] ?: null, $data['name'], $data['slug'], $data['sort_order'] ?? 0]
    );
  }

  public static function update(int $id, array $data): void
  {
    DB::execute(
      'UPDATE categories SET parent_id = ?, name= ?, slug = ?, sort_order=? WHERE id = ?',
      [$data['parent_id'] ?: null, $data['name'], $data['slug'], $data['sort_order'] ?? 0, $id]
    );
  }

  public static function delete(int $id): void
  {
    DB::execute('DELETE FROM categories WHERE id = ?', [$id]);
  }

  // カテゴリ一覧を親子関係（ツリー構造）に変換する処理（管理用画面）
  public static function tree(): array
  {
    $all = self::all();
    $tree = [];
    $map = [];
    foreach ($all as $cat) {
      $map[$cat['id']] = $cat + ['children' => []];
    }
    foreach ($map as $id => $cat) {
      if ($cat['parent_id']) {
        $map[$cat['parent_id']]['children'][] = &$map[$id];
      } else {
        $tree[] = &$map[$id];
      }
    }
    return $tree;
  }
}
