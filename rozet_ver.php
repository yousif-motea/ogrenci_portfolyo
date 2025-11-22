<?php
require_once 'functions.php';
requireLogin();

$role = currentUserRole();
// Yönetici, Rehber ve Öğretmen rozet verebilir
if (!in_array($role, ['yonetici', 'rehber', 'ogretmen'])) {
      die("Erişim yetkiniz yok.");
}

$success = "";
$error = "";

// Öğrenciler
$stmt = $pdo->query("SELECT * FROM ogrenciler ORDER BY sinif, sube, ad, soyad");
$ogrenciler = $stmt->fetchAll();

// Rozetler
$stmt2 = $pdo->query("SELECT * FROM rozetler ORDER BY kategori, ad");
$rozetler = $stmt2->fetchAll();

if (empty($ogrenciler) || empty($rozetler)) {
      $error = "Sistemde tanımlı öğrenci veya rozet bulunamadı.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
      $ogrenci_id = $_POST['ogrenci_id'] ?? null;
      $rozet_id   = $_POST['rozet_id'] ?? null;
      $tarih      = $_POST['tarih'] ?? null;
      $aciklama   = trim($_POST['aciklama'] ?? '');

      if ($ogrenci_id && $rozet_id && $tarih) {
            $stmt = $pdo->prepare("INSERT INTO ogrenci_rozetleri 
                               (ogrenci_id, rozet_id, verilis_tarihi, aciklama)
                               VALUES (?, ?, ?, ?)");
            $stmt->execute([$ogrenci_id, $rozet_id, $tarih, $aciklama]);
            $success = "Rozet başarıyla öğrenciye tanımlandı.";
      } else {
            $error = "Lütfen tüm zorunlu alanları doldurun.";
      }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Öğrenciye Rozet Ver</title>
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
                        <h1>Rozet Ver</h1>
                        <p>Öğrencinin başarısını ödüllendirin.</p>
                  </div>
                  <a href="rozetler.php" class="btn" style="background: #64748b;">
                        <i class="fa-solid fa-arrow-left"></i> Listeye Dön
                  </a>
            </div>

            <?php if ($success): ?>
                  <div class="alert-success"><i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                  <div class="alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if (!$error): ?>
                  <div class="card">
                        <form method="post">
                              <div class="form-grid">
                                    <div>
                                          <label><i class="fa-solid fa-user-graduate"></i> Öğrenci</label>
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
                                          <label><i class="fa-solid fa-medal"></i> Verilecek Rozet</label>
                                          <select name="rozet_id" required>
                                                <option value="">-- Rozet Seçin --</option>
                                                <?php foreach ($rozetler as $r): ?>
                                                      <option value="<?= $r['id'] ?>">
                                                            <?= htmlspecialchars('[' . ucfirst($r['kategori']) . '] ' . $r['ad']) ?>
                                                      </option>
                                                <?php endforeach; ?>
                                          </select>
                                    </div>
                              </div>

                              <label><i class="fa-regular fa-calendar"></i> Veriliş Tarihi</label>
                              <input type="date" name="tarih" required value="<?= date('Y-m-d') ?>">

                              <label><i class="fa-solid fa-pen"></i> Açıklama (Neden Verildi?)</label>
                              <textarea name="aciklama" rows="4" placeholder="Örn: Okul futbol turnuvasındaki üstün başarısından dolayı..."></textarea>

                              <button type="submit" style="margin-top: 15px; background: #8b5cf6;">
                                    <i class="fa-solid fa-gift"></i> Rozeti Ver
                              </button>
                        </form>
                  </div>
            <?php endif; ?>

      </main>
      <script src="script.js"></script>

</body>

</html>