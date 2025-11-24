<?php
require_once 'functions.php';
requireLogin();

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

// İstatistikler
$stats = [
      'toplam_rozet' => count($rozetler),
      'verilen_rozet' => $pdo->query("SELECT COUNT(*) FROM ogrenci_rozetleri")->fetchColumn(),
      'bu_ay_verilen' => $pdo->query("SELECT COUNT(*) FROM ogrenci_rozetleri WHERE MONTH(verilis_tarihi) = MONTH(CURRENT_DATE()) AND YEAR(verilis_tarihi) = YEAR(CURRENT_DATE())")->fetchColumn(),
      'en_populer' => $pdo->query("SELECT r.ad, COUNT(*) as sayi FROM ogrenci_rozetleri orz INNER JOIN rozetler r ON r.id = orz.rozet_id GROUP BY orz.rozet_id ORDER BY sayi DESC LIMIT 1")->fetch()
];

// Kategoriye göre renk ve ikon
function getCategoryData($cat)
{
      $data = [
            'akademik' => ['color' => 'badge-green', 'icon' => 'fa-graduation-cap', 'emoji' => '🎓', 'bg' => '#dcfce7'],
            'sportif' => ['color' => 'badge-blue', 'icon' => 'fa-medal', 'emoji' => '🏆', 'bg' => '#dbeafe'],
            'disiplin' => ['color' => 'badge-red', 'icon' => 'fa-star', 'emoji' => '⭐', 'bg' => '#fee2e2'],
            'sosyal' => ['color' => 'badge-orange', 'icon' => 'fa-users', 'emoji' => '🤝', 'bg' => '#ffedd5'],
            'sanat' => ['color' => 'badge-purple', 'icon' => 'fa-palette', 'emoji' => '🎨', 'bg' => '#f3e8ff'],
            'teknoloji' => ['color' => 'badge-cyan', 'icon' => 'fa-laptop-code', 'emoji' => '💻', 'bg' => '#cffafe']
      ];
      return $data[mb_strtolower($cat)] ?? ['color' => 'badge-gray', 'icon' => 'fa-certificate', 'emoji' => '🏅', 'bg' => '#f1f5f9'];
}

// Rozet seviyeleri
function getRozetLevel($count)
{
      if ($count >= 20) return ['level' => 'Efsane', 'icon' => '👑', 'color' => '#fbbf24'];
      if ($count >= 10) return ['level' => 'Uzman', 'icon' => '💎', 'color' => '#8b5cf6'];
      if ($count >= 5) return ['level' => 'İleri', 'icon' => '🌟', 'color' => '#3b82f6'];
      return ['level' => 'Başlangıç', 'icon' => '✨', 'color' => '#10b981'];
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>🏆 Rozet Sistemi</title>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="style.css">
      <style>
            .badge-purple {
                  background: #f3e8ff;
                  color: #7c3aed;
                  border: 1px solid #a78bfa;
            }

            .badge-cyan {
                  background: #cffafe;
                  color: #0891b2;
                  border: 1px solid #22d3ee;
            }

            .badge-gray {
                  background: #f1f5f9;
                  color: #475569;
                  border: 1px solid #cbd5e1;
            }

            .stats-grid {
                  display: grid;
                  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                  gap: 20px;
                  margin-bottom: 30px;
            }

            .stat-card {
                  background: linear-gradient(135deg, var(--card-bg) 0%, var(--bg-color) 100%);
                  padding: 24px;
                  border-radius: 16px;
                  border: 2px solid var(--border-color);
                  position: relative;
                  overflow: hidden;
                  transition: all 0.3s ease;
            }

            .stat-card::before {
                  content: '';
                  position: absolute;
                  top: 0;
                  left: 0;
                  width: 100%;
                  height: 4px;
                  background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
                  transform: scaleX(0);
                  transition: transform 0.3s ease;
            }

            .stat-card:hover {
                  transform: translateY(-5px);
                  box-shadow: var(--shadow-xl);
                  border-color: var(--primary-light);
            }

            .stat-card:hover::before {
                  transform: scaleX(1);
            }

            .stat-emoji {
                  font-size: 48px;
                  margin-bottom: 12px;
                  display: block;
                  animation: bounce 2s infinite;
            }

            @keyframes bounce {

                  0%,
                  100% {
                        transform: translateY(0);
                  }

                  50% {
                        transform: translateY(-10px);
                  }
            }

            .stat-value {
                  font-size: 36px;
                  font-weight: 900;
                  color: var(--primary-color);
                  margin: 8px 0;
            }

            .stat-label {
                  font-size: 13px;
                  color: var(--text-muted);
                  text-transform: uppercase;
                  font-weight: 700;
                  letter-spacing: 0.5px;
            }

            .rozet-card {
                  background: var(--card-bg);
                  border-radius: 16px;
                  padding: 24px;
                  border: 2px solid var(--border-color);
                  transition: all 0.3s ease;
                  position: relative;
                  overflow: hidden;
            }

            .rozet-card::after {
                  content: '';
                  position: absolute;
                  top: -50%;
                  right: -50%;
                  width: 200%;
                  height: 200%;
                  background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
                  opacity: 0;
                  transition: opacity 0.3s ease;
            }

            .rozet-card:hover {
                  transform: translateY(-8px) scale(1.02);
                  box-shadow: var(--shadow-2xl);
                  border-color: var(--primary-color);
            }

            .rozet-card:hover::after {
                  opacity: 1;
            }

            .rozet-icon {
                  font-size: 72px;
                  margin-bottom: 16px;
                  display: block;
                  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
                  transition: all 0.3s ease;
            }

            .rozet-card:hover .rozet-icon {
                  transform: scale(1.2) rotate(10deg);
                  filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.2));
            }

            .rozet-name {
                  font-size: 20px;
                  font-weight: 800;
                  color: var(--text-main);
                  margin-bottom: 8px;
            }

            .rozet-description {
                  font-size: 14px;
                  color: var(--text-muted);
                  line-height: 1.6;
                  margin-bottom: 16px;
            }

            .rozet-footer {
                  display: flex;
                  justify-content: space-between;
                  align-items: center;
                  margin-top: 16px;
                  padding-top: 16px;
                  border-top: 2px solid var(--border-color);
            }

            .rozet-grid {
                  display: grid;
                  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                  gap: 24px;
                  margin-top: 24px;
            }

            .action-buttons {
                  display: flex;
                  gap: 8px;
            }

            .icon-btn {
                  width: 36px;
                  height: 36px;
                  border-radius: 8px;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  border: none;
                  cursor: pointer;
                  transition: all 0.2s ease;
                  font-size: 14px;
            }

            .icon-btn.delete {
                  background: #fee2e2;
                  color: #dc2626;
            }

            .icon-btn.delete:hover {
                  background: #dc2626;
                  color: white;
                  transform: scale(1.1);
            }

            .icon-btn.edit {
                  background: #dbeafe;
                  color: #2563eb;
            }

            .icon-btn.edit:hover {
                  background: #2563eb;
                  color: white;
                  transform: scale(1.1);
            }

            .timeline-item {
                  display: flex;
                  gap: 16px;
                  padding: 20px;
                  background: var(--card-bg);
                  border-radius: 12px;
                  border: 2px solid var(--border-color);
                  margin-bottom: 16px;
                  transition: all 0.3s ease;
                  position: relative;
                  overflow: hidden;
            }

            .timeline-item::before {
                  content: '';
                  position: absolute;
                  left: 0;
                  top: 0;
                  bottom: 0;
                  width: 4px;
                  background: var(--primary-color);
                  transform: scaleY(0);
                  transition: transform 0.3s ease;
            }

            .timeline-item:hover {
                  transform: translateX(8px);
                  border-color: var(--primary-light);
                  box-shadow: var(--shadow-lg);
            }

            .timeline-item:hover::before {
                  transform: scaleY(1);
            }

            .timeline-emoji {
                  font-size: 48px;
                  flex-shrink: 0;
            }

            .timeline-content {
                  flex: 1;
            }

            .timeline-student {
                  font-weight: 700;
                  color: var(--text-main);
                  font-size: 16px;
                  margin-bottom: 4px;
            }

            .timeline-class {
                  font-size: 12px;
                  color: var(--text-muted);
                  margin-bottom: 8px;
            }

            .timeline-badge-name {
                  display: inline-flex;
                  align-items: center;
                  gap: 8px;
                  background: var(--info-bg);
                  padding: 6px 12px;
                  border-radius: 20px;
                  font-size: 13px;
                  font-weight: 700;
                  color: var(--info-text);
                  margin-bottom: 8px;
            }

            .timeline-reason {
                  font-size: 14px;
                  color: var(--text-muted);
                  font-style: italic;
            }

            .timeline-date {
                  font-size: 12px;
                  color: var(--text-muted);
                  display: flex;
                  align-items: center;
                  gap: 4px;
            }

            .page-header-modern {
                  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
                  padding: 40px;
                  border-radius: 20px;
                  color: white;
                  margin-bottom: 30px;
                  position: relative;
                  overflow: hidden;
            }

            .page-header-modern::before {
                  content: '🏆';
                  position: absolute;
                  font-size: 200px;
                  opacity: 0.1;
                  right: -50px;
                  top: -50px;
                  animation: float 6s ease-in-out infinite;
            }

            @keyframes float {

                  0%,
                  100% {
                        transform: translateY(0) rotate(0deg);
                  }

                  50% {
                        transform: translateY(-20px) rotate(10deg);
                  }
            }

            .page-header-modern h1 {
                  font-size: 42px;
                  font-weight: 900;
                  margin-bottom: 8px;
                  text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            }

            .page-header-modern p {
                  font-size: 18px;
                  opacity: 0.95;
            }

            .header-actions {
                  display: flex;
                  gap: 12px;
                  margin-top: 20px;
            }

            .header-btn {
                  background: rgba(255, 255, 255, 0.2);
                  backdrop-filter: blur(10px);
                  color: white;
                  padding: 12px 24px;
                  border-radius: 12px;
                  border: 2px solid rgba(255, 255, 255, 0.3);
                  font-weight: 700;
                  transition: all 0.3s ease;
                  display: inline-flex;
                  align-items: center;
                  gap: 8px;
            }

            .header-btn:hover {
                  background: white;
                  color: var(--primary-color);
                  transform: translateY(-2px);
                  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            }

            .empty-state {
                  text-align: center;
                  padding: 60px 20px;
                  color: var(--text-muted);
            }

            .empty-state-emoji {
                  font-size: 80px;
                  margin-bottom: 16px;
                  opacity: 0.5;
            }

            .tab-container {
                  display: flex;
                  gap: 8px;
                  margin-bottom: 24px;
                  background: var(--card-bg);
                  padding: 8px;
                  border-radius: 12px;
                  border: 2px solid var(--border-color);
            }

            .tab-btn {
                  flex: 1;
                  padding: 12px 20px;
                  border: none;
                  background: transparent;
                  border-radius: 8px;
                  font-weight: 700;
                  cursor: pointer;
                  transition: all 0.3s ease;
                  color: var(--text-muted);
            }

            .tab-btn.active {
                  background: var(--primary-color);
                  color: white;
                  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            }

            .tab-content {
                  display: none;
            }

            .tab-content.active {
                  display: block;
                  animation: fadeIn 0.3s ease;
            }
      </style>
</head>

<body>

      <header class="topbar">
            <div class="logo"><i class="fa-solid fa-graduation-cap"></i> Portfolyo Sistemi</div>
            <div class="user-info">
                  <a href="index.php"><i class="fa-solid fa-house"></i> Panel</a>
                  <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars(currentUserName()); ?></span>
                  <a href="logout.php" class="btn-logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Çıkış</a>
            </div>
      </header>

      <main class="main-content">

            <div class="page-header-modern">
                  <h1>🏆 Rozet Yönetim Sistemi</h1>
                  <p>Öğrencilerin başarılarını ödüllendirin ve motive edin</p>
                  <div class="header-actions">
                        <a href="rozet_ver.php" class="header-btn">
                              <i class="fa-solid fa-gift"></i> Rozet Ver
                        </a>
                        <a href="rozet_ekle.php" class="header-btn">
                              <i class="fa-solid fa-plus"></i> Yeni Rozet
                        </a>
                  </div>
            </div>

            <!-- İstatistikler -->
            <div class="stats-grid">
                  <div class="stat-card">
                        <span class="stat-emoji">🎯</span>
                        <div class="stat-value"><?= $stats['toplam_rozet'] ?></div>
                        <div class="stat-label">Toplam Rozet Türü</div>
                  </div>
                  <div class="stat-card">
                        <span class="stat-emoji">🎁</span>
                        <div class="stat-value"><?= $stats['verilen_rozet'] ?></div>
                        <div class="stat-label">Verilen Rozet</div>
                  </div>
                  <div class="stat-card">
                        <span class="stat-emoji">📅</span>
                        <div class="stat-value"><?= $stats['bu_ay_verilen'] ?></div>
                        <div class="stat-label">Bu Ay Verilen</div>
                  </div>
                  <div class="stat-card">
                        <span class="stat-emoji">⭐</span>
                        <div class="stat-value"><?= $stats['en_populer']['ad'] ?? 'N/A' ?></div>
                        <div class="stat-label">En Popüler Rozet</div>
                  </div>
            </div>

            <!-- Tab Menüsü -->
            <div class="tab-container">
                  <button class="tab-btn active" onclick="switchTab('rozetler')">
                        <i class="fa-solid fa-list"></i> Rozet Listesi
                  </button>
                  <button class="tab-btn" onclick="switchTab('gecmis')">
                        <i class="fa-solid fa-clock-rotate-left"></i> Veriliş Geçmişi
                  </button>
            </div>

            <!-- Rozet Listesi Tab -->
            <div class="tab-content active" id="tab-rozetler">
                  <?php if (empty($rozetler)): ?>
                        <div class="empty-state">
                              <div class="empty-state-emoji">🎭</div>
                              <h3>Henüz rozet tanımlanmamış</h3>
                              <p>Başlamak için yeni bir rozet oluşturun!</p>
                        </div>
                  <?php else: ?>
                        <div class="rozet-grid">
                              <?php foreach ($rozetler as $r):
                                    $catData = getCategoryData($r['kategori']);
                              ?>
                                    <div class="rozet-card">
                                          <span class="rozet-icon"><?= $catData['emoji'] ?></span>
                                          <h3 class="rozet-name"><?= htmlspecialchars($r['ad']) ?></h3>
                                          <p class="rozet-description"><?= nl2br(htmlspecialchars($r['kosul_aciklama'])) ?></p>

                                          <div class="rozet-footer">
                                                <span class="badge <?= $catData['color'] ?>">
                                                      <i class="fa-solid <?= $catData['icon'] ?>"></i>
                                                      <?= htmlspecialchars(ucfirst($r['kategori'])) ?>
                                                </span>
                                                <div class="action-buttons">
                                                      <a href="rozet_sil.php?id=<?= $r['id'] ?>"
                                                            class="icon-btn delete"
                                                            onclick="return confirm('🗑️ Bu rozeti silmek istediğinize emin misiniz?');"
                                                            title="Sil">
                                                            <i class="fa-solid fa-trash"></i>
                                                      </a>
                                                </div>
                                          </div>
                                    </div>
                              <?php endforeach; ?>
                        </div>
                  <?php endif; ?>
            </div>

            <!-- Geçmiş Tab -->
            <div class="tab-content" id="tab-gecmis">
                  <div style="margin-bottom: 20px;">
                        <input type="text" id="timelineSearch" placeholder="🔍 Öğrenci veya rozet ara..."
                              style="width: 100%; max-width: 500px; padding: 12px 16px 12px 40px; border-radius: 12px;">
                  </div>

                  <?php if (empty($ogrenci_rozetleri)): ?>
                        <div class="empty-state">
                              <div class="empty-state-emoji">📦</div>
                              <h3>Henüz rozet verilmemiş</h3>
                              <p>İlk rozeti vererek başlayın!</p>
                        </div>
                  <?php else: ?>
                        <?php foreach ($ogrenci_rozetleri as $o):
                              $catData = getCategoryData($o['kategori']);
                        ?>
                              <div class="timeline-item">
                                    <div class="timeline-emoji"><?= $catData['emoji'] ?></div>
                                    <div class="timeline-content">
                                          <div class="timeline-student">
                                                <?= htmlspecialchars($o['ogr_ad'] . ' ' . $o['ogr_soyad']) ?>
                                          </div>
                                          <div class="timeline-class">
                                                <?= htmlspecialchars($o['sinif'] . $o['sube']) ?> - <?= htmlspecialchars($o['ogr_no']) ?>
                                          </div>
                                          <span class="timeline-badge-name">
                                                <i class="fa-solid fa-medal"></i>
                                                <?= htmlspecialchars($o['rozet_adi']) ?>
                                          </span>
                                          <?php if (!empty($o['aciklama'])): ?>
                                                <div class="timeline-reason">
                                                      "<?= htmlspecialchars($o['aciklama']) ?>"
                                                </div>
                                          <?php endif; ?>
                                          <div class="timeline-date">
                                                <i class="fa-regular fa-calendar"></i>
                                                <?= date('d.m.Y', strtotime($o['verilis_tarihi'])) ?>
                                          </div>
                                    </div>
                              </div>
                        <?php endforeach; ?>
                  <?php endif; ?>
            </div>

      </main>

      <script src="script.js"></script>
      <script>
            function switchTab(tabName) {
                  // Tab butonlarını güncelle
                  document.querySelectorAll('.tab-btn').forEach(btn => {
                        btn.classList.remove('active');
                  });
                  event.target.closest('.tab-btn').classList.add('active');

                  // Tab içeriklerini güncelle
                  document.querySelectorAll('.tab-content').forEach(content => {
                        content.classList.remove('active');
                  });
                  document.getElementById('tab-' + tabName).classList.add('active');
            }

            // Timeline arama
            const timelineSearch = document.getElementById('timelineSearch');
            if (timelineSearch) {
                  timelineSearch.addEventListener('keyup', function() {
                        const filter = this.value.toLowerCase();
                        document.querySelectorAll('.timeline-item').forEach(item => {
                              const text = item.textContent.toLowerCase();
                              item.style.display = text.includes(filter) ? 'flex' : 'none';
                        });
                  });
            }

            // Rozet kartlarına hover animasyonu
            document.querySelectorAll('.rozet-card').forEach(card => {
                  card.addEventListener('mouseenter', function() {
                        this.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
                  });
            });
      </script>

</body>

</html>