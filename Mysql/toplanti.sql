-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 05 Kas 2021, 16:06:39
-- Sunucu sürümü: 10.4.19-MariaDB
-- PHP Sürümü: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `toplanti`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `genel_ayar`
--

CREATE TABLE `genel_ayar` (
  `id` int(11) NOT NULL,
  `baslangic_saati` time NOT NULL,
  `bitis_saati` time NOT NULL,
  `hazirlik_suresi` time NOT NULL,
  `toplanti_suresi` time NOT NULL,
  `salon_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `genel_ayar`
--

INSERT INTO `genel_ayar` (`id`, `baslangic_saati`, `bitis_saati`, `hazirlik_suresi`, `toplanti_suresi`, `salon_id`) VALUES
(1, '08:00:00', '18:00:00', '00:05:00', '00:20:00', 1),
(2, '08:00:00', '18:00:00', '00:15:00', '00:40:00', 2),
(3, '08:00:00', '18:00:00', '00:05:00', '00:20:00', 3),
(5, '08:00:00', '18:00:00', '00:00:00', '00:20:00', 4),
(6, '08:00:00', '18:00:00', '00:05:00', '00:10:00', 7),
(7, '08:00:00', '19:00:00', '00:02:00', '00:40:00', 13);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `randevu_bilgileri`
--

CREATE TABLE `randevu_bilgileri` (
  `id` int(11) NOT NULL,
  `personel` text COLLATE utf8mb4_turkish_ci NOT NULL,
  `bolum` text COLLATE utf8mb4_turkish_ci NOT NULL,
  `gorev` text COLLATE utf8mb4_turkish_ci NOT NULL,
  `baslangic_saati` time NOT NULL,
  `tarih` date NOT NULL,
  `toplanti_salonu` int(11) NOT NULL,
  `aciklama` text COLLATE utf8mb4_turkish_ci NOT NULL,
  `toplantiTipi` text COLLATE utf8mb4_turkish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `randevu_bilgileri`
--

INSERT INTO `randevu_bilgileri` (`id`, `personel`, `bolum`, `gorev`, `baslangic_saati`, `tarih`, `toplanti_salonu`, `aciklama`, `toplantiTipi`) VALUES
(21, '', '', '', '00:00:00', '2021-10-25', 0, '', ''),
(26, 'Rıza Alper Çöğürcü', 'Yazılım', 'Yazılım Uzmanı', '08:00:00', '2021-11-05', 1, 'ABC Birimi Yöneticisi ile Proje Durum Değerlendirmesi', 'Personel İle');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `toplanti_salonlari`
--

CREATE TABLE `toplanti_salonlari` (
  `id` int(11) NOT NULL,
  `adi` text COLLATE utf8mb4_turkish_ci NOT NULL,
  `aciklama` text COLLATE utf8mb4_turkish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Tablo döküm verisi `toplanti_salonlari`
--

INSERT INTO `toplanti_salonlari` (`id`, `adi`, `aciklama`) VALUES
(1, 'Aziz Sancar', 'Zemin Kat - Tedarikçi Toplantı Odası'),
(3, 'El Cezeri', 'İdari Bina, Kat 1. Genel Müdür odası yanı'),
(8, 'Mustafa Paşa', 'İdari Bina, Kat 1 Merdivenlerin Karşısı'),
(9, 'El-Harezmi', 'Zemin Kat, Kafeterya Yanı'),
(12, 'Tasarım Toplantı Salonu', 'Ar-Ge Girişi Turnike Karşısı');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `genel_ayar`
--
ALTER TABLE `genel_ayar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `randevu_bilgileri`
--
ALTER TABLE `randevu_bilgileri`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `toplanti_salonlari`
--
ALTER TABLE `toplanti_salonlari`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `genel_ayar`
--
ALTER TABLE `genel_ayar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `randevu_bilgileri`
--
ALTER TABLE `randevu_bilgileri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Tablo için AUTO_INCREMENT değeri `toplanti_salonlari`
--
ALTER TABLE `toplanti_salonlari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
