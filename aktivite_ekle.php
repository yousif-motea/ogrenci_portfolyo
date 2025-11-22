<?php
require_once 'functions.php';
requireLogin();

if (currentUserRole() !== 'yonetici') {
      die("Erişim yetkiniz yok.");
}

$success = "";

// Aktivite türlerini çek
$stmt = $pdo->query("SELECT * FROM aktivite_turleri ORDER BY ad");
$turler = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $tur_id = $_POST['tur_id'] ?? null;
      $baslik = trim($_POST['baslik'] ?? '');
      $aciklama = trim($_POST['aciklama'] ?? '');
      $tarih = $_POST['tarih'] ?? null;

      if ($tur_id && $baslik !== '' && $tarih) {
            $stmt = $pdo->prepare("INSERT INTO aktiviteler (tur_id, baslik, aciklama, tarih)
                               VALUES (?, ?, ?, ?)");
            $stmt->execute([$tur_id, $baslik, $aciklama, $tarih]);
            $success = "Aktivite başarıyla oluşturuldu.";
      } else {
            $success = "Lütfen zorunlu alanları doldurun.";
      }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Yeni Aktivite Ekle</title>
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
                  <a href="index.php" style="margin-right: 15px; color: #e0e7ff;">
                        <i class="fa-solid fa-house"></i> Panel
                  </a>
                  <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars(currentUserName()); ?></span>
            </div>
      </header>

      <main class="main-content">

            <div class="page-header-flex">
                  <div>
                        <h1>Yeni Aktivite Ekle</h1>
                        <p>Okul içi veya dışı bir etkinlik tanımlayın.</p>
                  </div>
                  <a href="aktiviteler.php" class="btn" style="background: #64748b;">
                        <i class="fa-solid fa-arrow-left"></i> Listeye Dön
                  </a>
            </div>

            <?php if ($success): ?>
                  <div class="alert-success"><i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="card">
                  <form method="post">
                        <div class="form-grid">
                              <div>
                                    <label><i class="fa-solid fa-layer-group"></i> Aktivite Türü</label>
                                    <select name="tur_id" required>
                                          <option value="">-- Tür Seçiniz --</option>
                                          <?php foreach ($turler as $t): ?>
                                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['ad']) ?></option>
                                          <?php endforeach; ?>
                                    </select>
                              </div>
                              <div>
                                    <label><i class="fa-regular fa-calendar"></i> Tarih</label>
                                    <input type="date" name="tarih" required value="<?= date('Y-m-d') ?>">
                              </div>
                        </div>

                        <label><i class="fa-solid fa-heading"></i> Başlık</label>
                        <input type="text" name="baslik" required placeholder="Örn: Ankara Gezisi">

                        <label><i class="fa-solid fa-align-left"></i> Açıklama</label>
                        <textarea name="aciklama" rows="4" placeholder="Aktivite hakkında kısa bilgi..."></textarea>

                        <button type="submit" style="margin-top: 15px;">
                              <i class="fa-solid fa-save"></i> Kaydet
                        </button>
                  </form>
            </div>

      </main>
      <script src="script.js"></script>

</body>

</html>