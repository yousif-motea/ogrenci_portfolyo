<?php
require_once 'functions.php';
require_once 'db.php';
requireLogin();

// Yetki Kontrolü
if (!in_array(currentUserRole(), ['yonetici', 'ogretmen', 'rehber'])) {
      die("Bu işlemi yapmaya yetkiniz yok.");
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
      die("Geçersiz ID.");
}

try {
      $pdo->beginTransaction();

      // Öğrenci numarasını al (Users tablosu için)
      $stmt = $pdo->prepare("SELECT ogr_no FROM ogrenciler WHERE id = ?");
      $stmt->execute([$id]);
      $ogrenci = $stmt->fetch();

      if ($ogrenci) {
            $ogr_no = $ogrenci['ogr_no'];

            // Bağlı tabloları temizle
            $tables = ['ogrenci_aktiviteleri', 'ogrenci_rozetleri', 'disiplin_kayitlari', 'pdr_kayitlari'];
            foreach ($tables as $table) {
                  $stmt = $pdo->prepare("DELETE FROM $table WHERE ogrenci_id = ?");
                  $stmt->execute([$id]);
            }

            // Ana kaydı sil
            $stmt = $pdo->prepare("DELETE FROM ogrenciler WHERE id = ?");
            $stmt->execute([$id]);

            // Giriş hesabını sil
            $stmt = $pdo->prepare("DELETE FROM users WHERE username = ? AND role = 'ogrenci'");
            $stmt->execute([$ogr_no]);

            $pdo->commit();
      } else {
            $pdo->rollBack();
      }

      header("Location: ogrenciler.php?msg=silindi");
      exit;
} catch (PDOException $e) {
      $pdo->rollBack();
      die("Silme hatası: " . $e->getMessage());
}
