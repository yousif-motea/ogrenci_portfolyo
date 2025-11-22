<?php
require 'db.php';

// Veli bilgileri
$username = 'veli1';
$plainPassword = '123456';
$ad = 'Mehmet';
$soyad = 'Yılmaz';

$passwordHash = password_hash($plainPassword, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("INSERT INTO users (username, password_hash, ad, soyad, role)
                       VALUES (?, ?, ?, ?, 'veli')");
$stmt->execute([$username, $passwordHash, $ad, $soyad]);

$veli_id = $pdo->lastInsertId();

echo "Veli kullanıcısı oluşturuldu.<br>";
echo "Kullanıcı adı: $username<br>";
echo "Şifre: $plainPassword<br>";
echo "Veli user_id: $veli_id<br>";
