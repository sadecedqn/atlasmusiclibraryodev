<?php
include_once 'vtbaglanti.php';

// ==========================================
// 1. MÜZİK YÜKLEME İŞLEMİ
// ==========================================
if (isset($_POST['muzik_ekle'])) {
    $sarki_adi = $_POST['sarki_adi'];
    $sanatci_adi = $_POST['sanatci_adi'];
    $kategori = $_POST['kategori'];
    $sure = $_POST['sure'];
    $resim_yolu = $_POST['resim_yolu'];

    $muzik_adi = $_FILES['muzik_dosyasi']['name'];
    $muzik_tmp = $_FILES['muzik_dosyasi']['tmp_name'];
    $temiz_isim = preg_replace("/[^a-zA-Z0-9\.]/", "_", basename($muzik_adi));
    $yeni_dosya_adi = time() . "_" . $temiz_isim; 
    $hedef_klasor = "uploads/muzikler/";
    
    if (!file_exists($hedef_klasor)) { mkdir($hedef_klasor, 0777, true); }
    $hedef_yol = $hedef_klasor . $yeni_dosya_adi;

    if(move_uploaded_file($muzik_tmp, $hedef_yol)) {
        $sorgu = $db->prepare("INSERT INTO muzikler (sarki_adi, sanatci_adi, kategori, sure, resim_yolu, muzik_dosyasi) VALUES (?, ?, ?, ?, ?, ?)");
        $sorgu->execute([$sarki_adi, $sanatci_adi, $kategori, $sure, $resim_yolu, $hedef_yol]);
        echo "<script>alert('Müzik başarıyla yüklendi!'); window.location.href='admin.php';</script>";
    }
}

// ==========================================
// 2. MÜZİK SİLME İŞLEMİ
// ==========================================
if (isset($_GET['sil'])) {
    $id = $_GET['sil'];
    $bul = $db->prepare("SELECT muzik_dosyasi FROM muzikler WHERE id = ?");
    $bul->execute([$id]);
    $dosya = $bul->fetch(PDO::FETCH_ASSOC);
    if($dosya && file_exists($dosya['muzik_dosyasi'])){ unlink($dosya['muzik_dosyasi']); }
    $sorgu = $db->prepare("DELETE FROM muzikler WHERE id = ?");
    $sorgu->execute([$id]);
    echo "<script>alert('Müzik başarıyla silindi!'); window.location.href='admin.php';</script>";
}

// ==========================================
// 3. MÜZİK GÜNCELLEME İŞLEMİ
// ==========================================
if (isset($_POST['muzik_guncelle'])) {
    $id = $_POST['sarki_id'];
    $sarki_adi = $_POST['sarki_adi'];
    $sanatci_adi = $_POST['sanatci_adi'];
    $kategori = $_POST['kategori'];
    $sure = $_POST['sure'];
    $resim_yolu = $_POST['resim_yolu'];

    $mevcut_sorgu = $db->prepare("SELECT muzik_dosyasi FROM muzikler WHERE id = ?");
    $mevcut_sorgu->execute([$id]);
    $mevcut_veriler = $mevcut_sorgu->fetch(PDO::FETCH_ASSOC);
    $final_dosya_yolu = $mevcut_veriler['muzik_dosyasi'];

    if ($_FILES['muzik_dosyasi']['size'] > 0) {
        $muzik_adi = $_FILES['muzik_dosyasi']['name'];
        $muzik_tmp = $_FILES['muzik_dosyasi']['tmp_name'];
        $yeni_dosya_adi = time() . "_" . preg_replace("/[^a-zA-Z0-9\.]/", "_", basename($muzik_adi));
        $hedef_yol = "uploads/muzikler/" . $yeni_dosya_adi;

        if (move_uploaded_file($muzik_tmp, $hedef_yol)) {
            if (file_exists($final_dosya_yolu)) { unlink($final_dosya_yolu); }
            $final_dosya_yolu = $hedef_yol;
        }
    }

    $guncelle = $db->prepare("UPDATE muzikler SET sarki_adi=?, sanatci_adi=?, kategori=?, sure=?, resim_yolu=?, muzik_dosyasi=? WHERE id=?");
    if($guncelle->execute([$sarki_adi, $sanatci_adi, $kategori, $sure, $resim_yolu, $final_dosya_yolu, $id])) {
        echo "<script>alert('Müzik başarıyla güncellendi!'); window.location.href='admin.php';</script>"; 
    }
}

// ==========================================
// 4. SİTE AYARLARINI GÜNCELLEME
// ==========================================
if (isset($_POST['ayarlari_guncelle'])) {
    $data = [
        $_POST['site_baslik'], $_POST['tema_renk'], $_POST['alt_baslik'],
        $_POST['kategori_baslik'],
        $_POST['modal_giris_baslik'], $_POST['modal_kadi_placeholder'],
        $_POST['modal_sifre_placeholder'], $_POST['modal_giris_btn'],
        $_POST['modal_kayit_baslik'], $_POST['modal_kayit_btn']
    ];

    $sql = "UPDATE ayarlar SET site_baslik=?, tema_renk=?, alt_baslik=?, kategori_baslik=?, 
            modal_giris_baslik=?, modal_kadi_placeholder=?, modal_sifre_placeholder=?, 
            modal_giris_btn=?, modal_kayit_baslik=?, modal_kayit_btn=? WHERE id=1";
    
    $guncelle = $db->prepare($sql);
    if ($guncelle->execute($data)) {
        echo "<script>alert('Ayarlar başarıyla güncellendi!'); window.location.href='admin.php';</script>";
    }
}

// ==========================================
// 5. YENİ MENÜ EKLE, SİL, GÜNCELLE, SIRALA
// ==========================================
if (isset($_POST['menu_ekle'])) {
    $menu_adi = $_POST['menu_adi'];
    $menu_link = $_POST['menu_link'];
    $ekle = $db->prepare("INSERT INTO menuler (menu_adi, menu_link, sira) VALUES (?, ?, 99)");
    if($ekle->execute([$menu_adi, $menu_link])){
        echo "<script>alert('Menü eklendi!'); window.location.href='admin.php';</script>";
    }
}

if (isset($_GET['menu_sil'])) {
    $id = $_GET['menu_sil'];
    $sil = $db->prepare("DELETE FROM menuler WHERE id = ?");
    if($sil->execute([$id])){
        echo "<script>alert('Menü silindi!'); window.location.href='admin.php';</script>";
    }
}

if (isset($_POST['menu_guncelle'])) {
    $id = $_POST['menu_id'];
    $menu_adi = $_POST['menu_adi'];
    $menu_link = $_POST['menu_link'];
    $guncelle = $db->prepare("UPDATE menuler SET menu_adi = ?, menu_link = ? WHERE id = ?");
    if($guncelle->execute([$menu_adi, $menu_link, $id])){
        echo "<script>alert('Menü başarıyla güncellendi!'); window.location.href='admin.php';</script>";
    }
}

if (isset($_POST['menu_sirala'])) {
    $veri = json_decode($_POST['veri'], true);
    foreach($veri as $item) {
        $guncelle = $db->prepare("UPDATE menuler SET sira = ? WHERE id = ?");
        $guncelle->execute([$item['sira'], $item['id']]);
    }
    exit;
}

// ==========================================
// 6. KATEGORİ EKLE, SİL, GÜNCELLE
// ==========================================
if (isset($_POST['kategori_ekle'])) {
    $kategori_adi = $_POST['kategori_adi'];
    $ekle = $db->prepare("INSERT INTO muzik_kategori (kategori_adi) VALUES (?)");
    if($ekle->execute([$kategori_adi])){
        echo "<script>alert('Kategori başarıyla eklendi!'); window.location.href='admin.php';</script>";
    }
}

if (isset($_GET['kategori_sil'])) {
    $id = $_GET['kategori_sil'];
    $sil = $db->prepare("DELETE FROM muzik_kategori WHERE id = ?");
    if($sil->execute([$id])){
        echo "<script>alert('Kategori başarıyla silindi!'); window.location.href='admin.php';</script>";
    }
}

if (isset($_POST['kategori_guncelle'])) {
    $id = $_POST['kategori_id'];
    $yeni_kategori_adi = $_POST['kategori_adi'];
    
    // 1. Önce eski kategori adını bul (Şarkıları da güncellemek için)
    $eski_sorgu = $db->prepare("SELECT kategori_adi FROM muzik_kategori WHERE id = ?");
    $eski_sorgu->execute([$id]);
    $eski_kat = $eski_sorgu->fetch(PDO::FETCH_ASSOC);
    
    // 2. Kategorinin kendisini güncelle
    $guncelle = $db->prepare("UPDATE muzik_kategori SET kategori_adi = ? WHERE id = ?");
    if($guncelle->execute([$yeni_kategori_adi, $id])){
        
        // 3. (AKILLI SİSTEM) Eski kategoriye sahip tüm müziklerin kategorisini yeni adla değiştir
        if ($eski_kat) {
            $muzikleri_guncelle = $db->prepare("UPDATE muzikler SET kategori = ? WHERE kategori = ?");
            $muzikleri_guncelle->execute([$yeni_kategori_adi, $eski_kat['kategori_adi']]);
        }
        
        echo "<script>alert('Kategori ve bağlı müzikler başarıyla güncellendi!'); window.location.href='admin.php';</script>";
    }
}

// ==========================================
// 6. KATEGORİ EKLE, SİL, GÜNCELLE (RESİM YÜKLEME DESTEKLİ)
// ==========================================
if (isset($_POST['kategori_ekle'])) {
    $kategori_adi = $_POST['kategori_adi'];
    $final_resim = $_POST['kategori_resim']; // Başlangıçta URL'yi alalım

    // Eğer bir dosya yüklenmişse
    if (isset($_FILES['kat_dosya']) && $_FILES['kat_dosya']['size'] > 0) {
        $klasor = "uploads/kategori_resim/";
        if (!file_exists($klasor)) { mkdir($klasor, 0777, true); }
        
        $dosya_adi = time() . "_" . preg_replace("/[^a-zA-Z0-9\.]/", "_", basename($_FILES['kat_dosya']['name']));
        $hedef = $klasor . $dosya_adi;

        if (move_uploaded_file($_FILES['kat_dosya']['tmp_name'], $hedef)) {
            $final_resim = $hedef; // Dosya yüklendiyse artık yolumuz bu
        }
    }

    $ekle = $db->prepare("INSERT INTO muzik_kategori (kategori_adi, kategori_resim) VALUES (?, ?)");
    if($ekle->execute([$kategori_adi, $final_resim])){
        echo "<script>alert('Kategori başarıyla eklendi!'); window.location.href='admin.php';</script>";
    }
}

if (isset($_POST['kategori_guncelle'])) {
    $id = $_POST['kategori_id'];
    $yeni_ad = $_POST['kategori_adi'];
    
    // Mevcut verileri çekelim (Eski resmi korumak veya silmek için)
    $eski_sorgu = $db->prepare("SELECT * FROM muzik_kategori WHERE id = ?");
    $eski_sorgu->execute([$id]);
    $eski_veri = $eski_sorgu->fetch(PDO::FETCH_ASSOC);
    
    $final_resim = $_POST['kategori_resim']; // Formdaki URL kutusunu al

    // Yeni dosya seçilmiş mi kontrol et
    if (isset($_FILES['kat_dosya']) && $_FILES['kat_dosya']['size'] > 0) {
        $klasor = "uploads/kategori_resim/";
        if (!file_exists($klasor)) { mkdir($klasor, 0777, true); }
        
        $dosya_adi = time() . "_" . preg_replace("/[^a-zA-Z0-9\.]/", "_", basename($_FILES['kat_dosya']['name']));
        $hedef = $klasor . $dosya_adi;

        if (move_uploaded_file($_FILES['kat_dosya']['tmp_name'], $hedef)) {
            // Eski resim bir yerel dosyaysa onu silelim
            if($eski_veri['kategori_resim'] && file_exists($eski_veri['kategori_resim'])){
                unlink($eski_veri['kategori_resim']);
            }
            $final_resim = $hedef;
        }
    }

    $guncelle = $db->prepare("UPDATE muzik_kategori SET kategori_adi = ?, kategori_resim = ? WHERE id = ?");
    if($guncelle->execute([$yeni_ad, $final_resim, $id])){
        // Bağlı müzikleri de akıllıca güncelle
        if ($eski_veri) {
            $db->prepare("UPDATE muzikler SET kategori = ? WHERE kategori = ?")->execute([$yeni_ad, $eski_veri['kategori_adi']]);
        }
        echo "<script>alert('Kategori ve bağlı müzikler güncellendi!'); window.location.href='admin.php';</script>";
    }
}

// =========================================================
// KULLANICI YÖNETİM İŞLEMLERİ
// =========================================================

// Kullanıcı Ekleme
if (isset($_POST['kullanici_ekle'])) {
    $k_adi = $_POST['kullanici_adi'];
    $sifre = md5($_POST['sifre']); // Şifreyi MD5 ile şifreliyoruz
    $yetki = $_POST['yetki'];

    // Kullanıcı adı var mı kontrolü
    $kontrol = $db->prepare("SELECT id FROM kullanicilar WHERE kullanici_adi = ?");
    $kontrol->execute([$k_adi]);
    
    if ($kontrol->rowCount() == 0) {
        $ekle = $db->prepare("INSERT INTO kullanicilar (kullanici_adi, sifre, yetki) VALUES (?, ?, ?)");
        $ekle->execute([$k_adi, $sifre, $yetki]);
    } else {
        echo "<script>alert('Bu kullanıcı adı zaten mevcut!'); window.location.href='admin.php';</script>";
        exit;
    }
    header("Location: admin.php");
    exit;
}

// Kullanıcı Güncelleme
if (isset($_POST['kullanici_guncelle'])) {
    $id = $_POST['kullanici_id'];
    $k_adi = $_POST['kullanici_adi'];
    $yetki = $_POST['yetki'];

    // Şifre alanı doluysa şifreyi de güncelle, boşsa sadece diğerlerini güncelle
    if (!empty($_POST['sifre'])) {
        $sifre = md5($_POST['sifre']);
        $guncelle = $db->prepare("UPDATE kullanicilar SET kullanici_adi = ?, sifre = ?, yetki = ? WHERE id = ?");
        $guncelle->execute([$k_adi, $sifre, $yetki, $id]);
    } else {
        $guncelle = $db->prepare("UPDATE kullanicilar SET kullanici_adi = ?, yetki = ? WHERE id = ?");
        $guncelle->execute([$k_adi, $yetki, $id]);
    }
    header("Location: admin.php");
    exit;
}

// Kullanıcı Silme
if (isset($_GET['kullanici_sil'])) {
    $id = $_GET['kullanici_sil'];
    $sil = $db->prepare("DELETE FROM kullanicilar WHERE id = ?");
    $sil->execute([$id]);
    header("Location: admin.php");
    exit;
}

?>
