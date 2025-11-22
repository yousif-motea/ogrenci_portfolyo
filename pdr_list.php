<?php
require_once 'functions.php';
requireLogin();

// Yönetici veya rehber görebilsin
$role = currentUserRole();
if (!in_array($role, ['yonetici', 'rehber'])) {
      die("Bu sayfaya erişim yetkiniz yok.");
}

// PDR kayıtlarını öğrenci ve rehber bilgisiyle çek
$sql = "SELECT p.*, 
               o.ogr_no, o.ad AS ogr_ad, o.soyad AS ogr_soyad, o.sinif, o.sube,
               u.ad AS rehber_ad, u.soyad AS rehber_soyad
        FROM pdr_kayitlari p
        INNER JOIN ogrenciler o ON o.id = p.ogrenci_id
        INNER JOIN users u ON u.id = p.rehber_user_id
        ORDER BY p.tarih DESC, p.id DESC";

$stmt = $pdo->query($sql);
$kayitlar = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>PDR Kayıtları</title>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="style.css">

      <style>
            .action-btn {
                  padding: 6px 10px;
                  border-radius: 6px;
                  color: #fff;
                  font-size: 12px;
                  transition: opacity 0.2s;
                  display: inline-flex;
                  align-items: center;
                  gap: 5px;
            }

            .btn-delete {
                  background-color: #ef4444;
            }

            .action-btn:hover {
                  opacity: 0.9;
                  color: #fff;
                  text-decoration: none;
            }

            .page-header-flex {
                  display: flex;
                  justify-content: space-between;
                  align-items: center;
                  margin-bottom: 20px;
            }

            /* Uzun metinleri sınırla */
            .text-truncate {
                  max-width: 250px;
                  white-space: nowrap;
                  overflow: hidden;
                  text-overflow: ellipsis;
                  color: #64748b;
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
                  <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Çıkış</a>
            </div>
      </header>

      <main class="main-content">

            <div class="page-header-flex">
                  <div>
                        <h1>PDR Kayıtları</h1>
                        <p>Öğrenci görüşmeleri ve rehberlik notları.</p>
                  </div>
                  <a href="pdr_ekle.php" class="btn">
                        <i class="fa-solid fa-plus"></i> Yeni Görüşme Ekle
                  </a>
            </div>

            <div style="margin-bottom: 15px; position: relative; max-width: 400px;">
                  <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                  <input type="text" id="tableSearch" placeholder="Öğrenci, konu veya rehber ara..." style="padding-left: 36px; width: 100%;">
            </div>

            <div class="card" style="padding: 0; overflow: hidden;">
                  <table cellpadding="0" cellspacing="0">
                        <thead>
                              <tr>
                                    <th>Tarih</th>
                                    <th>Öğrenci</th>
                                    <th>Konu</th>
                                    <th>Görüşme Notu</th>
                                    <th>Rehber</th>
                                    <th style="text-align: right;">İşlem</th>
                              </tr>
                        </thead>
                        <tbody>
                              <?php foreach ($kayitlar as $k): ?>
                                    <tr class="hover-row">
                                          <td style="color: #64748b; font-size: 13px;">
                                                <i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($k['tarih']) ?>
                                          </td>
                                          <td>
                                                <strong><?= htmlspecialchars($k['ogr_ad'] . ' ' . $k['ogr_soyad']) ?></strong>
                                                <br>
                                                <span style="font-size: 11px; color: #94a3b8;">
                                                      <?= htmlspecialchars($k['sinif'] . $k['sube']) ?> (<?= htmlspecialchars($k['ogr_no']) ?>)
                                                </span>
                                          </td>
                                          <td>
                                                <span class="badge badge-orange"><?= htmlspecialchars($k['konu']) ?></span>
                                          </td>
                                          <td title="<?= htmlspecialchars($k['aciklama']) ?>">
                                                <div class="text-truncate">
                                                      <?= htmlspecialchars($k['aciklama']) ?>
                                                </div>
                                          </td>
                                          <td style="font-size: 13px;">
                                                <i class="fa-solid fa-user-tie" style="color: #94a3b8;"></i>
                                                <?= htmlspecialchars($k['rehber_ad'] . ' ' . $k['rehber_soyad']) ?>
                                          </td>
                                          <td style="text-align: right;">
                                                <a href="pdr_sil.php?id=<?= $k['id'] ?>"
                                                      class="action-btn btn-delete"
                                                      onclick="return confirm('Bu PDR kaydını silmek istediğinize emin misiniz?');"
                                                      title="Kaydı Sil">
                                                      <i class="fa-solid fa-trash"></i>
                                                </a>
                                          </td>
                                    </tr>
                              <?php endforeach; ?>
                        </tbody>
                  </table>

                  <?php if (empty($kayitlar)): ?>
                        <div style="padding: 20px; text-align: center; color: #666;">
                              Kayıtlı PDR görüşmesi bulunmuyor.
                        </div>
                  <?php endif; ?>
            </div>

      </main>
      <script src="script.js"></script>

</body>

</html>