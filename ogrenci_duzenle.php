<?php
require_once 'functions.php';
require_once 'db.php';
requireLogin();

if (currentUserRole() !== 'yonetici') {
      die("Yetkiniz yok.");
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM ogrenciler WHERE id = ?");
$stmt->execute([$id]);
$ogrenci = $stmt->fetch();

if (!$ogrenci) {
      die("Öğrenci bulunamadı.");
}

$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $ogr_no = $_POST['ogr_no'];
      $ad     = $_POST['ad'];
      $soyad  = $_POST['soyad'];
      $sinif  = $_POST['sinif'];
      $sube   = $_POST['sube'];
      $dogum  = $_POST['dogum'];

      $stmt = $pdo->prepare("UPDATE ogrenciler SET ogr_no=?, ad=?, soyad=?, sinif=?, sube=?, dogum_tarihi=? WHERE id=?");
      $stmt->execute([$ogr_no, $ad, $soyad, $sinif, $sube, $dogum, $id]);

      // Not: Şifre veya kullanıcı adı (users tablosu) buradan güncellenmiyor. 
      // İstenirse o da eklenebilir ama şimdilik sadece profil güncelliyoruz.

      $success = "Öğrenci bilgileri güncellendi.";
      // Güncel bilgiyi tekrar çekelim
      $stmt = $pdo->prepare("SELECT * FROM ogrenciler WHERE id = ?");
      $stmt->execute([$id]);
      $ogrenci = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Öğrenci Düzenle</title>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="style.css">
      <style>
            .form-grid {
                  display: grid;
                  grid-template-columns: 1fr 1fr;
                  gap: 20px;
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
                        <h1>Öğrenci Düzenle</h1>
                        <p>Mevcut öğrenci bilgilerini güncelleyin.</p>
                  </div>
                  <a href="ogrenciler.php" class="btn" style="background: #64748b;">
                        <i class="fa-solid fa-arrow-left"></i> Geri Dön
                  </a>
            </div>

            <?php if ($success): ?>
                  <div class="alert-success"><i class="fa-solid fa-check"></i> <?= $success ?></div>
            <?php endif; ?>

            <div class="card">
                  <form method="post">
                        <div class="form-grid">
                              <div>
                                    <label>Öğrenci No</label>
                                    <input type="text" name="ogr_no" value="<?= htmlspecialchars($ogrenci['ogr_no']) ?>" required>
                              </div>
                              <div>
                                    <label>Doğum Tarihi</label>
                                    <input type="date" name="dogum" value="<?= $ogrenci['dogum_tarihi'] ?>">
                              </div>
                        </div>

                        <div class="form-grid">
                              <div>
                                    <label>Ad</label>
                                    <input type="text" name="ad" value="<?= htmlspecialchars($ogrenci['ad']) ?>" required>
                              </div>
                              <div>
                                    <label>Soyad</label>
                                    <input type="text" name="soyad" value="<?= htmlspecialchars($ogrenci['soyad']) ?>" required>
                              </div>
                        </div>

                        <div class="form-grid">
                              <div>
                                    <label>Sınıf</label>
                                    <input type="text" name="sinif" value="<?= htmlspecialchars($ogrenci['sinif']) ?>" required>
                              </div>
                              <div>
                                    <label>Şube</label>
                                    <input type="text" name="sube" value="<?= htmlspecialchars($ogrenci['sube']) ?>" required>
                              </div>
                        </div>

                        <button type="submit" style="margin-top: 15px;">
                              <i class="fa-solid fa-rotate"></i> Bilgileri Güncelle
                        </button>
                  </form>
            </div>
      </main>
      <script src="script.js"></script>
</body>

</html>