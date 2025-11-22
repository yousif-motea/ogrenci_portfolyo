<?php
require_once 'functions.php';

if (isLoggedIn()) {
      header('Location: index.php');
      exit;
}

$hata = '';
$secili_tip = $_POST['kullanici_tipi'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = trim($_POST['username'] ?? '');
      $password = $_POST['password'] ?? '';

      if ($username === '' || $password === '') {
            $hata = 'Lütfen tüm alanları doldurun.';
      } elseif ($secili_tip === '') {
            $hata = 'Giriş türünü seçiniz.';
      } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                  $role = $user['role'];
                  $uyumlu = false;

                  if ($secili_tip === 'ogrenci' && ($role === 'ogrenci' || $role === 'veli')) {
                        $uyumlu = true;
                  } elseif ($secili_tip === 'ogretmen' && in_array($role, ['ogretmen', 'rehber', 'yonetici'])) {
                        $uyumlu = true;
                  }

                  if (!$uyumlu) {
                        $hata = 'Seçilen kullanıcı tipi yetkinizle uyuşmuyor.';
                  } else {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['ad_soyad'] = $user['ad'] . ' ' . $user['soyad'];
                        $_SESSION['username'] = $user['username'];
                        header('Location: index.php');
                        exit;
                  }
            } else {
                  $hata = 'Kullanıcı bilgileri hatalı.';
            }
      }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Giriş | Portfolyo Sistemi</title>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="style.css">
</head>

<body class="login-body">
      <div class="login-container">
            <h1><i class="fa-solid fa-school"></i> Portfolyo Sistemi</h1>
            <h2>Hesabınıza giriş yapın</h2>

            <?php if ($hata): ?>
                  <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($hata); ?></div>
            <?php endif; ?>

            <form method="post" action="login.php">
                  <label>Giriş Tipi</label>
                  <div class="user-type-group">
                        <label class="user-type-option">
                              <input type="radio" name="kullanici_tipi" value="ogrenci" <?php if ($secili_tip === 'ogrenci') echo 'checked'; ?>>
                              Öğrenci / Veli
                        </label>
                        <label class="user-type-option">
                              <input type="radio" name="kullanici_tipi" value="ogretmen" <?php if ($secili_tip === 'ogretmen') echo 'checked'; ?>>
                              Öğretmen
                        </label>
                  </div>

                  <label for="username">Kullanıcı Adı / No</label>
                  <input type="text" name="username" id="username" required placeholder="Örn: 1234">

                  <label for="password">Şifre</label>
                  <input type="password" name="password" id="password" required placeholder="******">

                  <button type="submit">Giriş Yap <i class="fa-solid fa-arrow-right"></i></button>
            </form>
      </div>
</body>

</html>