<?php
require_once 'functions.php';
requireLogin();

$role = currentUserRole();
if (!in_array($role, ['yonetici', 'rehber', 'ogretmen'])) {
      die("Erişim yetkiniz yok.");
}

$success = "";
$error = "";

// Öğrenciler
$stmt = $pdo->query("SELECT * FROM ogrenciler ORDER BY sinif, sube, ad, soyad");
$ogrenciler = $stmt->fetchAll();

// Rozetler kategorilere göre grupla
$stmt2 = $pdo->query("SELECT * FROM rozetler ORDER BY kategori, ad");
$tumRozetler = $stmt2->fetchAll();

$rozetler = [];
foreach ($tumRozetler as $r) {
      $rozetler[$r['kategori']][] = $r;
}

if (empty($ogrenciler) || empty($tumRozetler)) {
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

            // Öğrenci ve rozet bilgilerini al
            $ogr = $pdo->query("SELECT ad, soyad FROM ogrenciler WHERE id = $ogrenci_id")->fetch();
            $roz = $pdo->query("SELECT ad FROM rozetler WHERE id = $rozet_id")->fetch();

            $success = "🎉 Tebrikler! '{$roz['ad']}' rozeti {$ogr['ad']} {$ogr['soyad']}'e başarıyla verildi!";
      } else {
            $error = "❌ Lütfen tüm zorunlu alanları doldurun.";
      }
}

// Emoji kategorileri
$categoryEmojis = [
      'akademik' => '🎓',
      'sportif' => '🏆',
      'sosyal' => '🤝',
      'disiplin' => '⭐',
      'sanat' => '🎨',
      'teknoloji' => '💻'
];
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>🎁 Öğrenciye Rozet Ver</title>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="style.css">
      <style>
            .wizard-container {
                  max-width: 1000px;
                  margin: 0 auto;
            }

            .progress-bar {
                  display: flex;
                  justify-content: space-between;
                  margin-bottom: 40px;
                  position: relative;
            }

            .progress-bar::before {
                  content: '';
                  position: absolute;
                  top: 30px;
                  left: 0;
                  right: 0;
                  height: 4px;
                  background: var(--border-color);
                  z-index: 0;
            }

            .progress-line {
                  position: absolute;
                  top: 30px;
                  left: 0;
                  height: 4px;
                  background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
                  transition: width 0.5s ease;
                  z-index: 1;
            }

            .step {
                  flex: 1;
                  text-align: center;
                  position: relative;
                  z-index: 2;
            }

            .step-circle {
                  width: 60px;
                  height: 60px;
                  border-radius: 50%;
                  background: var(--card-bg);
                  border: 4px solid var(--border-color);
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  margin: 0 auto 12px;
                  font-size: 24px;
                  transition: all 0.3s ease;
            }

            .step.active .step-circle {
                  border-color: var(--primary-color);
                  background: var(--primary-color);
                  color: white;
                  transform: scale(1.1);
                  box-shadow: 0 0 0 8px rgba(99, 102, 241, 0.1);
            }

            .step.completed .step-circle {
                  border-color: var(--success-color);
                  background: var(--success-color);
                  color: white;
            }

            .step-label {
                  font-size: 14px;
                  font-weight: 700;
                  color: var(--text-muted);
                  transition: color 0.3s ease;
            }

            .step.active .step-label {
                  color: var(--primary-color);
            }

            .wizard-content {
                  background: var(--card-bg);
                  border: 2px solid var(--border-color);
                  border-radius: 20px;
                  padding: 40px;
                  min-height: 500px;
            }

            .wizard-step {
                  display: none;
                  animation: fadeIn 0.5s ease;
            }

            .wizard-step.active {
                  display: block;
            }

            .step-title {
                  font-size: 32px;
                  font-weight: 900;
                  color: var(--text-main);
                  margin-bottom: 12px;
                  text-align: center;
            }

            .step-subtitle {
                  text-align: center;
                  color: var(--text-muted);
                  font-size: 16px;
                  margin-bottom: 40px;
            }

            .student-grid {
                  display: grid;
                  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                  gap: 16px;
                  margin-top: 24px;
            }

            .student-card {
                  background: var(--card-bg);
                  border: 2px solid var(--border-color);
                  border-radius: 12px;
                  padding: 20px;
                  text-align: center;
                  cursor: pointer;
                  transition: all 0.3s ease;
            }

            .student-card:hover {
                  transform: translateY(-4px);
                  border-color: var(--primary-light);
                  box-shadow: var(--shadow-lg);
            }

            .student-card.selected {
                  border-color: var(--primary-color);
                  background: var(--info-bg);
                  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            }

            .student-avatar {
                  width: 80px;
                  height: 80px;
                  border-radius: 50%;
                  background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  margin: 0 auto 12px;
                  font-size: 36px;
                  color: white;
                  font-weight: 700;
            }

            .student-name {
                  font-weight: 700;
                  color: var(--text-main);
                  margin-bottom: 4px;
            }

            .student-class {
                  font-size: 13px;
                  color: var(--text-muted);
            }

            .search-box {
                  margin-bottom: 24px;
                  position: relative;
            }

            .search-box input {
                  width: 100%;
                  padding: 16px 20px 16px 50px;
                  border: 2px solid var(--border-color);
                  border-radius: 12px;
                  font-size: 16px;
                  transition: all 0.3s ease;
            }

            .search-box i {
                  position: absolute;
                  left: 18px;
                  top: 50%;
                  transform: translateY(-50%);
                  color: var(--text-muted);
                  font-size: 18px;
            }

            .badge-category {
                  display: inline-block;
                  margin-bottom: 24px;
            }

            .badge-grid {
                  display: grid;
                  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                  gap: 20px;
            }

            .badge-card {
                  background: var(--card-bg);
                  border: 2px solid var(--border-color);
                  border-radius: 16px;
                  padding: 24px;
                  text-align: center;
                  cursor: pointer;
                  transition: all 0.3s ease;
                  position: relative;
            }

            .badge-card:hover {
                  transform: translateY(-6px) scale(1.03);
                  border-color: var(--primary-color);
                  box-shadow: var(--shadow-xl);
            }

            .badge-card.selected {
                  border-color: var(--primary-color);
                  background: var(--info-bg);
                  box-shadow: 0 0 0 6px rgba(99, 102, 241, 0.15);
            }

            .badge-card.selected::after {
                  content: '✓';
                  position: absolute;
                  top: 12px;
                  right: 12px;
                  width: 32px;
                  height: 32px;
                  background: var(--success-color);
                  color: white;
                  border-radius: 50%;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  font-weight: 900;
                  font-size: 18px;
            }

            .badge-emoji {
                  font-size: 72px;
                  margin-bottom: 16px;
                  display: block;
                  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
            }

            .badge-name {
                  font-weight: 800;
                  font-size: 18px;
                  color: var(--text-main);
                  margin-bottom: 8px;
            }

            .badge-desc {
                  font-size: 13px;
                  color: var(--text-muted);
                  line-height: 1.4;
            }

            .summary-box {
                  background: linear-gradient(135deg, var(--info-bg) 0%, var(--card-bg) 100%);
                  border: 2px solid var(--primary-color);
                  border-radius: 20px;
                  padding: 40px;
                  text-align: center;
            }

            .summary-emoji {
                  font-size: 120px;
                  margin-bottom: 24px;
                  animation: bounce 2s infinite;
            }

            .summary-title {
                  font-size: 28px;
                  font-weight: 900;
                  color: var(--text-main);
                  margin-bottom: 16px;
            }

            .summary-detail {
                  font-size: 16px;
                  color: var(--text-muted);
                  margin-bottom: 12px;
            }

            .summary-detail strong {
                  color: var(--primary-color);
            }

            .wizard-actions {
                  display: flex;
                  justify-content: space-between;
                  margin-top: 40px;
                  gap: 16px;
            }

            .btn-wizard {
                  flex: 1;
                  padding: 16px 32px;
                  border: none;
                  border-radius: 12px;
                  font-weight: 700;
                  font-size: 16px;
                  cursor: pointer;
                  transition: all 0.3s ease;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  gap: 10px;
            }

            .btn-back {
                  background: var(--secondary-color);
                  color: white;
            }

            .btn-back:hover {
                  background: #475569;
                  transform: translateY(-2px);
            }

            .btn-next {
                  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
                  color: white;
            }

            .btn-next:hover {
                  transform: translateY(-2px);
                  box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
            }

            .btn-submit {
                  background: linear-gradient(135deg, var(--success-color), #16a34a);
                  color: white;
            }

            .btn-submit:hover {
                  transform: translateY(-2px);
                  box-shadow: 0 8px 20px rgba(34, 197, 94, 0.4);
            }

            .reason-box {
                  margin-top: 32px;
            }

            .reason-box textarea {
                  width: 100%;
                  min-height: 120px;
                  padding: 16px;
                  border: 2px solid var(--border-color);
                  border-radius: 12px;
                  font-size: 15px;
                  font-family: inherit;
                  resize: vertical;
            }

            .quick-reasons {
                  display: flex;
                  flex-wrap: wrap;
                  gap: 8px;
                  margin-top: 12px;
            }

            .quick-reason {
                  padding: 8px 16px;
                  background: var(--info-bg);
                  color: var(--info-text);
                  border: 1px solid var(--primary-light);
                  border-radius: 20px;
                  font-size: 13px;
                  font-weight: 600;
                  cursor: pointer;
                  transition: all 0.2s ease;
            }

            .quick-reason:hover {
                  background: var(--primary-color);
                  color: white;
                  transform: scale(1.05);
            }
      </style>
</head>

<body>

      <header class="topbar">
            <div class="logo"><i class="fa-solid fa-graduation-cap"></i> Portfolyo Sistemi</div>
            <div class="user-info">
                  <a href="index.php"><i class="fa-solid fa-house"></i> Panel</a>
                  <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars(currentUserName()); ?></span>
            </div>
      </header>

      <main class="main-content">
            <div class="wizard-container">

                  <?php if ($success): ?>
                        <div class="alert-success">
                              <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                        </div>
                  <?php endif; ?>

                  <?php if ($error): ?>
                        <div class="alert-error">
                              <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
                        </div>
                  <?php endif; ?>

                  <?php if (!$error): ?>

                        <!-- Progress Bar -->
                        <div class="progress-bar">
                              <div class="progress-line" id="progressLine" style="width: 0%"></div>
                              <div class="step active" id="step-indicator-1">
                                    <div class="step-circle">👤</div>
                                    <div class="step-label">Öğrenci Seç</div>
                              </div>
                              <div class="step" id="step-indicator-2">
                                    <div class="step-circle">🏆</div>
                                    <div class="step-label">Rozet Seç</div>
                              </div>
                              <div class="step" id="step-indicator-3">
                                    <div class="step-circle">📝</div>
                                    <div class="step-label">Detaylar</div>
                              </div>
                              <div class="step" id="step-indicator-4">
                                    <div class="step-circle">✨</div>
                                    <div class="step-label">Onayla</div>
                              </div>
                        </div>

                        <form method="post" id="wizardForm">
                              <div class="wizard-content">

                                    <!-- Step 1: Öğrenci Seçimi -->
                                    <div class="wizard-step active" id="step-1">
                                          <div class="step-title">Hangi öğrenciye rozet vereceksiniz? 👤</div>
                                          <div class="step-subtitle">Aşağıdan bir öğrenci seçin</div>

                                          <div class="search-box">
                                                <i class="fa-solid fa-search"></i>
                                                <input type="text" id="studentSearch" placeholder="Öğrenci adı, sınıf veya numara ara...">
                                          </div>

                                          <div class="student-grid">
                                                <?php foreach ($ogrenciler as $o): ?>
                                                      <div class="student-card" onclick="selectStudent(<?= $o['id'] ?>, '<?= htmlspecialchars($o['ad'] . ' ' . $o['soyad']) ?>', '<?= htmlspecialchars($o['sinif'] . $o['sube']) ?>')">
                                                            <div class="student-avatar">
                                                                  <?= mb_substr($o['ad'], 0, 1) . mb_substr($o['soyad'], 0, 1) ?>
                                                            </div>
                                                            <div class="student-name"><?= htmlspecialchars($o['ad'] . ' ' . $o['soyad']) ?></div>
                                                            <div class="student-class"><?= htmlspecialchars($o['sinif'] . $o['sube']) ?> - <?= htmlspecialchars($o['ogr_no']) ?></div>
                                                      </div>
                                                <?php endforeach; ?>
                                          </div>

                                          <input type="hidden" name="ogrenci_id" id="selectedStudent">
                                    </div>

                                    <!-- Step 2: Rozet Seçimi -->
                                    <div class="wizard-step" id="step-2">
                                          <div class="step-title">Hangi rozeti vereceksiniz? 🏆</div>
                                          <div class="step-subtitle">Kategori seçin ve rozet seçin</div>

                                          <?php foreach ($rozetler as $kategori => $rozetListesi): ?>
                                                <div style="margin-bottom: 40px;">
                                                      <span class="badge badge-blue badge-category" style="font-size: 16px; padding: 10px 20px;">
                                                            <?= $categoryEmojis[$kategori] ?> <?= ucfirst($kategori) ?>
                                                      </span>

                                                      <div class="badge-grid">
                                                            <?php foreach ($rozetListesi as $r): ?>
                                                                  <div class="badge-card" onclick="selectBadge(<?= $r['id'] ?>, '<?= htmlspecialchars($r['ad']) ?>', '<?= $categoryEmojis[$kategori] ?>')">
                                                                        <div class="badge-emoji"><?= $categoryEmojis[$kategori] ?></div>
                                                                        <div class="badge-name"><?= htmlspecialchars($r['ad']) ?></div>
                                                                        <div class="badge-desc"><?= htmlspecialchars($r['kosul_aciklama']) ?></div>
                                                                  </div>
                                                            <?php endforeach; ?>
                                                      </div>
                                                </div>
                                          <?php endforeach; ?>

                                          <input type="hidden" name="rozet_id" id="selectedBadge">
                                    </div>

                                    <!-- Step 3: Detaylar -->
                                    <div class="wizard-step" id="step-3">
                                          <div class="step-title">Neden bu rozeti veriyorsunuz? 📝</div>
                                          <div class="step-subtitle">Başarının detaylarını paylaşın</div>

                                          <div style="max-width: 600px; margin: 0 auto;">
                                                <label style="display: block; margin-bottom: 8px; font-weight: 700;">
                                                      <i class="fa-regular fa-calendar"></i> Veriliş Tarihi
                                                </label>
                                                <input type="date" name="tarih" required value="<?= date('Y-m-d') ?>"
                                                      style="width: 100%; padding: 12px; border-radius: 12px; border: 2px solid var(--border-color); margin-bottom: 24px;">

                                                <label style="display: block; margin-bottom: 8px; font-weight: 700;">
                                                      <i class="fa-solid fa-pen"></i> Açıklama
                                                </label>
                                                <textarea name="aciklama" id="reasonText" rows="5"
                                                      placeholder="Örn: Okul futbol turnuvasında 8 gol atarak şampiyon oldu..."
                                                      style="width: 100%; padding: 16px; border-radius: 12px; border: 2px solid var(--border-color); resize: vertical;"></textarea>

                                                <div style="margin-top: 12px; font-size: 13px; color: var(--text-muted);">
                                                      💡 İpucu: Hızlı başlangıç için aşağıdaki şablonlardan birini kullanabilirsiniz
                                                </div>

                                                <div class="quick-reasons">
                                                      <div class="quick-reason" onclick="useQuickReason('Okul yarışmasında birinci oldu')">
                                                            🥇 Yarışma Birinciliği
                                                      </div>
                                                      <div class="quick-reason" onclick="useQuickReason('Dönem boyunca örnek davranışlar sergiledi')">
                                                            ⭐ Örnek Davranış
                                                      </div>
                                                      <div class="quick-reason" onclick="useQuickReason('Sınıf arkadaşlarına yardım etti')">
                                                            🤝 Yardımseverlik
                                                      </div>
                                                      <div class="quick-reason" onclick="useQuickReason('Proje çalışmasında üstün başarı gösterdi')">
                                                            📊 Proje Başarısı
                                                      </div>
                                                </div>
                                          </div>
                                    </div>

                                    <!-- Step 4: Özet ve Onay -->
                                    <div class="wizard-step" id="step-4">
                                          <div class="summary-box">
                                                <div class="summary-emoji" id="summaryEmoji">🎉</div>
                                                <div class="summary-title">Rozet Vermeye Hazır!</div>
                                                <div class="summary-detail">
                                                      <strong id="summaryStudent">-</strong> adlı öğrenciye
                                                </div>
                                                <div class="summary-detail">
                                                      <strong id="summaryBadge">-</strong> rozeti verilecek
                                                </div>
                                                <div class="summary-detail" style="margin-top: 24px; font-size: 14px; font-style: italic;">
                                                      "Bu başarı öğrencinin dosyasına kaydedilecek ve portfolyosunda görünecektir."
                                                </div>
                                          </div>
                                    </div>

                              </div>

                              <!-- Wizard Actions -->
                              <div class="wizard-actions">
                                    <button type="button" class="btn-wizard btn-back" id="btnBack" onclick="previousStep()" style="display: none;">
                                          <i class="fa-solid fa-arrow-left"></i> Geri
                                    </button>
                                    <button type="button" class="btn-wizard btn-next" id="btnNext" onclick="nextStep()">
                                          İleri <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                    <button type="submit" class="btn-wizard btn-submit" id="btnSubmit" style="display: none;">
                                          <i class="fa-solid fa-gift"></i> Rozeti Ver
                                    </button>
                              </div>
                        </form>

                  <?php endif; ?>

            </div>
      </main>

      <script src="script.js"></script>
      <script>
            let currentStep = 1;
            const totalSteps = 4;

            let selectedStudentId = null;
            let selectedStudentName = '';
            let selectedStudentClass = '';
            let selectedBadgeId = null;
            let selectedBadgeName = '';
            let selectedBadgeEmoji = '';

            function selectStudent(id, name, className) {
                  selectedStudentId = id;
                  selectedStudentName = name;
                  selectedStudentClass = className;
                  document.getElementById('selectedStudent').value = id;

                  // Tüm kartları temizle
                  document.querySelectorAll('.student-card').forEach(card => {
                        card.classList.remove('selected');
                  });

                  // Seçili kartı işaretle
                  event.target.closest('.student-card').classList.add('selected');

                  // Otomatik ilerleme
                  setTimeout(() => nextStep(), 500);
            }

            function selectBadge(id, name, emoji) {
                  selectedBadgeId = id;
                  selectedBadgeName = name;
                  selectedBadgeEmoji = emoji;
                  document.getElementById('selectedBadge').value = id;

                  // Tüm kartları temizle
                  document.querySelectorAll('.badge-card').forEach(card => {
                        card.classList.remove('selected');
                  });

                  // Seçili kartı işaretle
                  event.target.closest('.badge-card').classList.add('selected');

                  // Otomatik ilerleme
                  setTimeout(() => nextStep(), 500);
            }

            function nextStep() {
                  // Validasyon
                  if (currentStep === 1 && !selectedStudentId) {
                        alert('❌ Lütfen bir öğrenci seçin!');
                        return;
                  }

                  if (currentStep === 2 && !selectedBadgeId) {
                        alert('❌ Lütfen bir rozet seçin!');
                        return;
                  }

                  if (currentStep < totalSteps) {
                        currentStep++;
                        updateWizard();
                  }
            }

            function previousStep() {
                  if (currentStep > 1) {
                        currentStep--;
                        updateWizard();
                  }
            }

            function updateWizard() {
                  // Adımları güncelle
                  document.querySelectorAll('.wizard-step').forEach(step => {
                        step.classList.remove('active');
                  });
                  document.getElementById('step-' + currentStep).classList.add('active');

                  // Progress bar güncelle
                  document.querySelectorAll('.step').forEach((step, index) => {
                        step.classList.remove('active', 'completed');
                        if (index + 1 < currentStep) {
                              step.classList.add('completed');
                        } else if (index + 1 === currentStep) {
                              step.classList.add('active');
                        }
                  });

                  const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
                  document.getElementById('progressLine').style.width = progress + '%';

                  // Buton görünürlüğü
                  document.getElementById('btnBack').style.display = currentStep > 1 ? 'flex' : 'none';
                  document.getElementById('btnNext').style.display = currentStep < totalSteps ? 'flex' : 'none';
                  document.getElementById('btnSubmit').style.display = currentStep === totalSteps ? 'flex' : 'none';

                  // Son adımda özeti güncelle
                  if (currentStep === 4) {
                        document.getElementById('summaryStudent').textContent = selectedStudentName + ' (' + selectedStudentClass + ')';
                        document.getElementById('summaryBadge').textContent = selectedBadgeName;
                        document.getElementById('summaryEmoji').textContent = selectedBadgeEmoji;
                  }

                  // Yukarı kaydır
                  window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                  });
            }

            function useQuickReason(text) {
                  document.getElementById('reasonText').value = text;
            }

            // Öğrenci arama
            document.getElementById('studentSearch').addEventListener('keyup', function() {
                  const filter = this.value.toLowerCase();
                  document.querySelectorAll('.student-card').forEach(card => {
                        const text = card.textContent.toLowerCase();
                        card.style.display = text.includes(filter) ? 'block' : 'none';
                  });
            });

            // Form gönderimi
            document.getElementById('wizardForm').addEventListener('submit', function(e) {
                  const btn = document.getElementById('btnSubmit');
                  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Veriliyor...';
                  btn.disabled = true;
            });
      </script>

</body>

</html>