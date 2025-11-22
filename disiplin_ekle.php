<?php
require_once 'functions.php';
requireLogin();

$role = currentUserRole();
if (!in_array($role, ['yonetici', 'rehber'])) {
      die("Erişim yetkiniz yok.");
}

$success = "";
$error = "";

// Tüm öğrencileri çek
$stmt = $pdo->query("SELECT * FROM ogrenciler ORDER BY sinif, sube, ad, soyad");
$ogrenciler = $stmt->fetchAll();

if (empty($ogrenciler)) {
      $error = "Sistemde henüz öğrenci kaydı yok. Önce öğrenci eklemelisiniz.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
      $ogrenci_id = $_POST['ogrenci_id'] ?? null;
      $tarih      = $_POST['tarih'] ?? null;
      $seviye     = $_POST['seviye'] ?? null;
      $aciklama   = trim($_POST['aciklama'] ?? '');

      if ($ogrenci_id && $tarih && $seviye) {
            $stmt = $pdo->prepare("INSERT INTO disiplin_kayitlari 
                               (ogrenci_id, tarih, seviye, aciklama)
                               VALUES (?, ?, ?, ?)");
            $stmt->execute([$ogrenci_id, $tarih, $seviye, $aciklama]);
            $success = "Disiplin kaydı başarıyla oluşturuldu.";
      } else {
            $error = "Lütfen tüm zorunlu alanları doldurun.";
      }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Yeni Disiplin Kaydı</title>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="style.css">
</head>

<body>

      <header class="topbar">
            <div class="logo">
                  <i class="fa-solid fa-graduation-cap"></i> Portfolyo Sistemi
            </div>
            <div class="user-info">
                  <a href="index.php" style="margin-right: 15px; color: #e0e7ff;">
                        <i class="fa-solid fa-house"></i> Panel
                  </a>
                  <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars(currentUserName()); ?></span>
            </div>
      </header>

      <main class="main-content">

            <div class="page-header-flex">
                  <div>
                        <h1>Yeni Disiplin Kaydı</h1>
                        <p>Öğrenci siciline yeni bir durum işleyin.</p>
                  </div>
                  <a href="disiplin_list.php" class="btn" style="background: #64748b;">
                        <i class="fa-solid fa-arrow-left"></i> Listeye Dön
                  </a>
            </div>

            <?php if ($success): ?>
                  <div class="alert-success"><i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                  <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!$error || !empty($ogrenciler)): ?>
                  <div class="card" style="max-width: 600px;">
                        <form method="post">
                              <label><i class="fa-solid fa-user-graduate"></i> Öğrenci Seçiniz:</label>
                              <select name="ogrenci_id" required>
                                    <option value="">-- Öğrenci Seçin --</option>
                                    <?php foreach ($ogrenciler as $o): ?>
                                          <option value="<?= $o['id'] ?>">
                                                <?= htmlspecialchars($o['sinif'] . $o['sube'] . ' - ' . $o['ad'] . ' ' . $o['soyad'] . ' (' . $o['ogr_no'] . ')') ?>
                                          </option>
                                    <?php endforeach; ?>
                              </select>

                              <label><i class="fa-regular fa-calendar-days"></i> Olay Tarihi:</label>
                              <input type="date" name="tarih" required value="<?= date('Y-m-d') ?>">

                              <label><i class="fa-solid fa-triangle-exclamation"></i> Durum / Seviye:</label>
                              <select name="seviye" required>
                                    <option value="">-- Seçiniz --</option>
                                    <option value="Sözlü Uyarı">Sözlü Uyarı</option>
                                    <option value="Yazılı Uyarı">Yazılı Uyarı</option>
                                    <option value="Kınama">Kınama</option>
                                    <option value="Uzaklaştırma">Uzaklaştırma</option>
                                    <option value="Disiplin Kurulu Sevk">Disiplin Kurulu Sevk</option>
                              </select>

                              <label><i class="fa-solid fa-pen"></i> Açıklama / Detay:</label>
                              <textarea name="aciklama" rows="5" placeholder="Olayın detaylarını buraya yazınız..." required></textarea>

                              <button type="submit">
                                    <i class="fa-solid fa-save"></i> Kaydet
                              </button>
                        </form>
                  </div>
            <?php endif; ?>

      </main>

      <script src="script.js"></script>

</body>

</html>