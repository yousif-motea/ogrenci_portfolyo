<?php
require_once 'functions.php';
requireLogin();

// Şimdilik sadece yönetici görebilsin (İstersen öğretmenlere de açabilirsin)
if (currentUserRole() !== 'yonetici') {
      die("Bu sayfaya erişim yetkiniz yok.");
}

// Tüm rozet tanımları
$stmt = $pdo->query("SELECT * FROM rozetler ORDER BY kategori, ad");
$rozetler = $stmt->fetchAll();

// Son verilen rozetler (Log)
$sql = "SELECT orz.*, 
               o.ogr_no, o.ad AS ogr_ad, o.soyad AS ogr_soyad, o.sinif, o.sube,
               r.ad AS rozet_adi, r.kategori
        FROM ogrenci_rozetleri orz
        INNER JOIN ogrenciler o ON o.id = orz.ogrenci_id
        INNER JOIN rozetler r ON r.id = orz.rozet_id
        ORDER BY orz.verilis_tarihi DESC, orz.id DESC
        LIMIT 20";
$stmt2 = $pdo->query($sql);
$ogrenci_rozetleri = $stmt2->fetchAll();

// Kategoriye göre renk belirleme
function getCategoryColor($cat)
{
      return match (mb_strtolower($cat)) {
            'akademik' => 'badge-green',
            'sportif' => 'badge-blue',
            'disiplin' => 'badge-red',
            'sosyal' => 'badge-orange',
            default => 'badge-gray'
      };
}

function getCategoryIcon($cat)
{
      return match (mb_strtolower($cat)) {
            'akademik' => 'fa-book-open',
            'sportif' => 'fa-trophy',
            'disiplin' => 'fa-scale-balanced',
            'sosyal' => 'fa-handshake',
            default => 'fa-star'
      };
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Rozet Sistemi</title>
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

            .btn-award {
                  background-color: #8b5cf6;
            }

            /* Mor renk */
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
                  <a href="index.php" style="margin-right: 15px; color: #e0e7ff;"><i class="fa-solid fa-house"></i> Panel</a>
                  <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars(currentUserName()); ?></span>
                  <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Çıkış</a>
            </div>
      </header>

      <main class="main-content">

            <div class="page-header-flex">
                  <div>
                        <h1>Rozet Sistemi</h1>
                        <p>Öğrenci motivasyon ve ödüllendirme yönetimi.</p>
                  </div>
                  <div>
                        <a href="rozet_ver.php" class="btn" style="background: #8b5cf6; margin-right: 10px;">
                              <i class="fa-solid fa-medal"></i> Öğrenciye Rozet Ver
                        </a>
                        <a href="rozet_ekle.php" class="btn">
                              <i class="fa-solid fa-plus"></i> Yeni Rozet Tanımla
                        </a>
                  </div>
            </div>

            <div class="card" style="margin-bottom: 30px;">
                  <div class="card-header">
                        <div class="card-title-sm"><i class="fa-solid fa-list-ul"></i> Tanımlı Rozet Listesi</div>
                  </div>
                  <div style="overflow-x: auto;">
                        <table cellpadding="0" cellspacing="0">
                              <thead>
                                    <tr>
                                          <th>Rozet Adı</th>
                                          <th>Kategori</th>
                                          <th>Koşul / Açıklama</th>
                                          <th style="text-align: right;">İşlem</th>
                                    </tr>
                              </thead>
                              <tbody>
                                    <?php foreach ($rozetler as $r): ?>
                                          <tr class="hover-row">
                                                <td><strong><?= htmlspecialchars($r['ad']) ?></strong></td>
                                                <td>
                                                      <span class="badge <?= getCategoryColor($r['kategori']) ?>">
                                                            <i class="fa-solid <?= getCategoryIcon($r['kategori']) ?>"></i>
                                                            <?= htmlspecialchars($r['kategori']) ?>
                                                      </span>
                                                </td>
                                                <td style="color: #64748b;"><?= nl2br(htmlspecialchars($r['kosul_aciklama'])) ?></td>
                                                <td style="text-align: right;">
                                                      <a href="rozet_sil.php?id=<?= $r['id'] ?>"
                                                            class="action-btn btn-delete"
                                                            onclick="return confirm('Bu rozet tanımını silmek istediğinize emin misiniz?');">
                                                            <i class="fa-solid fa-trash"></i>
                                                      </a>
                                                </td>
                                          </tr>
                                    <?php endforeach; ?>
                              </tbody>
                        </table>
                        <?php if (empty($rozetler)): ?>
                              <p style="padding:20px; text-align:center; color:#666;">Henüz tanımlı rozet yok.</p>
                        <?php endif; ?>
                  </div>
            </div>

            <div class="card">
                  <div class="card-header">
                        <div class="card-title-sm"><i class="fa-solid fa-clock-rotate-left"></i> Son Verilen Rozetler</div>
                  </div>

                  <div style="margin-bottom: 10px; position: relative;">
                        <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" id="tableSearch" placeholder="Öğrenci veya rozet ara..." style="padding-left: 36px;">
                  </div>

                  <div style="overflow-x: auto;">
                        <table cellpadding="0" cellspacing="0">
                              <thead>
                                    <tr>
                                          <th>Tarih</th>
                                          <th>Öğrenci</th>
                                          <th>Kazanılan Rozet</th>
                                          <th>Açıklama</th>
                                    </tr>
                              </thead>
                              <tbody>
                                    <?php foreach ($ogrenci_rozetleri as $o): ?>
                                          <tr class="hover-row">
                                                <td style="color: #64748b; font-size: 13px;">
                                                      <i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($o['verilis_tarihi']) ?>
                                                </td>
                                                <td>
                                                      <strong><?= htmlspecialchars($o['ogr_ad'] . ' ' . $o['ogr_soyad']) ?></strong>
                                                      <br>
                                                      <span style="font-size: 11px; color: #94a3b8;">
                                                            <?= htmlspecialchars($o['sinif'] . $o['sube']) ?> (<?= htmlspecialchars($o['ogr_no']) ?>)
                                                      </span>
                                                </td>
                                                <td>
                                                      <div style="display: flex; align-items: center; gap: 6px;">
                                                            <span class="badge <?= getCategoryColor($o['kategori']) ?>">
                                                                  <?= htmlspecialchars($o['kategori']) ?>
                                                            </span>
                                                            <span><?= htmlspecialchars($o['rozet_adi']) ?></span>
                                                      </div>
                                                </td>
                                                <td style="color: #64748b; font-size: 13px;">
                                                      <?= htmlspecialchars($o['aciklama']) ?>
                                                </td>
                                          </tr>
                                    <?php endforeach; ?>
                              </tbody>
                        </table>
                        <?php if (empty($ogrenci_rozetleri)): ?>
                              <p style="padding:20px; text-align:center; color:#666;">Henüz hiç rozet verilmemiş.</p>
                        <?php endif; ?>
                  </div>
            </div>

      </main>
      <script src="script.js"></script>

</body>

</html>