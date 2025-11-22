<?php
require_once 'functions.php';
require_once 'db.php';
requireLogin();

$role = currentUserRole();
$userId = currentUserId();
$username = $_SESSION['username'];

$ogrenci = null;
$ogrenci_id = 0;

if ($role === 'ogrenci') {
      $stmt = $pdo->prepare("SELECT * FROM ogrenciler WHERE ogr_no = ?");
      $stmt->execute([$username]);
      $ogrenci = $stmt->fetch();
      if (!$ogrenci) die("Öğrenci kaydı bulunamadı.");
      $ogrenci_id = (int)$ogrenci['id'];
} else {
      $ogrenci_id = (int)($_GET['ogrenci_id'] ?? 0);
      if ($ogrenci_id <= 0) die("Geçersiz öğrenci.");
      $stmt = $pdo->prepare("SELECT * FROM ogrenciler WHERE id = ?");
      $stmt->execute([$ogrenci_id]);
      $ogrenci = $stmt->fetch();
      if (!$ogrenci) die("Öğrenci bulunamadı.");

      if ($role === 'veli' && (int)$ogrenci['veli_user_id'] !== $userId) {
            die("Yetkiniz yok.");
      }
}

// Verileri Çek
// 1. Aktiviteler
$stmt = $pdo->prepare("SELECT a.baslik, a.tarih, t.ad AS tur_adi FROM ogrenci_aktiviteleri oa INNER JOIN aktiviteler a ON a.id = oa.aktivite_id LEFT JOIN aktivite_turleri t ON t.id = a.tur_id WHERE oa.ogrenci_id = ? AND oa.katilim_durumu = 'katildi' ORDER BY a.tarih ASC");
$stmt->execute([$ogrenci_id]);
$aktiviteler = $stmt->fetchAll();

// 2. Disiplin
$stmt = $pdo->prepare("SELECT tarih, seviye, aciklama FROM disiplin_kayitlari WHERE ogrenci_id = ? ORDER BY tarih ASC");
$stmt->execute([$ogrenci_id]);
$disiplinler = $stmt->fetchAll();

// 3. PDR
$stmt = $pdo->prepare("SELECT tarih, konu, aciklama FROM pdr_kayitlari WHERE ogrenci_id = ? ORDER BY tarih ASC");
$stmt->execute([$ogrenci_id]);
$pdrler = $stmt->fetchAll();

// 4. Rozetler
$stmt = $pdo->prepare("SELECT r.ad AS rozet_adi, r.kategori, orz.verilis_tarihi, orz.aciklama FROM ogrenci_rozetleri orz INNER JOIN rozetler r ON r.id = orz.rozet_id WHERE orz.ogrenci_id = ? ORDER BY orz.verilis_tarihi ASC");
$stmt->execute([$ogrenci_id]);
$rozetler = $stmt->fetchAll();

$aktivite_sayi = count($aktiviteler);
$disiplin_sayi = count($disiplinler);
$pdr_sayi = count($pdrler);
$rozet_sayi = count($rozetler);
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Portfolyo | <?= htmlspecialchars($ogrenci['ad']) ?></title>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="style.css">
</head>

<body>

      <header class="topbar">
            <div class="logo"><i class="fa-solid fa-graduation-cap"></i> Portfolyo</div>
            <div class="user-info">
                  <?php if ($role !== 'ogrenci' && $role !== 'veli'): ?>
                        <a href="index.php" style="margin-right:15px; color:#e0e7ff;"><i class="fa-solid fa-house"></i> Panel</a>
                  <?php endif; ?>
                  <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars(currentUserName()); ?></span>
                  <a href="logout.php" style="margin-left:10px;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Çıkış</a>
            </div>
      </header>

      <main class="main-content">

            <div class="card card-main" style="border-left: 5px solid var(--primary-color);">
                  <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                              <h1 style="font-size: 24px; margin-bottom: 5px; color: var(--text-main);">
                                    <?= htmlspecialchars($ogrenci['ad'] . ' ' . $ogrenci['soyad']) ?>
                              </h1>
                              <div style="color: var(--text-muted); font-size: 14px;">
                                    <i class="fa-solid fa-id-badge"></i> No: <?= htmlspecialchars($ogrenci['ogr_no']); ?> &nbsp;|&nbsp;
                                    <i class="fa-solid fa-users-rectangle"></i> Sınıf: <?= htmlspecialchars($ogrenci['sinif'] . $ogrenci['sube']); ?>
                              </div>
                        </div>
                        <?php if ($role === 'yonetici'): ?>
                              <a href="ogrenci_duzenle.php?id=<?= $ogrenci_id ?>" class="btn" style="margin-top:0; padding: 8px 16px; font-size: 12px;">
                                    <i class="fa-solid fa-pen"></i> Düzenle
                              </a>
                        <?php endif; ?>
                  </div>

                  <div class="chip-row" style="margin-top: 20px;">
                        <span class="chip chip-blue"><i class="fa-solid fa-person-running"></i> Aktivite: <?= $aktivite_sayi; ?></span>
                        <span class="chip chip-green"><i class="fa-solid fa-medal"></i> Rozet: <?= $rozet_sayi; ?></span>
                        <span class="chip chip-orange"><i class="fa-solid fa-user-doctor"></i> PDR: <?= $pdr_sayi; ?></span>
                        <?php if ($disiplin_sayi > 0): ?>
                              <span class="chip chip-red"><i class="fa-solid fa-triangle-exclamation"></i> Disiplin: <?= $disiplin_sayi; ?></span>
                        <?php endif; ?>
                  </div>
            </div>

            <div class="portfolio-grid">
                  <div class="portfolio-col">
                        <div class="card">
                              <div class="card-header">
                                    <div class="card-title-sm"><i class="fa-solid fa-calendar-check" style="color: var(--primary-color);"></i> Aktiviteler</div>
                              </div>
                              <?php if (empty($aktiviteler)): ?>
                                    <p class="muted">Henüz katılım yok.</p>
                              <?php else: ?>
                                    <ul class="timeline">
                                          <?php foreach ($aktiviteler as $a): ?>
                                                <li>
                                                      <div class="timeline-date"><?= htmlspecialchars($a['tarih']); ?></div>
                                                      <div class="timeline-main">
                                                            <span class="tag tag-blue"><?= htmlspecialchars($a['tur_adi']); ?></span>
                                                            <div class="timeline-title"><?= htmlspecialchars($a['baslik']); ?></div>
                                                      </div>
                                                </li>
                                          <?php endforeach; ?>
                                    </ul>
                              <?php endif; ?>
                        </div>

                        <div class="card">
                              <div class="card-header">
                                    <div class="card-title-sm"><i class="fa-solid fa-award" style="color: #166534;"></i> Rozetler</div>
                              </div>
                              <?php if (empty($rozetler)): ?>
                                    <p class="muted">Henüz rozet kazanılmamış.</p>
                              <?php else: ?>
                                    <ul class="tag-list">
                                          <?php foreach ($rozetler as $r): ?>
                                                <li>
                                                      <div class="tag-row">
                                                            <span class="tag tag-green"><?= htmlspecialchars($r['kategori']); ?></span>
                                                            <strong><?= htmlspecialchars($r['rozet_adi']); ?></strong>
                                                      </div>
                                                      <div class="tag-meta">
                                                            <?= htmlspecialchars($r['verilis_tarihi']); ?>
                                                            <?= $r['aciklama'] ? ' - ' . htmlspecialchars($r['aciklama']) : '' ?>
                                                      </div>
                                                </li>
                                          <?php endforeach; ?>
                                    </ul>
                              <?php endif; ?>
                        </div>
                  </div>

                  <div class="portfolio-col">
                        <div class="card">
                              <div class="card-header">
                                    <div class="card-title-sm"><i class="fa-solid fa-gavel" style="color: #991b1b;"></i> Disiplin Durumu</div>
                              </div>
                              <?php if (empty($disiplinler)): ?>
                                    <p class="muted" style="color: #166534;"><i class="fa-solid fa-check"></i> Disiplin kaydı bulunmamaktadır.</p>
                              <?php else: ?>
                                    <ul class="timeline">
                                          <?php foreach ($disiplinler as $d): ?>
                                                <li>
                                                      <div class="timeline-date"><?= htmlspecialchars($d['tarih']); ?></div>
                                                      <div class="timeline-main">
                                                            <span class="tag tag-red"><?= htmlspecialchars($d['seviye']); ?></span>
                                                            <div class="timeline-title"><?= htmlspecialchars($d['aciklama']); ?></div>
                                                      </div>
                                                </li>
                                          <?php endforeach; ?>
                                    </ul>
                              <?php endif; ?>
                        </div>

                        <div class="card">
                              <div class="card-header">
                                    <div class="card-title-sm"><i class="fa-solid fa-comments" style="color: #9a3412;"></i> PDR Görüşmeleri</div>
                              </div>
                              <?php if (empty($pdrler)): ?>
                                    <p class="muted">Görüşme kaydı yok.</p>
                              <?php else: ?>
                                    <ul class="timeline">
                                          <?php foreach ($pdrler as $p): ?>
                                                <li>
                                                      <div class="timeline-date"><?= htmlspecialchars($p['tarih']); ?></div>
                                                      <div class="timeline-main">
                                                            <div class="timeline-title"><strong><?= htmlspecialchars($p['konu']); ?></strong></div>
                                                            <div class="timeline-text"><?= nl2br(htmlspecialchars($p['aciklama'])); ?></div>
                                                      </div>
                                                </li>
                                          <?php endforeach; ?>
                                    </ul>
                              <?php endif; ?>
                        </div>
                  </div>
            </div>

      </main>
      <script src="script.js"></script>
</body>

</html>