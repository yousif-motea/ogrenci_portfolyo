<?php
require_once 'functions.php';
requireLogin();

// Sadece yönetici kullanabilsin
if (currentUserRole() !== 'yonetici') {
      die("Bu sayfaya erişim yetkiniz yok.");
}

$stmt = $pdo->query("SELECT * FROM ogrenciler ORDER BY id DESC");
$ogrenciler = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">

<head>
      <meta charset="UTF-8">
      <title>Öğrenci Yönetimi</title>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="stylesheet" href="style.css">

      <style>
            /* Tablo içi butonlar için ufak stil */
            .action-btn {
                  padding: 6px 10px;
                  border-radius: 6px;
                  color: #fff;
                  font-size: 12px;
                  margin-right: 4px;
                  transition: opacity 0.2s;
            }

            .btn-edit {
                  background-color: #3b82f6;
            }

            .btn-delete {
                  background-color: #ef4444;
            }

            .action-btn:hover {
                  opacity: 0.8;
                  color: #fff;
                  text-decoration: none;
            }

            /* Başlık ve Buton hizalaması */
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
                        <h1>Öğrenci Yönetimi</h1>
                        <p>Sistemdeki kayıtlı öğrencileri buradan yönetebilirsiniz.</p>
                  </div>
                  <a href="ogrenci_ekle.php" class="btn">
                        <i class="fa-solid fa-plus"></i> Yeni Öğrenci Ekle
                  </a>
            </div>

            <div style="margin-bottom: 15px; position: relative; max-width: 400px;">
                  <i class="fa-solid fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                  <input type="text" id="tableSearch" placeholder="İsim, numara veya sınıf ara..." style="padding-left: 36px; width: 100%;">
            </div>

            <div class="card" style="padding: 0; overflow: hidden;">
                  <table cellpadding="0" cellspacing="0">
                        <thead>
                              <tr>
                                    <th>ID</th>
                                    <th>Öğrenci No</th>
                                    <th>Ad Soyad</th>
                                    <th>Sınıf/Şube</th>
                                    <th style="text-align: right;">İşlemler</th>
                              </tr>
                        </thead>
                        <tbody>
                              <?php foreach ($ogrenciler as $o): ?>
                                    <tr class="hover-row">
                                          <td><?= $o['id'] ?></td>
                                          <td><span class="badge badge-blue"><?= htmlspecialchars($o['ogr_no']) ?></span></td>
                                          <td><strong><?= htmlspecialchars($o['ad'] . ' ' . $o['soyad']) ?></strong></td>
                                          <td><?= htmlspecialchars($o['sinif'] . ' / ' . $o['sube']) ?></td>
                                          <td style="text-align: right;">
                                                <a href="ogrenci_portfolyo.php?ogrenci_id=<?= $o['id'] ?>" class="action-btn" style="background:#64748b;" title="Portfolyo Görüntüle">
                                                      <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="ogrenci_duzenle.php?id=<?= $o['id'] ?>" class="action-btn btn-edit" title="Düzenle">
                                                      <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <a href="ogrenci_sil.php?id=<?= $o['id'] ?>" class="action-btn btn-delete" onclick="return confirm('Bu öğrenciyi ve tüm kayıtlarını silmek istediğinize emin misiniz?')" title="Sil">
                                                      <i class="fa-solid fa-trash"></i>
                                                </a>
                                          </td>
                                    </tr>
                              <?php endforeach; ?>
                        </tbody>
                  </table>

                  <?php if (empty($ogrenciler)): ?>
                        <div style="padding: 20px; text-align: center; color: #666;">
                              Henüz kayıtlı öğrenci bulunmuyor.
                        </div>
                  <?php endif; ?>
            </div>

      </main>

      <script src="script.js"></script>

</body>

</html>