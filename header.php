<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once 'vtbaglanti.php';

if (isset($_POST['modal_giris'])) {
    $kadi = $_POST['kullanici_adi']; $sifre = md5($_POST['sifre']);
    $sorgu = $db->prepare("SELECT * FROM kullanicilar WHERE kullanici_adi = ? AND sifre = ?");
    $sorgu->execute([$kadi, $sifre]); $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);
    if ($kullanici) { $_SESSION['kullanici'] = $kullanici['kullanici_adi']; $_SESSION['yetki'] = $kullanici['yetki']; header("Location: ".$_SERVER['PHP_SELF']); exit; } 
    else { echo "<script>alert('Hatalı giriş!');</script>"; }
}

if (isset($_POST['modal_kayit'])) {
    $kadi = $_POST['kullanici_adi']; $sifre = md5($_POST['sifre']);
    $kontrol = $db->prepare("SELECT * FROM kullanicilar WHERE kullanici_adi = ?"); $kontrol->execute([$kadi]);
    if ($kontrol->rowCount() == 0) { $ekle = $db->prepare("INSERT INTO kullanicilar (kullanici_adi, sifre, yetki) VALUES (?, ?, 'uye')"); $ekle->execute([$kadi, $sifre]); echo "<script>alert('Kayıt başarılı! Giriş yapabilirsiniz.');</script>"; } 
    else { echo "<script>alert('Bu kullanıcı adı alınmış!');</script>"; }
}

$ayar_sorgu = $db->query("SELECT * FROM ayarlar WHERE id = 1");
$ayar = $ayar_sorgu->fetch(PDO::FETCH_ASSOC);
if(!$ayar) { $ayar = ['site_baslik' => 'Atlas Music', 'tema_renk' => '#ffffff', 'alt_baslik' => 'Diğer Müzikler', 'kategori_baslik' => 'MÜZİK KATEGORİLERİ']; }

/* VİTRİN MÜZİKLERİ (İLK 4) */
$v_sorgu = $db->query("SELECT * FROM muzikler ORDER BY id DESC LIMIT 4");
$vitrin_muzikleri = $v_sorgu->fetchAll(PDO::FETCH_ASSOC);

/* ========================================================= */
/* YENİ: DİNAMİK 8'Lİ SAYFALAMA (PAGINATION) SİSTEMİ         */
/* ========================================================= */
$secilen_kat = isset($_GET['kat']) ? $_GET['kat'] : null;
$sayfa_no = (isset($_GET['s']) && is_numeric($_GET['s'])) ? (int)$_GET['s'] : 1;
if($sayfa_no < 1) $sayfa_no = 1;
$limit = 8; // Ekranda görünecek müzik sayısı
$offset = ($sayfa_no - 1) * $limit;
$url_ek = $secilen_kat ? "&kat=" . urlencode($secilen_kat) : "";

if ($secilen_kat) { 
    $t_sorgu = $db->prepare("SELECT COUNT(*) FROM muzikler WHERE kategori = ?");
    $t_sorgu->execute([$secilen_kat]);
    $toplam_kayit = $t_sorgu->fetchColumn();
    
    $k_sorgu = $db->prepare("SELECT * FROM muzikler WHERE kategori = ? ORDER BY id DESC LIMIT $limit OFFSET $offset"); 
    $k_sorgu->execute([$secilen_kat]); 
} 
else { 
    $t_sorgu = $db->query("SELECT COUNT(*) FROM muzikler");
    $toplam_kayit = $t_sorgu->fetchColumn() - 4; // Vitrindeki 4 kartı toplamdan düşüyoruz
    if($toplam_kayit < 0) $toplam_kayit = 0;
    
    $gercek_offset = $offset + 4; // Vitrini atlayıp okumaya başlıyoruz
    $k_sorgu = $db->query("SELECT * FROM muzikler ORDER BY id DESC LIMIT $limit OFFSET $gercek_offset"); 
}
$katalog_muzikleri = $k_sorgu->fetchAll(PDO::FETCH_ASSOC);
$toplam_sayfa = ceil($toplam_kayit / $limit);
if($toplam_sayfa < 1) $toplam_sayfa = 1;


$tum_muzikler_sorgu = $db->query("SELECT * FROM muzikler ORDER BY id DESC");
$tum_muzikler_dizi = $tum_muzikler_sorgu->fetchAll(PDO::FETCH_ASSOC);

$kat_sorgu = $db->query("SELECT * FROM muzik_kategori ORDER BY kategori_adi ASC");
$tum_kategoriler = $kat_sorgu->fetchAll(PDO::FETCH_ASSOC);

$menu_sorgu = $db->query("SELECT * FROM menuler ORDER BY sira ASC, id ASC");
$dinamik_menuler = $menu_sorgu->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $ayar['site_baslik']; ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <style>:root { --tema-renk: <?php echo $ayar['tema_renk']; ?>; }</style>
</head>
<body>
    <div id="yapiskan-navbar" class="yapiskan-navbar">
        <div class="navbar-icerik">
            <div class="logo-alan" style="color: #fff; font-weight: 800; letter-spacing: 2px; font-size: 18px;"><?php echo strtoupper($ayar['site_baslik']); ?></div>
            <nav class="ust-menu" style="position: relative; top: 0; right: 0;">
                <?php foreach($dinamik_menuler as $menu): ?>
                    <?php if ($menu['menu_link'] == 'admin.php' && (!isset($_SESSION['yetki']) || $_SESSION['yetki'] !== 'admin')) continue; ?>
                    <a href="<?php echo $menu['menu_link']; ?>" class="<?php echo (basename($_SERVER['PHP_SELF']) == $menu['menu_link']) ? 'aktif' : ''; ?>"><?php echo $menu['menu_adi']; ?></a>
                <?php endforeach; ?>
                <?php if (isset($_SESSION['kullanici'])): ?><a href="cikis.php" style="color: #ff4d4d;">ÇIKIŞ</a>
                <?php else: ?><div class="auth-birlesik-kutu"><a href="javascript:void(0)" onclick="modalAc('girisModal')">GİRİŞ YAP</a><a href="javascript:void(0)" onclick="modalAc('kayitModal')">KAYIT OL</a></div><?php endif; ?>
            </nav>
        </div>
    </div>

    <header class="<?php echo (isset($sayfa) && $sayfa == 'admin') ? '' : 'ust-alan'; ?>" style="<?php echo (isset($sayfa) && $sayfa == 'admin') ? 'width: 95%; max-width: 1400px; margin: 20px auto; display: flex; justify-content: flex-end;' : ''; ?>">
        <?php if(!isset($sayfa) || $sayfa != 'admin'): ?>
            <div class="cerceve"></div>
            <div class="ekolayzir"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
        <?php endif; ?>

        <nav class="ust-menu" style="<?php echo (isset($sayfa) && $sayfa == 'admin') ? 'position: relative; top: 0; right: 0;' : ''; ?>">
            <?php foreach($dinamik_menuler as $menu): ?>
                <?php if ($menu['menu_link'] == 'admin.php' && (!isset($_SESSION['yetki']) || $_SESSION['yetki'] !== 'admin')) continue; ?>
                <a href="<?php echo $menu['menu_link']; ?>" class="<?php echo (basename($_SERVER['PHP_SELF']) == $menu['menu_link']) ? 'aktif' : ''; ?>"><?php echo $menu['menu_adi']; ?></a>
            <?php endforeach; ?>
            <?php if (isset($_SESSION['kullanici'])): ?><a href="cikis.php" style="color: #ff4d4d;">ÇIKIŞ (<?php echo $_SESSION['kullanici']; ?>)</a>
            <?php else: ?><div class="auth-birlesik-kutu"><a href="javascript:void(0)" onclick="modalAc('girisModal')">GİRİŞ YAP</a><a href="javascript:void(0)" onclick="modalAc('kayitModal')">KAYIT OL</a></div><?php endif; ?>
        </nav>

        <?php if(!isset($sayfa) || $sayfa != 'admin'): ?>
            <div class="header-kart-dizisi">
                <?php foreach($vitrin_muzikleri as $muzik): ?>
                <div class="kapsayici-3d">
                    <div class="alan tl"></div> <div class="alan t"></div> <div class="alan tr"></div>
                    <div class="alan l"></div> <div class="alan c"></div> <div class="alan r"></div>
                    <div class="alan bl"></div> <div class="alan b"></div> <div class="alan br"></div>
                    <div class="kart-3d">
                        <svg height="300" width="300" class="dis-cerceve bb"><path class="arkaplan-cizgisi" pathLength="360" d="M0 110V70A70 70 135 0170 0H230A70 70 45 01300 70L300 110A40 40 135 01260 150H40A40 40 0 000 190V230A70 70 45 0070 300H230A70 70 135 00300 230V190"></path><path class="animasyonlu-cizgi" pathLength="360" d="M0 110V70A70 70 135 0170 0H230A70 70 45 01300 70L300 110A40 40 135 01260 150H40A40 40 0 000 190V230A70 70 45 0070 300H230A70 70 135 00300 230V190"></path></svg>
                        
                        <div class="ic-kisim"><img src="<?php echo $muzik['resim_yolu']; ?>" class="gercek-resim"></div>
                        
                        <label class="kart-oynat-btn"><input type="checkbox" onclick="muzikBaslat(this, '<?php echo addslashes($muzik['sarki_adi']); ?>', '<?php echo addslashes($muzik['sanatci_adi']); ?>', '<?php echo $muzik['resim_yolu']; ?>', '<?php echo $muzik['muzik_dosyasi']; ?>')"><div class="cizgi cizgi-1"></div><div class="cizgi cizgi-2"></div><div class="cizgi cizgi-3"></div></label>
                        <div class="buyuk-metin"><?php echo $muzik['sarki_adi']; ?></div>
                        <div class="bilgi-metni kategori"><?php echo $muzik['kategori']; ?></div>
                        <div class="bilgi-metni sarki-adi"><?php echo $muzik['sanatci_adi']; ?></div>
                        <div class="bilgi-metni detay"><?php echo $muzik['sure']; ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="muzik-calar-widget <?php echo (isset($sayfa) && $sayfa == 'admin') ? 'suruklenebilir' : ''; ?>" id="muzik-calar-widget" style="display: none;">
            <div class="mc-ust-bilgi">
                <img id="mc-kapak" src="" alt="Kapak">
                <div class="mc-metinler">
                    <div id="mc-sarki-adi" class="mc-isim">Şarkı Adı</div>
                    <div id="mc-sanatci" class="mc-sanatci">Sanatçı</div>
                </div>
                <button id="mc-kapat-btn" onclick="oynaticiKapat()">✕</button>
            </div>
            <div class="mc-ilerleme-alani" id="mc-ilerleme-alani"><div class="mc-ilerleme-cubugu" id="mc-ilerleme-cubugu"></div></div>
            <div class="mc-zamanlar"><span id="mc-gecen-zaman">00:00</span><span id="mc-kalan-zaman">00:00</span></div>
            <div class="mc-ses-alani">
                <span id="mc-ses-ikon" style="cursor: pointer;">
                    <svg id="ikon-ses-acik" viewBox="0 0 24 24" width="18" height="18" fill="#fff"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"></path></svg>
                    <svg id="ikon-ses-kapali" viewBox="0 0 24 24" width="18" height="18" fill="#fff" style="display: none;"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"></path></svg>
                </span>
                <input type="range" id="mc-ses-cubugu" min="0" max="1" step="0.01" value="1" oninput="sesAyarla(this.value)">
            </div>
            <div class="mc-kontroller">
                <button class="mc-btn" onclick="oncekiMuzik()">⏮</button>
                <label class="widget-oynat-btn"><input type="checkbox" id="mc-oynat-duraklat-checkbox" onclick="oynatDuraklatTetikle()"><div class="cizgi cizgi-1"></div><div class="cizgi cizgi-2"></div><div class="cizgi cizgi-3"></div></label>
                <button class="mc-btn" onclick="sonrakiMuzik()">⏭</button>
            </div>
        </div>
        
        <audio id="ses-motoru" src=""></audio>
    </header>

    <?php if(!isset($sayfa) || $sayfa != 'admin'): ?>
        <main class="alt-katalog" id="katalog-bolumu">
            <div id="muzik-kayan-alan">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-bottom: 30px;">
                    <h2 class="katalog-baslik" style="margin:0; border:none;"><?php echo $secilen_kat ? "KATEGORİ: " . strtoupper($secilen_kat) : $ayar['alt_baslik']; ?></h2>
                    <?php if($secilen_kat): ?><a href="anasayfa.php" style="color: var(--tema-renk); text-decoration: none; font-weight: bold; font-size: 13px;">← TÜMÜNÜ GÖSTER</a><?php endif; ?>
                </div>

                <div class="katalog-grid">
                    <?php if(count($katalog_muzikleri) > 0): ?>
                        <?php foreach($katalog_muzikleri as $muzik): ?>
                            <div class="kapsayici-3d">
                                <div class="kart-3d">
                                    <svg height="300" width="300" class="dis-cerceve bb"><path class="arkaplan-cizgisi" pathLength="360" d="M0 110V70A70 70 135 0170 0H230A70 70 45 01300 70L300 110A40 40 135 01260 150H40A40 40 0 000 190V230A70 70 45 0070 300H230A70 70 135 00300 230V190"></path><path class="animasyonlu-cizgi" pathLength="360" d="M0 110V70A70 70 135 0170 0H230A70 70 45 01300 70L300 110A40 40 135 01260 150H40A40 40 0 000 190V230A70 70 45 0070 300H230A70 70 135 00300 230V190"></path></svg>
                                    <div class="ic-kisim"><img src="<?php echo $muzik['resim_yolu']; ?>" class="gercek-resim"></div>
                                    <label class="kart-oynat-btn"><input type="checkbox" onclick="muzikBaslat(this, '<?php echo addslashes($muzik['sarki_adi']); ?>', '<?php echo addslashes($muzik['sanatci_adi']); ?>', '<?php echo $muzik['resim_yolu']; ?>', '<?php echo $muzik['muzik_dosyasi']; ?>')"><div class="cizgi cizgi-1"></div><div class="cizgi cizgi-2"></div><div class="cizgi cizgi-3"></div></label>
                                    <div class="buyuk-metin"><?php echo $muzik['sarki_adi']; ?></div>
                                    <div class="bilgi-metni kategori"><?php echo $muzik['kategori']; ?></div>
                                    <div class="bilgi-metni sarki-adi"><?php echo $muzik['sanatci_adi']; ?></div>
                                    <div class="bilgi-metni detay"><?php echo $muzik['sure']; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #666; width: 100%; text-align: center; padding: 50px 0;">Bu kategoride henüz müzik bulunmuyor.</p>
                    <?php endif; ?>
                </div>
                
                <?php if($toplam_sayfa > 1): ?>
                <div class="sayfalama-alani">
                    <a href="?s=<?php echo $sayfa_no - 1; ?><?php echo $url_ek; ?>" class="sayfa-ok-btn sol <?php echo ($sayfa_no <= 1) ? 'pasif' : ''; ?>">
                        <div class="ok-arkaplan"><svg viewBox="0 0 1024 1024"><path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z"></path><path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z"></path></svg></div>
                        <p>ÖNCEKİ</p>
                    </a>
                    <span class="sayfa-yazisi"><?php echo $sayfa_no; ?>. SAYFA</span>
                    <a href="?s=<?php echo $sayfa_no + 1; ?><?php echo $url_ek; ?>" class="sayfa-ok-btn sag <?php echo ($sayfa_no >= $toplam_sayfa) ? 'pasif' : ''; ?>">
                        <p>SONRAKİ</p>
                        <div class="ok-arkaplan"><svg viewBox="0 0 1024 1024" style="transform: rotate(180deg);"><path d="M224 480h640a32 32 0 1 1 0 64H224a32 32 0 0 1 0-64z"></path><path d="m237.248 512 265.408 265.344a32 32 0 0 1-45.312 45.312l-288-288a32 32 0 0 1 0-45.312l288-288a32 32 0 1 1 45.312 45.312L237.248 512z"></path></svg></div>
                    </a>
                </div>
                <?php endif; ?>
            </div> <h2 class="katalog-baslik" style="margin-top: 80px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px;"><?php echo isset($ayar['kategori_baslik']) ? $ayar['kategori_baslik'] : 'MÜZİK KATEGORİLERİ'; ?></h2>
            <div class="katalog-grid" style="gap: 20px; margin-top: 30px;">
                <?php foreach($tum_kategoriler as $kat): ?>
                    <a href="anasayfa.php?kat=<?php echo urlencode($kat['kategori_adi']); ?>" style="text-decoration: none;">
                        <div class="kategori-kutu">
                            <div class="kategori-kutu-ic" style="background-image: url('<?php echo $kat['kategori_resim']; ?>');"><div class="kategori-kutu-karartma"></div><div class="kategori-kutu-parlama"></div></div>
                            <span class="kategori-kutu-metin"><?php echo mb_strtoupper($kat['kategori_adi'], 'UTF-8'); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </main>
        
        <footer class="site-footer">
            <div class="footer-icerik">
                <p>© <?php echo date('Y'); ?> <?php echo $ayar['site_baslik']; ?> - Mert Tarafından Hazırlandı</p>
                <a href="https://github.com/sadecedqn/muzik" target="_blank" style="text-decoration: none;">
                    <button class="Btn"><span class="svgContainer"><svg fill="white" viewBox="0 0 496 512" height="1.6em"><path d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8zM97.2 352.9c-1.3 1-1 3.3.7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3.3 2.9 2.3 3.9 1.6 1 3.6.7 4.3-.7.7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3.7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3.7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9 1.6 2.3 4.3 3.3 5.6 2.3 1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z"></path></svg></span><span class="BG"></span></button>
                </a>
            </div>
        </footer>
    <?php endif; ?>

    <div id="girisModal" class="modal-arkaplan">
        <div class="modal-icerik">
            <span class="modal-kapat" onclick="modalKapat('girisModal')">✕</span>
            <h2 class="form-baslik" style="color: var(--tema-renk);"><?php echo !empty($ayar['modal_giris_baslik']) ? $ayar['modal_giris_baslik'] : 'SİSTEME GİRİŞ'; ?></h2>
            <form action="" method="POST" class="modal-form">
                <input type="text" name="kullanici_adi" class="modal-input" placeholder="<?php echo !empty($ayar['modal_kadi_placeholder']) ? $ayar['modal_kadi_placeholder'] : 'Kullanıcı Adı'; ?>" required>
                <input type="password" name="sifre" class="modal-input" placeholder="<?php echo !empty($ayar['modal_sifre_placeholder']) ? $ayar['modal_sifre_placeholder'] : 'Şifre'; ?>" required>
                <button type="submit" name="modal_giris" class="guncelle-btn"><?php echo !empty($ayar['modal_giris_btn']) ? $ayar['modal_giris_btn'] : 'GİRİŞ YAP'; ?></button>
            </form>
        </div>
    </div>

    <div id="kayitModal" class="modal-arkaplan">
        <div class="modal-icerik">
            <span class="modal-kapat" onclick="modalKapat('kayitModal')">✕</span>
            <h2 class="form-baslik" style="color: var(--tema-renk);"><?php echo !empty($ayar['modal_kayit_baslik']) ? $ayar['modal_kayit_baslik'] : 'KÜTÜPHANEYE KATIL'; ?></h2>
            <form action="" method="POST" class="modal-form">
                <input type="text" name="kullanici_adi" class="modal-input" placeholder="<?php echo !empty($ayar['modal_kadi_placeholder']) ? $ayar['modal_kadi_placeholder'] : 'Kullanıcı Adı'; ?>" required>
                <input type="password" name="sifre" class="modal-input" placeholder="<?php echo !empty($ayar['modal_sifre_placeholder']) ? $ayar['modal_sifre_placeholder'] : 'Şifre'; ?>" required>
                <button type="submit" name="modal_kayit" class="guncelle-btn"><?php echo !empty($ayar['modal_kayit_btn']) ? $ayar['modal_kayit_btn'] : 'KAYIT OL'; ?></button>
            </form>
        </div>
    </div>

    <script>
        function modalAc(id) { document.getElementById(id).style.display = 'flex'; }
        function modalKapat(id) { document.getElementById(id).style.display = 'none'; }
        window.onclick = function(event) { if (event.target.className === 'modal-arkaplan') { event.target.style.display = "none"; } }
    </script>
    
   <script>
        window.addEventListener('beforeunload', function() {
            const audio = document.getElementById('ses-motoru');
            if (audio && audio.getAttribute('src')) { 
                sessionStorage.setItem('muzik_src', audio.src);
                sessionStorage.setItem('muzik_dosya_yolu', audio.getAttribute('src')); 
                sessionStorage.setItem('muzik_sure', audio.currentTime);
                sessionStorage.setItem('muzik_isim', document.getElementById('mc-sarki-adi').innerText);
                sessionStorage.setItem('muzik_sanatci', document.getElementById('mc-sanatci').innerText);
                sessionStorage.setItem('muzik_kapak', document.getElementById('mc-kapak').src);
                sessionStorage.setItem('muzik_durum', audio.paused ? 'duraklatildi' : 'caliyor');
                sessionStorage.setItem('muzik_ses', audio.volume); 
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            if (sessionStorage.getItem('muzik_dosya_yolu')) {
                const audio = document.getElementById('ses-motoru');
                const isim = sessionStorage.getItem('muzik_isim');
                const sanatci = sessionStorage.getItem('muzik_sanatci');
                const kapak = sessionStorage.getItem('muzik_kapak');
                const dosyaYolu = sessionStorage.getItem('muzik_dosya_yolu');
                const durum = sessionStorage.getItem('muzik_durum');
                const ses = sessionStorage.getItem('muzik_ses'); 

                audio.src = sessionStorage.getItem('muzik_src');
                audio.setAttribute('src', dosyaYolu);
                audio.currentTime = parseFloat(sessionStorage.getItem('muzik_sure'));
                
                if (ses !== null) {
                    audio.volume = parseFloat(ses);
                    const sesCubugu = document.getElementById('mc-ses-cubugu');
                    if (sesCubugu) sesCubugu.value = ses;
                    
                    const ikonAcik = document.getElementById('ikon-ses-acik');
                    const ikonKapali = document.getElementById('ikon-ses-kapali');
                    if (parseFloat(ses) === 0) {
                        if(ikonAcik) ikonAcik.style.display = 'none';
                        if(ikonKapali) ikonKapali.style.display = 'block';
                    } else {
                        if(ikonAcik) ikonAcik.style.display = 'block';
                        if(ikonKapali) ikonKapali.style.display = 'none';
                    }
                }
                
                document.getElementById('mc-sarki-adi').innerText = isim;
                document.getElementById('mc-sanatci').innerText = sanatci;
                document.getElementById('mc-kapak').src = kapak;
                
                const widget = document.getElementById('muzik-calar-widget');
                widget.style.display = 'flex'; 
                widget.classList.add('goster');
                
                const cb = document.getElementById('mc-oynat-duraklat-checkbox');
                if(cb) cb.checked = (durum === 'caliyor');

                const headerAlan = document.querySelector('.ust-alan');
                if(headerAlan) {
                    headerAlan.style.setProperty('--bg-image', `url(${kapak})`);
                    if(durum === 'caliyor') headerAlan.classList.add('muzik-caliyor');
                }
                
                setTimeout(() => {
                    if (typeof tumSarkilar !== 'undefined') {
                        suAnkiIndeks = tumSarkilar.findIndex(s => s.muzik_dosyasi === dosyaYolu);
                    }
                    
                    document.querySelectorAll('.kart-oynat-btn input').forEach(input => {
                        if (input.getAttribute('onclick').includes(dosyaYolu)) {
                            input.checked = (durum === 'caliyor');
                            if (durum === 'caliyor') {
                                aktifCheckbox = input;
                                const kart = input.closest('.kapsayici-3d');
                                if(kart) kart.classList.add('aktif-kart');
                            }
                        }
                    });
                }, 50);

                if (durum === 'caliyor') {
                    let playPromise = audio.play();
                    if (playPromise !== undefined) {
                        playPromise.catch(error => { console.log("Oto-oynatma engellendi."); });
                    }
                }
                
                if (widget.classList.contains('suruklenebilir')) {
                    const savedX = sessionStorage.getItem('widget_x');
                    const savedY = sessionStorage.getItem('widget_y');
                    if(savedX && savedY) {
                        widget.style.bottom = 'auto';
                        widget.style.right = 'auto';
                        widget.style.left = savedX;
                        widget.style.top = savedY;
                    }
                } else {
                    widget.style.bottom = '';
                    widget.style.right = '';
                    widget.style.left = '';
                    widget.style.top = '';
                }
            }
        });
    </script>

    <script>const tumSarkilar = <?php echo json_encode($tum_muzikler_dizi); ?>;</script>
    <script src="assets/script.js"></script>
</body>
</html>