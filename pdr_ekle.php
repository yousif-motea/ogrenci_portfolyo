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
      $error = "Sistemde öğrenci bulunmuyor. Önce öğrenci eklemelisiniz.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
      $ogrenci_id = $_POST['ogrenci_id'] ?? null;
      $tarih      = $_POST['tarih'] ?? null;
      $konu       = trim($_POST['konu'] ?? '');
      $aciklama   = trim($_POST['aciklama'] ?? '');
      $rehber_id  = currentUserId();

      if ($ogrenci_id && $tarih && $konu !== '') {
            $stmt = $pdo->prepare("INSERT INTO pdr_kayitlari 
                               (ogrenci_id, rehber_user_id, tarih, konu, aciklama)
                               VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$ogrenci_id, $rehber_id, $tarih, $konu, $aciklama]);
            $success = "PDR görüşme kaydı başarıyla oluşturuldu.";
      } else {
            $error = "Lütfen zorunlu alanları (Öğrenci, Tarih, Konu) doldurun.";
      }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Yeni PDR Kaydı</title>
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
            <div class="logo">
                  <i class="fa-solid fa-graduation-cap"></i> Portfolyo Sistemi
            </div>
            <div class="user-info">
                  <a href="index.php" style="margin-right: 15px; color: #e0e7ff;"><i class="fa-solid fa-house"></i> Panel</a>
                  <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars(currentUserName()); ?></span>
            </div>
      </header>

      <main class="main-content">

            <div class="page-header-flex">
                  <div>
                        <h1>Yeni PDR Kaydı</h1>
                        <p>Rehberlik servisi görüşme notu ekleme ekranı.</p>
                  </div>
                  <a href="pdr_list.php" class="btn" style="background: #64748b;">
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
                  <div class="card">
                        <form method="post">
                              <div class="form-grid">
                                    <div>
                                          <label><i class="fa-solid fa-user-graduate"></i> Öğrenci Seçiniz</label>
                                          <select name="ogrenci_id" required>
                                                <option value="">-- Öğrenci Seçin --</option>
                                                <?php foreach ($ogrenciler as $o): ?>
                                                      <option value="<?= $o['id'] ?>">
                                                            <?= htmlspecialchars($o['sinif'] . $o['sube'] . ' - ' . $o['ad'] . ' ' . $o['soyad']) ?>
                                                      </option>
                                                <?php endforeach; ?>
                                          </select>
                                    </div>
                                    <div>
                                          <label><i class="fa-regular fa-calendar"></i> Görüşme Tarihi</label>
                                          <input type="date" name="tarih" required value="<?= date('Y-m-d') ?>">
                                    </div>
                              </div>

                              <label><i class="fa-solid fa-tag"></i> Görüşme Konusu</label>
                              <input type="text" name="konu" required placeholder="Örn: Verimli Ders Çalışma, Sınav Kaygısı vb.">

                              <label><i class="fa-solid fa-align-left"></i> Açıklama / Görüşme Notları</label>
                              <textarea name="aciklama" rows="6" placeholder="Görüşme detaylarını buraya not alabilirsiniz..."></textarea>

                              <button type="submit" style="margin-top: 15px;">
                                    <i class="fa-solid fa-save"></i> Kaydı Oluştur
                              </button>
                        </form>
                  </div>
            <?php endif; ?>

      </main>
      <script src="script.js"></script>

</body>

</html>