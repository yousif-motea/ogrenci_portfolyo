<?php
require_once 'functions.php';
requireLogin();

if (currentUserRole() !== 'yonetici') {
      die("Yetkiniz yok.");
}

$id = $_GET['id'] ?? 0;

// Sadece rozet tanımını siliyoruz, ogrenci_rozetleri kayıtları kalabilir (geçmiş olsun diye)
$stmt = $pdo->prepare("DELETE FROM rozetler WHERE id = ?");
$stmt->execute([$id]);

header("Location: rozetler.php");
exit;
