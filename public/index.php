<?php
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/src/helpers.php';
Session::start();
?>

<?php require_once dirname(__DIR__) . '/templates/header.php'; ?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
  ああ

  <?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>

</body>

</html>