<?php
require_once 'functions.php';
requireLogin();

// Yetki kontrolü
$role = currentUserRole();
if (!in_array($role, ['yonetici', 'rehber'])) {
      die("Bu işlem için yetkiniz yok.");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
      $stmt = $pdo->prepare("DELETE FROM pdr_kayitlari WHERE id = ?");
      $stmt->execute([$id]);
}

// Listeye geri dön
header("Location: pdr_list.php");
exit;
