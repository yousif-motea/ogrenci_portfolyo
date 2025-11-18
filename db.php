<?php
$host = 'localhost';
$db   = 'ogrenci_portfolyo_db'; // phpMyAdmin'de oluşturduğun DB adı
$user = 'root';                 // XAMPP varsayılan
$pass = '';                     // şifre yoksa boş bırak
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
      $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
      die('Veritabanı bağlantı hatası: ' . $e->getMessage());
}
