<?php
require_once 'functions.php';
requireLogin();

if (currentUserRole() !== 'yonetici') {
      die("Bu sayfaya erişim yetkiniz yok.");
}

$sql = "SELECT a.*, t.ad AS tur_adi 
        FROM aktiviteler a
        LEFT JOIN aktivite_turleri t ON a.tur_id = t.id
        ORDER BY a.tarih DESC, a.id DESC";
$stmt = $pdo->query($sql);
$aktiviteler = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Aktivite Yönetimi</title>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="style.css">

      <style>
            .action-btn {
                  padding: 6px 10px;
                  border-radius: 6px;
                  color: #fff;
                  font-size: 12px;
                  margin-right: 4px;
                  transition: opacity 0.2s;
                  display: inline-flex;
                  align-items: center;
                  gap: 5px;
            }

            .btn-purple {
                  background-color: #8b5cf6;
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
                  <a href="index.php" style="margin-right: 15px; color: #e0e7ff;"><i class="fa-solid fa-house"></i> Panel</a>
                  <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars(currentUserName()); ?></span>
                  <a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Çıkış</a>
            </div>
      </header>

      <main class="main-content">

            <div class="page-header-flex">
                  <div>
                        <h1>Aktiviteler</h1>
                        <p>Okul içi ve dışı düzenlenen etkinliklerin listesi.</p>
                  </div>
                  <a href="aktivite_ekle.php" class="btn">
                        <i class="fa-solid fa-plus"></i> Yeni Aktivite Ekle
                  </a>
            </div>

            <div style="margin-bottom: 15px; position: relative; max-width: 400px;">
                  <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                  <input type="text" id="tableSearch" placeholder="Aktivite başlığı veya türü ara..." style="padding-left: 36px; width: 100%;">
            </div>

            <div class="card" style="padding: 0; overflow: hidden;">
                  <table cellpadding="0" cellspacing="0">
                        <thead>
                              <tr>
                                    <th>ID</th>
                                    <th>Başlık</th>
                                    <th>Aktivite Türü</th>
                                    <th>Tarih</th>
                                    <th style="text-align: right;">İşlemler</th>
                              </tr>
                        </thead>
                        <tbody>
                              <?php foreach ($aktiviteler as $a): ?>
                                    <tr class="hover-row">
                                          <td><?= $a['id'] ?></td>
                                          <td><strong><?= htmlspecialchars($a['baslik']) ?></strong></td>
                                          <td>
                                                <span class="badge badge-orange"><?= htmlspecialchars($a['tur_adi']) ?></span>
                                          </td>
                                          <td>
                                                <i class="fa-regular fa-calendar" style="color:#94a3b8; margin-right:4px;"></i>
                                                <?= $a['tarih'] ?>
                                          </td>
                                          <td style="text-align: right;">
                                                <a href="aktivite_katilim.php?id=<?= $a['id'] ?>" class="action-btn btn-purple" title="Katılımcı Listesi">
                                                      <i class="fa-solid fa-users-viewfinder"></i> Katılımlar
                                                </a>
                                          </td>
                                    </tr>
                              <?php endforeach; ?>
                        </tbody>
                  </table>

                  <?php if (empty($aktiviteler)): ?>
                        <div style="padding: 20px; text-align: center; color: #666;">
                              Henüz kayıtlı aktivite bulunmuyor.
                        </div>
                  <?php endif; ?>
            </div>

      </main>
      <script src="script.js"></script>

</body>

</html>