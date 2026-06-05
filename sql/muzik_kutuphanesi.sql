SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


--
-- Veritabanı: `if0_41869263_atlasmusiclibraryodev`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ayarlar`
--

CREATE TABLE `ayarlar` (
  `id` int(11) NOT NULL,
  `site_baslik` varchar(255) DEFAULT NULL,
  `tema_renk` varchar(7) DEFAULT NULL,
  `footer_metni` text DEFAULT NULL,
  `menu_anasayfa` varchar(50) DEFAULT 'ANA SAYFA',
  `menu_kategoriler` varchar(50) DEFAULT 'KATEGORİLER',
  `menu_admin` varchar(50) DEFAULT 'ADMİN',
  `alt_baslik` varchar(100) DEFAULT 'Diğer Müzikler',
  `modal_giris_baslik` varchar(100) DEFAULT 'SİSTEME GİRİŞ',
  `modal_kadi_placeholder` varchar(100) DEFAULT 'Kullanıcı Adı',
  `modal_sifre_placeholder` varchar(100) DEFAULT 'Şifre',
  `modal_giris_btn` varchar(50) DEFAULT 'GİRİŞ YAP',
  `modal_kayit_baslik` varchar(100) DEFAULT 'KÜTÜPHANEYE KATIL',
  `modal_kayit_btn` varchar(50) DEFAULT 'KAYIT OL',
  `kategori_baslik` varchar(100) DEFAULT 'MÜZİK KATEGORİLERİ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `ayarlar`
--

INSERT INTO `ayarlar` (`id`, `site_baslik`, `tema_renk`, `footer_metni`, `menu_anasayfa`, `menu_kategoriler`, `menu_admin`, `alt_baslik`, `modal_giris_baslik`, `modal_kadi_placeholder`, `modal_sifre_placeholder`, `modal_giris_btn`, `modal_kayit_baslik`, `modal_kayit_btn`, `kategori_baslik`) VALUES
(1, 'Atlas Music Library', '#ffffff', '', 'ANA SAYFA', 'KATEGORİLER', 'ADMİN', 'ÖNE ÇIKANLAR', 'KÜTÜPHANEYE GİRİŞ YAP', 'Kullanıcı Adı', 'Şifre', NULL, NULL, NULL, 'KATEGORİLER');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `id` int(11) NOT NULL,
  `kullanici_adi` varchar(50) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `yetki` enum('admin','uye') DEFAULT 'uye',
  `kayit_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`id`, `kullanici_adi`, `sifre`, `yetki`, `kayit_tarihi`) VALUES
(1, 'admin_mert', 'e10adc3949ba59abbe56e057f20f883e', 'admin', '2026-04-29 09:46:40'),
(3, 'mertt', '202cb962ac59075b964b07152d234b70', 'uye', '2026-04-29 09:52:09'),
(5, 'admin1', '202cb962ac59075b964b07152d234b70', 'admin', '2026-06-03 13:03:41');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `menuler`
--

CREATE TABLE `menuler` (
  `id` int(11) NOT NULL,
  `menu_adi` varchar(100) DEFAULT NULL,
  `menu_link` varchar(255) DEFAULT NULL,
  `sira` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `menuler`
--

INSERT INTO `menuler` (`id`, `menu_adi`, `menu_link`, `sira`) VALUES
(1, 'ANA SAYFA', 'anasayfa.php', 0),
(3, 'ADMİN', 'admin.php', 2),
(9, 'KATEGORİLER', 'anasayfa.php#kategoriler', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzikler`
--

CREATE TABLE `muzikler` (
  `id` int(11) NOT NULL,
  `sarki_adi` varchar(255) DEFAULT NULL,
  `sanatci_adi` varchar(255) DEFAULT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `sure` varchar(50) DEFAULT NULL,
  `muzik_dosyasi` varchar(255) DEFAULT NULL,
  `resim_yolu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `muzikler`
--

INSERT INTO `muzikler` (`id`, `sarki_adi`, `sanatci_adi`, `kategori`, `sure`, `muzik_dosyasi`, `resim_yolu`) VALUES
(6, 'Çözüm Sendin', 'İnsan Mıyız', 'TÜRKÇE POP', '03:12', 'uploads/muzikler/1777448901___nsan_M__y__z_______z__m_Sendin__10C6naAn4LY_.mp3', 'https://cdn-images.dzcdn.net/images/cover/00f3241862910b49f391ecf58b64a195/0x1900-000000-80-0-0.jpg'),
(7, 'Aşktı Bu', 'Redd', 'TÜRKÇE POP', '03:23', 'uploads/muzikler/1777448971_Askti_Bu__Zc1iflvSehE_.mp3', 'https://i.scdn.co/image/ab67616d0000b273c53fb79c2bf2c7c6c513c0d7'),
(8, 'blinding lights arabic', '@rachidaseyakhe', 'Arabesk / Fantezi', '04:27', 'uploads/muzikler/1777450043_audio_track.mp3', 'https://i.ytimg.com/vi/MCW4eGoLX3s/hq720.jpg?sqp=-oaymwFBCNAFEJQDSFryq4qpAzMIARUAAIhCGAHYAQHiAQoIGBACGAY4AUAB8AEB-AHOBYAC0AWKAgwIABABGGUgXihSMA8=&rs=AOn4CLAlq3EcSjHyMFgaSt57osRCaXDZ-Q'),
(9, 'gelemez', 'EGE!', 'TÜRKÇE RAP / HIP-HOP', '02:33', 'uploads/muzikler/1777450184_EGE___gelemez_Official_Music_Video.mp3', 'https://i.ytimg.com/vi/v3XMOqlO9x0/maxresdefault.jpg'),
(10, 'yenildiğim tek savaştın', 'ASLAR - Topic', 'TÜRKÇE RAP / HIP-HOP', '01:50', 'uploads/muzikler/1777450418_yenildi__im_tek_sava__t__n.mp3', 'https://i.ytimg.com/vi/K2-QZ-hEU9Y/hq720.jpg?sqp=-oaymwEnCNAFEJQDSFryq4qpAxkIARUAAIhCGAHYAQHiAQoIGBACGAY4AUAB&rs=AOn4CLBJcdGUWFm2cjdPe1usP4LY_unoOw'),
(11, 'beni al', 'Ankara Echoes', 'TÜRKÇE POP', '02:50', 'uploads/muzikler/1777451878_Ankara_Echoes___Beni_Al_sans__rs__z_ta_ki_seni_g__rene_kadar.mp3', 'https://i.ytimg.com/vi/JFmjllY_ng8/hq720.jpg?sqp=-oaymwFBCNAFEJQDSFryq4qpAzMIARUAAIhCGAHYAQHiAQoIGBACGAY4AUAB8AEB-AH-CYAC0AWKAgwIABABGBQgWShyMA8=&rs=AOn4CLAtlDxiu_LMcK_RIYn-iuLHdxF3yA'),
(12, 'Olabilirdik', 'Dolu Kadehi Ters Tut & Selin', 'MERT KATEGORİSİ', '03:48', 'uploads/muzikler/1777463877_Dolu_Kadehi_Ters_Tut___Selin___Olabilirdik__Official_Video____hLBQEWNQeM_.mp3', 'https://i.ytimg.com/vi/_hLBQEWNQeM/hq720.jpg?sqp=-oaymwEnCNAFEJQDSFryq4qpAxkIARUAAIhCGAHYAQHiAQoIGBACGAY4AUAB&rs=AOn4CLBqkrd4_cD59zmTb56Pv7eog_YFBg'),
(13, 'Bugün Herkes Ölsün İstedim', 'Redd', 'DARK TECHNO', '04:12', 'uploads/muzikler/1778587068_1778583594_Bug__n_Herkes___ls__n___stedim__onXOEPyItNo_.mp3', 'https://i.ytimg.com/vi/onXOEPyItNo/hq720.jpg?sqp=-oaymwEnCNAFEJQDSFryq4qpAxkIARUAAIhCGAHYAQHiAQoIGBACGAY4AUAB&rs=AOn4CLClrmFX-uz6o39L0g9zrSh7F3VOVw'),
(14, 'Onlar Bile Üzülürler', 'Redd', 'Arabesk / Fantezi', '02:56', 'uploads/muzikler/1778587045_1777483492_Onlar_Bile___z__l__rler__5ie_1LkrdzM_.mp3', 'https://i.ytimg.com/vi/5ie-1LkrdzM/hq720.jpg?sqp=-oaymwEnCNAFEJQDSFryq4qpAxkIARUAAIhCGAHYAQHiAQoIGBACGAY4AUAB&rs=AOn4CLBAqaEu8z3d2ybTRWDg5vloE-3nlg'),
(15, 'Aşk Virüs', 'Redd', 'DARK TECHNO', '02:18', 'uploads/muzikler/1778587029_1778583660_REDD___A__k_Vir__s__RTYPvxeIy9E_.mp3', 'https://i.ytimg.com/vi/RTYPvxeIy9E/hq720.jpg?sqp=-oaymwEnCNAFEJQDSFryq4qpAxkIARUAAIhCGAHYAQHiAQoIGBACGAY4AUAB&rs=AOn4CLC15ExxQYeoYMo_QeFGMM0MmrWyqw'),
(16, 'Boşlukta Dans', 'Redd', 'Karadeniz Ezgileri', '04:12', 'uploads/muzikler/1778587002_1778583678_Redd___Bo__lukta_Dans__ZJXi0RN_tXo_.mp3', 'https://i.ytimg.com/vi/ZJXi0RN_tXo/hq720.jpg?sqp=-oaymwEnCNAFEJQDSFryq4qpAxkIARUAAIhCGAHYAQHiAQoIGBACGAY4AUAB&rs=AOn4CLBU0Mbbdzut1cHMbclTqPn-m0Nw5A'),
(17, 'Hala Seni Çok Özlüyorum', 'Redd', 'TÜRKÇE POP', '03:45', 'uploads/muzikler/1778586567_1778583696_Redd___Hala_Seni___ok___zl__yorum__i2v_Q5WdggE_.mp3', 'https://i.ytimg.com/vi/i2v_Q5WdggE/hq720.jpg?sqp=-oaymwEnCNAFEJQDSFryq4qpAxkIARUAAIhCGAHYAQHiAQoIGBACGAY4AUAB&rs=AOn4CLC1fMNG_ZO2O1A6xm9wKEVErE1X4A'),
(18, 'İtiraf', 'Redd', 'PHONK', '04:12', 'uploads/muzikler/1778586522_1778583731_Redd_____tiraf__g1ZKg7xArog_.mp3', 'https://i.ytimg.com/vi/g1ZKg7xArog/hqdefault.jpg?sqp=-oaymwEnCOADEI4CSFryq4qpAxkIARUAAIhCGAHYAQHiAQoIGBACGAY4AUAB&rs=AOn4CLCzJ0MRedCANsi8wgXxEGCzuEJ0rQ');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `muzik_kategori`
--

CREATE TABLE `muzik_kategori` (
  `id` int(11) NOT NULL,
  `kategori_adi` varchar(100) DEFAULT NULL,
  `kategori_resim` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `muzik_kategori`
--

INSERT INTO `muzik_kategori` (`id`, `kategori_adi`, `kategori_resim`) VALUES
(1, 'TECHNO / RAVE', 'uploads/kategori_resim/1777465909_techno_rave.png'),
(2, 'PHONK', 'uploads/kategori_resim/1777465851_phonk.png'),
(3, 'AMBIENT', 'uploads/kategori_resim/1777465339_ambient.jpg'),
(4, 'DEEP BASS', 'uploads/kategori_resim/1777465622_deep_bass.png'),
(5, 'DARK TECHNO', 'uploads/kategori_resim/1777465550_dark_techno.png'),
(6, 'TÜRKÇE POP', 'uploads/kategori_resim/1777466076_turcepop.png'),
(7, 'Arabesk / Fantezi', 'uploads/kategori_resim/1777465488_arabesk_fantazi.png'),
(8, 'TÜRKÇE RAP / HIP-HOP', 'uploads/kategori_resim/1777466110_turkcraph__phop.png'),
(9, 'Anadolu Rock', 'uploads/kategori_resim/1777465409_anadolu_rock.png'),
(10, 'TÜRK HALK MÜZİĞİ (THM)', 'uploads/kategori_resim/1777465970_turkhalk.png'),
(11, 'TÜRK SANAT MÜZİĞİ (TSM)', 'uploads/kategori_resim/1777466032_t__rksanat.png'),
(12, 'ALTERNATİF / BAĞIMSIZ', 'uploads/kategori_resim/1777465310_alternatif_ba____ms__z.png'),
(13, 'Elektronik / Deep House', 'uploads/kategori_resim/1777465685_elektronik_deephouse.png'),
(14, 'Karadeniz Ezgileri', 'uploads/kategori_resim/1777465795_karadeniz.png'),
(15, 'ENSTRÜMANTAL / FON', 'uploads/kategori_resim/1777465749_enstr__mental_fon.png');



--
-- Tablo için indeksler `ayarlar`
--
ALTER TABLE `ayarlar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `menuler`
--
ALTER TABLE `menuler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `muzikler`
--
ALTER TABLE `muzikler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `muzik_kategori`
--
ALTER TABLE `muzik_kategori`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `ayarlar`
--
ALTER TABLE `ayarlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `menuler`
--
ALTER TABLE `menuler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Tablo için AUTO_INCREMENT değeri `muzikler`
--
ALTER TABLE `muzikler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Tablo için AUTO_INCREMENT değeri `muzik_kategori`
--
ALTER TABLE `muzik_kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;