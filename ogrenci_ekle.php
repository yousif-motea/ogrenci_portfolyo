<?php
require_once 'functions.php';
require_once 'db.php';
requireLogin();

// Yetki Kontrolü
if (!in_array(currentUserRole(), ['yonetici', 'ogretmen', 'rehber'])) {
      die("Erişim yetkiniz yok.");
}

$hata = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $ogr_no = trim($_POST['ogr_no']);
      $ad = trim($_POST['ad']);
      $soyad = trim($_POST['soyad']);
      $sinif = trim($_POST['sinif']);
      $sube = trim($_POST['sube']);
      $dogum = $_POST['dogum'];
      $raw_password = $_POST['password'];

      if (empty($ogr_no) || empty($raw_password)) {
            $hata = "Öğrenci numarası ve şifre zorunludur.";
      } else {
            $password_hash = password_hash($raw_password, PASSWORD_DEFAULT);
            $pdo->beginTransaction();

            try {
                  // 1. Users tablosuna ekle
                  $stmtUser = $pdo->prepare("INSERT INTO users (username, password_hash, ad, soyad, role) VALUES (?, ?, ?, ?, 'ogrenci')");
                  $stmtUser->execute([$ogr_no, $password_hash, $ad, $soyad]);

                  // 2. Ogrenciler tablosuna ekle
                  $stmtOgr = $pdo->prepare("INSERT INTO ogrenciler (ogr_no, ad, soyad, sinif, sube, dogum_tarihi) VALUES (?, ?, ?, ?, ?, ?)");
                  $stmtOgr->execute([$ogr_no, $ad, $soyad, $sinif, $sube, $dogum]);

                  $pdo->commit();
                  $success = "Öğrenci ve giriş hesabı başarıyla oluşturuldu.";
            } catch (PDOException $e) {
                  $pdo->rollBack();
                  if ($e->getCode() == '23000') {
                        $hata = "Bu numara ile kayıtlı bir öğrenci zaten var.";
                  } else {
                        $hata = "Veritabanı hatası: " . $e->getMessage();
                  }
            }
      }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Öğrenci Ekle</title>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="style.css">
      <style>
            .form-grid {
                  display: grid;
                  grid-template-columns: 1fr 1fr;
                  gap: 20px;
            }

            @media (max-width: 768px) {
                  .form-grid {
                        grid-template-columns: 1fr;
                  }
            }
      </style>
</head>

<body>

      <header class="topbar">
            <div class="logo"><i class="fa-solid fa-graduation-cap"></i> Portfolyo Sistemi</div>
            <div class="user-info">
                  <a href="index.php" style="margin-right: 15px; color: #e0e7ff;"><i class="fa-solid fa-house"></i> Panel</a>
                  <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars(currentUserName()); ?></span>
            </div>
      </header>

      <main class="main-content">
            <div class="page-header-flex">
                  <div>
                        <h1>Yeni Öğrenci Ekle</h1>
                        <p>Sisteme yeni bir öğrenci kaydı oluşturun.</p>
                  </div>
                  <a href="ogrenciler.php" class="btn" style="background: #64748b;">
                        <i class="fa-solid fa-arrow-left"></i> Listeye Dön
                  </a>
            </div>

            <?php if ($hata): ?>
                  <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= $hata ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                  <div class="alert-success"><i class="fa-solid fa-check-circle"></i> <?= $success ?></div>
            <?php endif; ?>

            <div class="card">
                  <form method="post">
                        <h3 style="margin-bottom: 15px; color: var(--primary-color); font-size: 16px;">Öğrenci Bilgileri</h3>

                        <div class="form-grid">
                              <div>
                                    <label><i class="fa-solid fa-id-card"></i> Öğrenci No (Kullanıcı Adı)</label>
                                    <input type="text" name="ogr_no" required placeholder="Örn: 1052">
                              </div>
                              <div>
                                    <label><i class="fa-solid fa-key"></i> Giriş Şifresi</label>
                                    <input type="text" name="password" required placeholder="Örn: 123456">
                              </div>
                        </div>

                        <div class="form-grid">
                              <div>
                                    <label>Ad</label>
                                    <input type="text" name="ad" required placeholder="Örn: Ahmet">
                              </div>
                              <div>
                                    <label>Soyad</label>
                                    <input type="text" name="soyad" required placeholder="Örn: Yılmaz">
                              </div>
                        </div>

                        <div class="form-grid">
                              <div>
                                    <label>Sınıf</label>
                                    <input type="text" name="sinif" required placeholder="Örn: 9">
                              </div>
                              <div>
                                    <label>Şube</label>
                                    <input type="text" name="sube" required placeholder="Örn: A">
                              </div>
                        </div>

                        <div>
                              <label><i class="fa-regular fa-calendar"></i> Doğum Tarihi</label>
                              <input type="date" name="dogum">
                        </div>

                        <button type="submit" style="margin-top: 20px;">
                              <i class="fa-solid fa-save"></i> Kaydı Tamamla
                        </button>
                  </form>
            </div>
      </main>
      <script src="script.js"></script>
</body>

</html>