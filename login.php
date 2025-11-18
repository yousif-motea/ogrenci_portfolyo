<?php
require_once 'functions.php';

// Eğer zaten giriş yapmışsa dashboard'a at
if (isLoggedIn()) {
      header('Location: index.php');
      exit;
}

$hata = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = trim($_POST['username'] ?? '');
      $password = $_POST['password'] ?? '';

      if ($username === '' || $password === '') {
            $hata = 'Kullanıcı adı ve şifre zorunludur.';
      } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                  $_SESSION['user_id']  = $user['id'];
                  $_SESSION['role']     = $user['role'];
                  $_SESSION['ad_soyad'] = $user['ad'] . ' ' . $user['soyad'];

                  header('Location: index.php');
                  exit;
            } else {
                  $hata = 'Kullanıcı adı veya şifre hatalı.';
            }
      }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Öğrenci Portfolyo Sistemi - Giriş</title>
      <link rel="stylesheet" href="style.css">
</head>

<body class="login-body">
      <div class="login-container">
            <h1>Öğrenci Portfolyo &amp; Gelişim İzleme</h1>
            <h2>Sisteme Giriş</h2>

            <?php if ($hata): ?>
                  <div class="alert-error"><?php echo htmlspecialchars($hata); ?></div>
            <?php endif; ?>

            <form method="post" action="login.php">
                  <label for="username">Kullanıcı Adı</label>
                  <input type="text" name="username" id="username" required>

                  <label for="password">Şifre</label>
                  <input type="password" name="password" id="password" required>

                  <button type="submit">Giriş Yap</button>
            </form>
      </div>
</body>

</html>