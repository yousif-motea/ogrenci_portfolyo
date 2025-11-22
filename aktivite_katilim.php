<?php
require_once 'functions.php';
requireLogin();

if (currentUserRole() !== 'yonetici') {
      die("Erişim yetkiniz yok.");
}

$aktivite_id = $_GET['id'] ?? 0;

// Aktivite bilgisi
$stmt = $pdo->prepare("SELECT a.*, t.ad AS tur_adi 
                       FROM aktiviteler a
                       LEFT JOIN aktivite_turleri t ON a.tur_id = t.id
                       WHERE a.id = ?");
$stmt->execute([$aktivite_id]);
$aktivite = $stmt->fetch();

if (!$aktivite) {
      die("Aktivite bulunamadı.");
}

$success = "";

// Form gönderildiyse güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $secili_ogrenciler = $_POST['ogrenci_ids'] ?? [];

      // Önce temizle
      $del = $pdo->prepare("DELETE FROM ogrenci_aktiviteleri WHERE aktivite_id = ?");
      $del->execute([$aktivite_id]);

      // Sonra ekle
      if (!empty($secili_ogrenciler)) {
            $ins = $pdo->prepare("INSERT INTO ogrenci_aktiviteleri 
                              (ogrenci_id, aktivite_id, katilim_durumu) 
                              VALUES (?, ?, 'katildi')");
            foreach ($secili_ogrenciler as $ogr_id) {
                  $ins->execute([$ogr_id, $aktivite_id]);
            }
      }
      $success = "Katılım listesi başarıyla güncellendi.";
}

// Mevcut katılanları çek (Checkboxları işaretlemek için)
$stmt = $pdo->prepare("SELECT ogrenci_id FROM ogrenci_aktiviteleri 
                       WHERE aktivite_id = ? AND katilim_durumu = 'katildi'");
$stmt->execute([$aktivite_id]);
$katilan_kayitlar = $stmt->fetchAll();
$katilan_ogrenci_ids = array_column($katilan_kayitlar, 'ogrenci_id');

// Tüm öğrenciler
$stmt = $pdo->query("SELECT * FROM ogrenciler ORDER BY sinif, sube, ad, soyad");
$ogrenciler = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Katılım Yönetimi</title>
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
                  <a href="index.php" style="margin-right: 15px; color: #e0e7ff;"><i class="fa-solid fa-house"></i> Panel</a>
                  <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars(currentUserName()); ?></span>
            </div>
      </header>

      <main class="main-content">

            <div class="page-header-flex">
                  <div>
                        <h1>Katılım Listesi</h1>
                        <p>Bu aktiviteye katılan öğrencileri seçiniz.</p>
                  </div>
                  <a href="aktiviteler.php" class="btn" style="background: #64748b;">
                        <i class="fa-solid fa-arrow-left"></i> Geri Dön
                  </a>
            </div>

            <div class="card" style="margin-bottom: 20px; border-left: 5px solid var(--primary-color);">
                  <h3 style="margin-bottom: 5px;"><?= htmlspecialchars($aktivite['baslik']) ?></h3>
                  <div style="color: var(--text-muted); font-size: 14px;">
                        <span class="badge badge-orange"><?= htmlspecialchars($aktivite['tur_adi']) ?></span>
                        &nbsp; <i class="fa-regular fa-calendar"></i> <?= $aktivite['tarih'] ?>
                  </div>
            </div>

            <?php if ($success): ?>
                  <div class="alert-success"><i class="fa-solid fa-check"></i> <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div style="margin-bottom: 15px; position: relative;">
                  <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                  <input type="text" id="tableSearch" placeholder="Listede öğrenci ara..." style="padding-left: 36px;">
            </div>

            <form method="post">
                  <div class="card" style="padding: 0; overflow: hidden;">
                        <table cellpadding="0" cellspacing="0">
                              <thead>
                                    <tr>
                                          <th style="width: 50px; text-align: center;">
                                                <input type="checkbox" id="selectAll" style="cursor: pointer;">
                                          </th>
                                          <th>Öğrenci No</th>
                                          <th>Ad Soyad</th>
                                          <th>Sınıf / Şube</th>
                                    </tr>
                              </thead>
                              <tbody>
                                    <?php foreach ($ogrenciler as $o): ?>
                                          <?php $checked = in_array($o['id'], $katilan_ogrenci_ids) ? 'checked' : ''; ?>
                                          <tr class="hover-row">
                                                <td style="text-align: center;">
                                                      <input type="checkbox" name="ogrenci_ids[]" value="<?= $o['id'] ?>" <?= $checked ?> class="student-checkbox" style="cursor: pointer; width: 18px; height: 18px;">
                                                </td>
                                                <td><?= htmlspecialchars($o['ogr_no']) ?></td>
                                                <td><strong><?= htmlspecialchars($o['ad'] . ' ' . $o['soyad']) ?></strong></td>
                                                <td><?= htmlspecialchars($o['sinif'] . ' / ' . $o['sube']) ?></td>
                                          </tr>
                                    <?php endforeach; ?>
                              </tbody>
                        </table>
                  </div>

                  <div style="margin-top: 20px; position: sticky; bottom: 20px; z-index: 50;">
                        <button type="submit" class="btn" style="width: 100%; padding: 15px; font-size: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                              <i class="fa-solid fa-save"></i> Seçimleri Kaydet
                        </button>
                  </div>
            </form>

      </main>

      <script src="script.js"></script>
      <script>
            // Bu sayfaya özel Tümünü Seç Scripti
            document.getElementById('selectAll').addEventListener('change', function() {
                  const checkboxes = document.querySelectorAll('.student-checkbox');
                  // Sadece görünür olanları (aramadan geçenleri) seçmek mantıklıdır
                  checkboxes.forEach(cb => {
                        // Eğer satır gizli değilse (arama filtresi)
                        if (cb.closest('tr').style.display !== 'none') {
                              cb.checked = this.checked;
                        }
                  });
            });
      </script>

</body>

</html>