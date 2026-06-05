# 🎵 Atlas Music Library

Müzik severlerin farklı kategorilerdeki parçaları keşfedebileceği, arka planda kesintisiz müzik deneyimi yaşayabileceği, modern ve interaktif bir arayüze sahip dijital müzik portalıdır.

## 📌 Proje Açıklaması

Bu proje, kullanıcıların standart bir web sitesinde gezinir gibi değil, profesyonel bir müzik uygulamasında vakit geçiriyor gibi hissetmeleri için tasarlandı. Sayfa değişimlerinde asla kesilmeyen arka plan müziği ve tamamen yönetilebilir dinamik bir altyapı sunar.

## 👥 Hedef Kullanıcılar

| Kullanıcı Grubu | Odak Noktası |
| :--- | :--- |
| **🎧 Müzik Tutkunları** | Farklı kategorilerde yeni müzikler keşfeden ve kesintisiz arka plan dinleme deneyimi arayanlar. |
| **🛠️ Yöneticiler (Admin)** | Kütüphaneye yeni albümler/şarkılar ekleyen, site temalarını ve kullanıcıları yöneten yetkililer. |

## 🚀 Temel Özellikler

* **🎧 Kesintisiz Dinleme:** Tarayıcı hafızası (SessionStorage) ile sayfalar arası geçişte şarkının kaldığı saniyeden devam etmesi.
* **🎨 Dinamik Tema Yönetimi:** Admin panelinden tek tıkla tüm sitenin renginin (CSS Variables) değiştirilebilmesi.
* **📱 Sürüklenebilir Widget:** Ekranın istenilen noktasına taşınabilen, akıllı ve mobil uyumlu mini oynatıcı kutucuğu.
* **⚙️ Kapsamlı Yönetim Paneli:** Müzik, sanatçı, kategori, menü ve kullanıcı yetkilerinin kolayca yönetildiği kontrol merkezi.

## 📂 Dosya Yapısı

Projenin kök dizini (`atlasmusiclibrary`) ve alt klasör mimarisi aşağıdaki gibidir:

```text
📦 atlasmusiclibrary
 ┣ 📂 assets
 ┃ ┣ 📜 script.js           # Oynatıcı, kuyruk sistemi ve AJAX işlemleri
 ┃ ┗ 📜 style.css           # Sitenin genel ve dinamik stilleri
 ┣ 📂 sql
 ┃ ┗ 📜 muzik_kutuphanesi.sql # Veritabanı tabloları ve varsayılan veriler
 ┣ 📂 uploads
 ┃ ┣ 📂 kategori_resim      # Kategorilere ait kapak görselleri
 ┃ ┗ 📂 muzikler            # Sisteme yüklenen .mp3 ses dosyaları
 ┣ 📜 admin.php             # Yönetim paneli arayüzü
 ┣ 📜 anasayfa.php          # Kullanıcıların karşılandığı ana vitrin ve katalog
 ┣ 📜 cikis.php             # Oturum sonlandırma işlemleri
 ┣ 📜 giris.php             # Kullanıcı giriş ekranı
 ┣ 📜 header.php            # Sayfa iskeleti
 ┣ 📜 index.php             # Anasayfaya yönlendirici kök dosya
 ┣ 📜 islem.php             # Formların (Ekle/Sil/Güncelle) işlendiği arka plan motoru
 ┣ 📜 kayit.php             # Yeni kullanıcı kayıt ekranı
 ┗ 📜 vtbaglanti.php        # Veritabanı bağlantı ayarları
