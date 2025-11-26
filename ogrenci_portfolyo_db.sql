-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 26 Kas 2025, 12:56:46
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `ogrenci_portfolyo_db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `aktiviteler`
--

CREATE TABLE `aktiviteler` (
  `id` int(11) NOT NULL,
  `tur_id` int(11) NOT NULL,
  `baslik` varchar(100) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `tarih` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `aktiviteler`
--

INSERT INTO `aktiviteler` (`id`, `tur_id`, `baslik`, `aciklama`, `tarih`) VALUES
(1, 3, 'ww', 'ww', '2000-11-20'),
(2, 1, 'voleybol turnuvası', '..', '2025-11-26');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `aktivite_turleri`
--

CREATE TABLE `aktivite_turleri` (
  `id` int(11) NOT NULL,
  `ad` varchar(50) NOT NULL,
  `aciklama` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `aktivite_turleri`
--

INSERT INTO `aktivite_turleri` (`id`, `ad`, `aciklama`) VALUES
(1, 'Sportif', 'Spor faaliyetleri (turnuva, maç, beden eğitimi dışı etkinlikler)'),
(2, 'Sosyal', 'Toplumsal ve sosyal etkinlikler'),
(3, 'Akademik', 'Proje, yarışma, seminer vb. akademik çalışmalar'),
(4, 'Kültürel', 'Gezi, tiyatro, müze ziyaretleri vb. kültürel etkinlikler');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `disiplin_kayitlari`
--

CREATE TABLE `disiplin_kayitlari` (
  `id` int(11) NOT NULL,
  `ogrenci_id` int(11) NOT NULL,
  `tarih` date NOT NULL,
  `seviye` enum('uyari','kınama','uzaklaştırma') DEFAULT 'uyari',
  `aciklama` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `disiplin_kayitlari`
--

INSERT INTO `disiplin_kayitlari` (`id`, `ogrenci_id`, `tarih`, `seviye`, `aciklama`) VALUES
(2, 10, '2025-11-24', '', 'Test'),
(3, 11, '2025-11-26', 'kınama', 'dfdf');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ogrenciler`
--

CREATE TABLE `ogrenciler` (
  `id` int(11) NOT NULL,
  `ogr_no` varchar(20) NOT NULL,
  `ad` varchar(50) NOT NULL,
  `soyad` varchar(50) NOT NULL,
  `sinif` varchar(10) DEFAULT NULL,
  `sube` varchar(5) DEFAULT NULL,
  `dogum_tarihi` date DEFAULT NULL,
  `veli_user_id` int(11) DEFAULT NULL,
  `rehber_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `ogrenciler`
--

INSERT INTO `ogrenciler` (`id`, `ogr_no`, `ad`, `soyad`, `sinif`, `sube`, `dogum_tarihi`, `veli_user_id`, `rehber_user_id`, `created_at`) VALUES
(10, '2000', 'Hatice', 'Çil', '9', 'A', '2002-11-20', NULL, NULL, '2025-11-22 12:50:05'),
(11, '2001', 'Kübra', 'Önder', '10', 'B', '2000-01-01', NULL, NULL, '2025-11-26 10:52:30');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ogrenci_aktiviteleri`
--

CREATE TABLE `ogrenci_aktiviteleri` (
  `id` int(11) NOT NULL,
  `ogrenci_id` int(11) NOT NULL,
  `aktivite_id` int(11) NOT NULL,
  `katilim_durumu` enum('katildi','katilmadi') DEFAULT 'katildi',
  `notlar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `ogrenci_aktiviteleri`
--

INSERT INTO `ogrenci_aktiviteleri` (`id`, `ogrenci_id`, `aktivite_id`, `katilim_durumu`, `notlar`, `created_at`) VALUES
(5, 10, 2, 'katildi', NULL, '2025-11-26 10:53:33');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ogrenci_rozetleri`
--

CREATE TABLE `ogrenci_rozetleri` (
  `id` int(11) NOT NULL,
  `ogrenci_id` int(11) NOT NULL,
  `rozet_id` int(11) NOT NULL,
  `verilis_tarihi` date NOT NULL,
  `aciklama` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `ogrenci_rozetleri`
--

INSERT INTO `ogrenci_rozetleri` (`id`, `ogrenci_id`, `rozet_id`, `verilis_tarihi`, `aciklama`) VALUES
(1, 10, 1, '2025-11-22', 'Başarılı Bir şekilde 10 kitap okuduğun için hak kazandın'),
(2, 10, 1, '2025-11-24', 'Dönem boyunca örnek davranışlar sergiledi'),
(3, 10, 2, '2025-11-24', 'Okul yarışmasında birinci oldu'),
(4, 10, 3, '2025-11-26', 'Okul yarışmasında birinci oldu');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `pdr_kayitlari`
--

CREATE TABLE `pdr_kayitlari` (
  `id` int(11) NOT NULL,
  `ogrenci_id` int(11) NOT NULL,
  `rehber_user_id` int(11) NOT NULL,
  `tarih` date NOT NULL,
  `konu` varchar(150) DEFAULT NULL,
  `aciklama` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `pdr_kayitlari`
--

INSERT INTO `pdr_kayitlari` (`id`, `ogrenci_id`, `rehber_user_id`, `tarih`, `konu`, `aciklama`) VALUES
(1, 10, 6, '2025-11-22', 'Verimli Ders Çalışma', 'Verimli ders çalışma konusu ele alınacak.');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `rozetler`
--

CREATE TABLE `rozetler` (
  `id` int(11) NOT NULL,
  `ad` varchar(100) NOT NULL,
  `kategori` enum('sportif','sosyal','akademik','disiplin') NOT NULL,
  `kosul_aciklama` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `rozetler`
--

INSERT INTO `rozetler` (`id`, `ad`, `kategori`, `kosul_aciklama`) VALUES
(1, 'Kitap Kurdu', 'akademik', 'Bir dönemde 10 Kitap okuyan öğrencilere verilir.'),
(2, 'Yarışma Birinciliği', 'sportif', 'Tebrikler yarışma birincisi oldunuz'),
(3, 'Voleybol Turnuvası Birinciliği1', 'sportif', '....');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `ad` varchar(50) NOT NULL,
  `soyad` varchar(50) NOT NULL,
  `role` enum('yonetici','rehber','ogretmen','veli','ogrenci') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `ad`, `soyad`, `role`, `created_at`) VALUES
(6, 'admin', '$2y$12$kZ7T.yE65pWnsBkcqzJHl.id3DoGz8nHzTFOwuwxv4h2yCOFjZGlW', 'Kübra', 'ÖNDER', 'yonetici', '2025-11-21 10:35:36'),
(12, '2000', '$2y$12$/Wg8CrauXWEWbdeUBqF9L.vBM4eHwFjvFsVpl4LWThlDoEa.oJOkC', 'Hatice', 'Çil', 'ogrenci', '2025-11-22 12:50:05'),
(14, '2001', '$2y$12$hINHGtDnI/J0auWPoijmEeBAbCCyA0IsZwVBkYSAC2Tx9OVn3uyEu', 'Kübra', 'Önder', 'ogrenci', '2025-11-26 10:52:30');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `aktiviteler`
--
ALTER TABLE `aktiviteler`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tur_id` (`tur_id`);

--
-- Tablo için indeksler `aktivite_turleri`
--
ALTER TABLE `aktivite_turleri`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `disiplin_kayitlari`
--
ALTER TABLE `disiplin_kayitlari`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ogrenci_id` (`ogrenci_id`);

--
-- Tablo için indeksler `ogrenciler`
--
ALTER TABLE `ogrenciler`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ogr_no` (`ogr_no`),
  ADD KEY `veli_user_id` (`veli_user_id`),
  ADD KEY `rehber_user_id` (`rehber_user_id`);

--
-- Tablo için indeksler `ogrenci_aktiviteleri`
--
ALTER TABLE `ogrenci_aktiviteleri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ogrenci_id` (`ogrenci_id`),
  ADD KEY `aktivite_id` (`aktivite_id`);

--
-- Tablo için indeksler `ogrenci_rozetleri`
--
ALTER TABLE `ogrenci_rozetleri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ogrenci_id` (`ogrenci_id`),
  ADD KEY `rozet_id` (`rozet_id`);

--
-- Tablo için indeksler `pdr_kayitlari`
--
ALTER TABLE `pdr_kayitlari`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ogrenci_id` (`ogrenci_id`),
  ADD KEY `rehber_user_id` (`rehber_user_id`);

--
-- Tablo için indeksler `rozetler`
--
ALTER TABLE `rozetler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `aktiviteler`
--
ALTER TABLE `aktiviteler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `aktivite_turleri`
--
ALTER TABLE `aktivite_turleri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `disiplin_kayitlari`
--
ALTER TABLE `disiplin_kayitlari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `ogrenciler`
--
ALTER TABLE `ogrenciler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Tablo için AUTO_INCREMENT değeri `ogrenci_aktiviteleri`
--
ALTER TABLE `ogrenci_aktiviteleri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `ogrenci_rozetleri`
--
ALTER TABLE `ogrenci_rozetleri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `pdr_kayitlari`
--
ALTER TABLE `pdr_kayitlari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `rozetler`
--
ALTER TABLE `rozetler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `aktiviteler`
--
ALTER TABLE `aktiviteler`
  ADD CONSTRAINT `aktiviteler_ibfk_1` FOREIGN KEY (`tur_id`) REFERENCES `aktivite_turleri` (`id`);

--
-- Tablo kısıtlamaları `disiplin_kayitlari`
--
ALTER TABLE `disiplin_kayitlari`
  ADD CONSTRAINT `disiplin_kayitlari_ibfk_1` FOREIGN KEY (`ogrenci_id`) REFERENCES `ogrenciler` (`id`);

--
-- Tablo kısıtlamaları `ogrenciler`
--
ALTER TABLE `ogrenciler`
  ADD CONSTRAINT `ogrenciler_ibfk_1` FOREIGN KEY (`veli_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ogrenciler_ibfk_2` FOREIGN KEY (`rehber_user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `ogrenci_aktiviteleri`
--
ALTER TABLE `ogrenci_aktiviteleri`
  ADD CONSTRAINT `ogrenci_aktiviteleri_ibfk_1` FOREIGN KEY (`ogrenci_id`) REFERENCES `ogrenciler` (`id`),
  ADD CONSTRAINT `ogrenci_aktiviteleri_ibfk_2` FOREIGN KEY (`aktivite_id`) REFERENCES `aktiviteler` (`id`);

--
-- Tablo kısıtlamaları `ogrenci_rozetleri`
--
ALTER TABLE `ogrenci_rozetleri`
  ADD CONSTRAINT `ogrenci_rozetleri_ibfk_1` FOREIGN KEY (`ogrenci_id`) REFERENCES `ogrenciler` (`id`),
  ADD CONSTRAINT `ogrenci_rozetleri_ibfk_2` FOREIGN KEY (`rozet_id`) REFERENCES `rozetler` (`id`);

--
-- Tablo kısıtlamaları `pdr_kayitlari`
--
ALTER TABLE `pdr_kayitlari`
  ADD CONSTRAINT `pdr_kayitlari_ibfk_1` FOREIGN KEY (`ogrenci_id`) REFERENCES `ogrenciler` (`id`),
  ADD CONSTRAINT `pdr_kayitlari_ibfk_2` FOREIGN KEY (`rehber_user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
