<?php
require_once 'functions.php';
requireLogin();

// Yetki Kontrolü
$role = currentUserRole();
if (!in_array($role, ['yonetici', 'rehber'])) {
      die("Bu işlem için yetkiniz yok.");
}

// ID Kontrolü
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
      $stmt = $pdo->prepare("DELETE FROM disiplin_kayitlari WHERE id = ?");
      $stmt->execute([$id]);
}

// İşlem bitince listeye dön
header("Location: disiplin_list.php");
exit;
