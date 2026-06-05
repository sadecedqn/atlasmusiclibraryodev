<?php
session_start();
include_once 'vtbaglanti.php';

$hata = "";

// Form gönderildiğinde çalışacak kısım
if (isset($_POST['kayit_ol'])) {
    $kadi = $_POST['kullanici_adi'];
    $sifre = md5($_POST['sifre']); // Şifreyi MD5 ile güvenli hale getiriyoruz

    // Kullanıcı adı daha önce alınmış mı kontrol edelim
    $kontrol = $db->prepare("SELECT * FROM kullanicilar WHERE kullanici_adi = ?");
    $kontrol->execute([$kadi]);
    
    if ($kontrol->rowCount() > 0) {
        $hata = "Bu kullanıcı adı zaten alınmış! Lütfen başka bir tane seçin.";
    } else {
        // Alınmamışsa veritabanına ekle (Varsayılan yetki: uye)
        $ekle = $db->prepare("INSERT INTO kullanicilar (kullanici_adi, sifre, yetki) VALUES (?, ?, 'uye')");
        if ($ekle->execute([$kadi, $sifre])) {
            echo "<script>alert('Kayıt başarılı! Şimdi giriş yapabilirsiniz.'); window.location.href='giris.php';</script>";
            exit;
        } else {
            $hata = "Kayıt sırasında sistemsel bir hata oluştu!";
        }
    }
}

// Temanın rengini çekelim ki bu sayfa da sitemize uyumlu parlasın
$ayar_sorgu = $db->query("SELECT * FROM ayarlar WHERE id = 1");
$ayar = $ayar_sorgu->fetch(PDO::FETCH_ASSOC);
if(!$ayar) { $ayar = ['site_baslik' => 'Atlas Music', 'tema_renk' => '#ffffff']; }
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol - <?php echo $ayar['site_baslik']; ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        :root { --tema-renk: <?php echo $ayar['tema_renk']; ?>; }
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background-image: radial-gradient(circle at top, color-mix(in srgb, var(--tema-renk) 15%, transparent), #121212 80%); }
        .giris-kart { background: linear-gradient(135deg, rgba(30,30,30,0.9) 0%, rgba(15,15,15,0.95) 100%); backdrop-filter: blur(20px); padding: 40px; border-radius: 25px; border: 1px solid color-mix(in srgb, var(--tema-renk) 30%, #333); width: 100%; max-width: 400px; box-shadow: 0 10px 40px rgba(0,0,0,0.8); text-align: center; }
        .giris-input { background: #111; border: 1px solid #333; padding: 15px; border-radius: 12px; color: #fff; width: 100%; box-sizing: border-box; margin-bottom: 20px; transition: 0.3s; }
        .giris-input:focus { border-color: var(--tema-renk); outline: none; box-shadow: 0 0 15px color-mix(in srgb, var(--tema-renk) 50%, transparent); }
    </style>
</head>
<body>

    <div class="giris-kart">
        <h2 class="form-baslik" style="color: var(--tema-renk); text-shadow: 0 0 15px color-mix(in srgb, var(--tema-renk) 50%, transparent);">KÜTÜPHANEYE KATIL</h2>
        
        <?php if($hata != ""): ?>
            <div style="background: #ff4d4d; color: #fff; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">
                <?php echo $hata; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="text" name="kullanici_adi" class="giris-input" placeholder="Kullanıcı Adı" required>
            <input type="password" name="sifre" class="giris-input" placeholder="Şifre" required>
            <button type="submit" name="kayit_ol" class="guncelle-btn" style="width: 100%;">KAYIT OL</button>
        </form>
        
        <div style="margin-top: 20px; font-size: 13px; color: #888;">
            Zaten üye misin? <a href="giris.php" style="color: var(--tema-renk); text-decoration: none; font-weight: bold;">Giriş Yap</a>
        </div>
        <div style="margin-top: 10px; font-size: 13px;">
            <a href="anasayfa.php" style="color: #666; text-decoration: none;">Ana Sayfaya Dön</a>
        </div>
    </div>

</body>
</html>