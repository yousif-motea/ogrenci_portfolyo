<?php
require_once 'functions.php';
requireLogin();

if (currentUserRole() !== 'yonetici') {
      die("Erişim yetkiniz yok.");
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $ad     = trim($_POST['ad'] ?? '');
      $kategori = $_POST['kategori'] ?? '';
      $kosul  = trim($_POST['kosul'] ?? '');

      if ($ad !== '' && $kategori !== '') {
            $stmt = $pdo->prepare("INSERT INTO rozetler (ad, kategori, kosul_aciklama) 
                               VALUES (?, ?, ?)");
            $stmt->execute([$ad, $kategori, $kosul]);
            $success = "Rozet başarıyla tanımlandı.";
      } else {
            $error = "Rozet adı ve kategori zorunludur.";
      }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Yeni Rozet Tanımla</title>
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
                        <h1>Yeni Rozet Tanımla</h1>
                        <p>Sisteme yeni bir başarı rozeti ekleyin.</p>
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

            <div class="card">
                  <form method="post">
                        <div class="form-grid">
                              <div>
                                    <label><i class="fa-solid fa-tag"></i> Rozet Adı</label>
                                    <input type="text" name="ad" required placeholder="Örn: Kitap Kurdu">
                              </div>
                              <div>
                                    <label><i class="fa-solid fa-layer-group"></i> Kategori</label>
                                    <select name="kategori" required>
                                          <option value="">Seçiniz</option>
                                          <option value="sportif">Sportif</option>
                                          <option value="sosyal">Sosyal</option>
                                          <option value="akademik">Akademik</option>
                                          <option value="disiplin">Disiplin</option>
                                    </select>
                              </div>
                        </div>

                        <label><i class="fa-solid fa-circle-info"></i> Kazanma Koşulu / Açıklama</label>
                        <textarea name="kosul" rows="4" placeholder="Örn: Bir dönemde 10 kitap okuyan öğrencilere verilir."></textarea>

                        <button type="submit" style="margin-top: 15px;">
                              <i class="fa-solid fa-save"></i> Rozeti Kaydet
                        </button>
                  </form>
            </div>

      </main>
      <script src="script.js"></script>

</body>

</html>