<?php
// ============================================================
// templates/footer.php
// ============================================================
?>
</main><!-- /.page-body -->

<!-- ===== SITE FOOTER ===== -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-inner">

      <!-- ブランド -->
      <div class="footer-brand">
        <div class="brand-name">PHP Shopping Site Demo</div>
        <p>こだわりのセレクトアイテムをお届けする<br>オンラインショップです。</p>
      </div>

      <!-- ショップ案内 -->
      <div class="footer-col">
        <h4>Shop</h4>
        <ul>
          <li><a href="/products.php">商品一覧</a></li>
          <li><a href="/products.php?category=tops">トップス</a></li>
          <li><a href="/products.php?category=bottoms">ボトムス</a></li>
          <li><a href="/products.php?category=outer">アウター</a></li>
        </ul>
      </div>

      <!-- マイアカウント -->
      <div class="footer-col">
        <h4>Account</h4>
        <ul>
          <?php if (Auth::check()): ?>
            <li><a href="/mypage.php">マイページ</a></li>
            <li><a href="/mypage_orders.php">注文履歴</a></li>
            <li><a href="/logout.php">ログアウト</a></li>
          <?php else: ?>
            <li><a href="/login.php">ログイン</a></li>
            <li><a href="/register.php">会員登録</a></li>
          <?php endif; ?>
          <li><a href="/cart.php">カート</a></li>
        </ul>
      </div>

    </div>

    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
    </div>
  </div>
</footer>

<script src="/assets/js/main.js"></script>
</body>

</html>