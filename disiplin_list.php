<?php
require_once 'functions.php';
requireLogin();

// Yönetici veya rehber kullanabilsin
$role = currentUserRole();
if (!in_array($role, ['yonetici', 'rehber'])) {
      die("Bu sayfaya erişim yetkiniz yok.");
}

// Öğrenci + disiplin kayıtlarını birlikte çek
$sql = "SELECT d.*, 
               o.ogr_no, o.ad, o.soyad, o.sinif, o.sube
        FROM disiplin_kayitlari d
        INNER JOIN ogrenciler o ON o.id = d.ogrenci_id
        ORDER BY d.tarih DESC, d.id DESC";

$stmt = $pdo->query($sql);
$kayitlar = $stmt->fetchAll();

// Seviyeye göre renk belirleme fonksiyonu
function getDisiplinBadge($seviye)
{
      $s = mb_strtolower($seviye);
      if (strpos($s, 'uzak') !== false || strpos($s, 'kınama') !== false) {
            return 'badge-red'; // Ciddi cezalar
      }
      return 'badge-orange'; // Uyarılar vb.
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Disiplin Kayıtları</title>
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
                  <span>
                        <i class="fa-regular fa-user"></i> <?= htmlspecialchars(currentUserName()); ?>
                  </span>
                  <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Çıkış</a>
            </div>
      </header>

      <main class="main-content">

            <div class="page-header-flex">
                  <div>
                        <h1>Disiplin Kayıtları</h1>
                        <p>Öğrencilerin davranış değerlendirme ve disiplin geçmişi.</p>
                  </div>
                  <a href="disiplin_ekle.php" class="btn">
                        <i class="fa-solid fa-plus"></i> Yeni Kayıt Ekle
                  </a>
            </div>

            <div style="margin-bottom: 15px; position: relative; max-width: 400px;">
                  <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                  <input type="text" id="tableSearch" placeholder="Öğrenci, seviye veya açıklama ara..." style="padding-left: 36px; width: 100%;">
            </div>

            <div class="card" style="padding: 0; overflow: hidden;">
                  <table cellpadding="0" cellspacing="0">
                        <thead>
                              <tr>
                                    <th>Tarih</th>
                                    <th>Öğrenci</th>
                                    <th>Sınıf</th>
                                    <th>Seviye</th>
                                    <th>Açıklama</th>
                                    <th style="text-align: right;">İşlem</th>
                              </tr>
                        </thead>
                        <tbody>
                              <?php foreach ($kayitlar as $k): ?>
                                    <tr class="hover-row">
                                          <td style="font-size: 13px; color: #64748b;">
                                                <i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($k['tarih']) ?>
                                          </td>
                                          <td>
                                                <strong><?= htmlspecialchars($k['ad'] . ' ' . $k['soyad']) ?></strong>
                                                <br>
                                                <span style="font-size: 11px; color: #94a3b8;">No: <?= htmlspecialchars($k['ogr_no']) ?></span>
                                          </td>
                                          <td><?= htmlspecialchars($k['sinif'] . ' / ' . $k['sube']) ?></td>
                                          <td>
                                                <span class="badge <?= getDisiplinBadge($k['seviye']) ?>">
                                                      <?= htmlspecialchars($k['seviye']) ?>
                                                </span>
                                          </td>
                                          <td style="max-width: 300px;">
                                                <?= nl2br(htmlspecialchars($k['aciklama'])) ?>
                                          </td>
                                          <td style="text-align: right;">
                                                <a href="disiplin_sil.php?id=<?= $k['id'] ?>"
                                                      class="action-btn btn-delete"
                                                      onclick="return confirm('Bu disiplin kaydını silmek istediğinize emin misiniz?');"
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
                              Kayıtlı disiplin verisi bulunmamaktadır.
                        </div>
                  <?php endif; ?>
            </div>

      </main>

      <script src="script.js"></script>

</body>

</html>