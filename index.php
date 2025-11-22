<?php
require_once 'functions.php';
requireLogin();

$role = currentUserRole();

// Yönlendirmeler (Öğrenci ve Veli için)
if ($role === 'ogrenci') {
      header('Location: ogrenci_portfolyo.php');
      exit;
}
if ($role === 'veli') {
      header('Location: veli_portfolyo.php');
      exit;
}

// --- İSTATİSTİK SORGULARI ---
$stmt = $pdo->query("SELECT COUNT(*) AS sayi FROM ogrenciler");
$ogrenci_sayi = (int)$stmt->fetch()['sayi'];

$stmt = $pdo->query("SELECT COUNT(*) AS sayi FROM aktiviteler");
$aktivite_sayi = (int)$stmt->fetch()['sayi'];

$stmt = $pdo->query("SELECT COUNT(*) AS sayi FROM disiplin_kayitlari");
$disiplin_sayi = (int)$stmt->fetch()['sayi'];

$stmt = $pdo->query("SELECT COUNT(*) AS sayi FROM pdr_kayitlari");
$pdr_sayi = (int)$stmt->fetch()['sayi'];

$stmt = $pdo->query("SELECT COUNT(*) AS sayi FROM ogrenci_rozetleri");
$rozet_sayi = (int)$stmt->fetch()['sayi'];

// --- SON KAYITLAR ---
$lastDis = $pdo->query("SELECT d.tarih, d.seviye, o.ad, o.soyad, o.sinif, o.sube FROM disiplin_kayitlari d INNER JOIN ogrenciler o ON o.id = d.ogrenci_id ORDER BY d.tarih DESC, d.id DESC LIMIT 5")->fetchAll();
$lastPdr = $pdo->query("SELECT p.tarih, p.konu, o.ad, o.soyad, o.sinif, o.sube FROM pdr_kayitlari p INNER JOIN ogrenciler o ON o.id = p.ogrenci_id ORDER BY p.tarih DESC, p.id DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Yönetim Paneli | Okul Portfolyo</title>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="style.css">

      <style>
            /* Header içindeki özel stiller (Sadece bu sayfada veya genel kullanılabilir) */
            .logo-link {
                  color: white;
                  text-decoration: none;
                  display: flex;
                  align-items: center;
                  gap: 10px;
                  font-weight: 700;
                  font-size: 18px;
                  transition: opacity 0.2s;
            }

            .logo-link:hover {
                  opacity: 0.8;
            }

            /* Çıkış Butonu Stili */
            .btn-logout {
                  background: rgba(255, 255, 255, 0.1);
                  color: white;
                  padding: 8px 16px;
                  border-radius: 6px;
                  text-decoration: none;
                  font-size: 13px;
                  font-weight: 500;
                  border: 1px solid rgba(255, 255, 255, 0.2);
                  transition: all 0.2s ease;
                  display: inline-flex;
                  align-items: center;
                  gap: 6px;
                  margin-left: 15px;
            }

            .btn-logout:hover {
                  background: #ef4444;
                  /* Kırmızı hover */
                  border-color: #ef4444;
                  transform: translateY(-1px);
                  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            }
      </style>
</head>

<body>
      <header class="topbar">
            <a href="index.php" class="logo-link">
                  <i class="fa-solid fa-graduation-cap"></i> Portfolyo Sistemi
            </a>

            <div class="user-info" style="display: flex; align-items: center;">
                  <span>
                        <i class="fa-regular fa-user"></i>
                        <?= htmlspecialchars(currentUserName()); ?>
                        <span style="opacity: 0.7; font-size: 12px;">(<?= htmlspecialchars($role); ?>)</span>
                  </span>

                  <a href="logout.php" class="btn-logout">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Çıkış
                  </a>
            </div>
      </header>

      <main class="main-content">
            <div class="page-header">
                  <h1>Yönetim Paneli</h1>
                  <p>Okul genel durumu, istatistikler ve son aktiviteler.</p>
            </div>

            <div class="dashboard-grid">
                  <div class="dash-card">
                        <div class="dash-icon"><i class="fa-solid fa-users"></i></div>
                        <div class="dash-label">Toplam Öğrenci</div>
                        <div class="dash-value"><?php echo $ogrenci_sayi; ?></div>
                        <div class="dash-footer"><a href="ogrenciler.php">Listeye Git <i class="fa-solid fa-arrow-right"></i></a></div>
                  </div>

                  <div class="dash-card">
                        <div class="dash-icon"><i class="fa-solid fa-calendar-check"></i></div>
                        <div class="dash-label">Aktiviteler</div>
                        <div class="dash-value"><?php echo $aktivite_sayi; ?></div>
                        <div class="dash-footer"><a href="aktiviteler.php">Yönet <i class="fa-solid fa-arrow-right"></i></a></div>
                  </div>

                  <div class="dash-card">
                        <div class="dash-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div class="dash-label">Disiplin</div>
                        <div class="dash-value"><?php echo $disiplin_sayi; ?></div>
                        <div class="dash-footer"><a href="disiplin_list.php">İncele <i class="fa-solid fa-arrow-right"></i></a></div>
                  </div>

                  <div class="dash-card">
                        <div class="dash-icon"><i class="fa-solid fa-user-doctor"></i></div>
                        <div class="dash-label">PDR Görüşmeleri</div>
                        <div class="dash-value"><?php echo $pdr_sayi; ?></div>
                        <div class="dash-footer"><a href="pdr_list.php">İncele <i class="fa-solid fa-arrow-right"></i></a></div>
                  </div>

                  <div class="dash-card">
                        <div class="dash-icon"><i class="fa-solid fa-medal"></i></div>
                        <div class="dash-label">Verilen Rozetler</div>
                        <div class="dash-value"><?php echo $rozet_sayi; ?></div>
                        <div class="dash-footer"><a href="rozetler.php">Yönet <i class="fa-solid fa-arrow-right"></i></a></div>
                  </div>
            </div>

            <div class="dashboard-lists">
                  <div class="dash-list-card">
                        <h3><i class="fa-solid fa-circle-exclamation" style="color: #ef4444;"></i> Son Disiplin Kayıtları</h3>
                        <?php if (empty($lastDis)): ?>
                              <p class="muted">Henüz kayıt bulunamadı.</p>
                        <?php else: ?>
                              <ul>
                                    <?php foreach ($lastDis as $d): ?>
                                          <li class="hover-row">
                                                <div>
                                                      <strong><?php echo htmlspecialchars($d['ad'] . ' ' . $d['soyad']); ?></strong>
                                                      <small style="display:block; color:#64748b; margin-top:2px;">
                                                            <?php echo htmlspecialchars($d['seviye']); ?>
                                                      </small>
                                                </div>
                                                <span class="badge badge-red"><?php echo htmlspecialchars($d['tarih']); ?></span>
                                          </li>
                                    <?php endforeach; ?>
                              </ul>
                        <?php endif; ?>
                  </div>

                  <div class="dash-list-card">
                        <h3><i class="fa-solid fa-clipboard-list" style="color: #4f46e5;"></i> Son PDR Kayıtları</h3>
                        <?php if (empty($lastPdr)): ?>
                              <p class="muted">Henüz kayıt bulunamadı.</p>
                        <?php else: ?>
                              <ul>
                                    <?php foreach ($lastPdr as $p): ?>
                                          <li class="hover-row">
                                                <div>
                                                      <strong><?php echo htmlspecialchars($p['ad'] . ' ' . $p['soyad']); ?></strong>
                                                      <small style="display:block; color:#64748b; margin-top:2px;">
                                                            <?php echo htmlspecialchars($p['konu']); ?>
                                                      </small>
                                                </div>
                                                <span class="badge badge-orange"><?php echo htmlspecialchars($p['tarih']); ?></span>
                                          </li>
                                    <?php endforeach; ?>
                              </ul>
                        <?php endif; ?>
                  </div>
            </div>
      </main>

      <script src="script.js"></script>
</body>

</html>