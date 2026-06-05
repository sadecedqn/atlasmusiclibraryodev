<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'vtbaglanti.php'; 

// GÜVENLİK KONTROLÜ
if (!isset($_SESSION['kullanici']) || $_SESSION['yetki'] !== 'admin') {
    header("Location: anasayfa.php");
    exit;
}

// --- DÜZENLEME MODU KONTROLLERİ ---
$duzenle_modu = false;
$d_sarki = ['id' => '', 'sarki_adi' => '', 'sanatci_adi' => '', 'kategori' => '', 'sure' => '', 'resim_yolu' => ''];

if (isset($_GET['duzenle'])) {
    $id = $_GET['duzenle'];
    $bul = $db->prepare("SELECT * FROM muzikler WHERE id = ?");
    $bul->execute([$id]);
    $res = $bul->fetch(PDO::FETCH_ASSOC);
    if ($res) { $d_sarki = $res; $duzenle_modu = true; }
}

$menu_duzenle_modu = false;
$d_menu = ['id' => '', 'menu_adi' => '', 'menu_link' => ''];

if (isset($_GET['menu_duzenle'])) {
    $m_id = $_GET['menu_duzenle'];
    $mbul = $db->prepare("SELECT * FROM menuler WHERE id = ?");
    $mbul->execute([$m_id]);
    $m_res = $mbul->fetch(PDO::FETCH_ASSOC);
    if ($m_res) { $d_menu = $m_res; $menu_duzenle_modu = true; }
}

$kat_duzenle_modu = false;
$d_kat = ['id' => '', 'kategori_adi' => ''];


if (isset($_GET['kat_duzenle'])) {
    $k_id = $_GET['kat_duzenle'];
    $kbul = $db->prepare("SELECT * FROM muzik_kategori WHERE id = ?");
    $kbul->execute([$k_id]);
    $k_res = $kbul->fetch(PDO::FETCH_ASSOC);
    if ($k_res) { $d_kat = $k_res; $kat_duzenle_modu = true; }
}

// Kullanıcı Düzenleme Modu Kontrolü
$kul_duzenle_modu = false;
$d_kul = ['id' => '', 'kullanici_adi' => '', 'yetki' => 'uye'];

if (isset($_GET['kul_duzenle'])) {
    $kul_id = $_GET['kul_duzenle'];
    $kul_bul = $db->prepare("SELECT id, kullanici_adi, yetki FROM kullanicilar WHERE id = ?");
    $kul_bul->execute([$kul_id]);
    $kul_res = $kul_bul->fetch(PDO::FETCH_ASSOC);
    if ($kul_res) { $d_kul = $kul_res; $kul_duzenle_modu = true; }
}


if (isset($_GET['kat_duzenle'])) {
    $k_id = $_GET['kat_duzenle'];
    $kbul = $db->prepare("SELECT * FROM muzik_kategori WHERE id = ?");
    $kbul->execute([$k_id]);
    $k_res = $kbul->fetch(PDO::FETCH_ASSOC);
    if ($k_res) { $d_kat = $k_res; $kat_duzenle_modu = true; }
}

// Verileri Çekelim
$kat_sorgu = $db->query("SELECT * FROM muzik_kategori ORDER BY kategori_adi ASC");
$kategoriler = $kat_sorgu->fetchAll(PDO::FETCH_ASSOC);

$liste_sorgu = $db->query("SELECT * FROM muzikler ORDER BY id DESC");
$muzikler = $liste_sorgu->fetchAll(PDO::FETCH_ASSOC);

$menu_sorgu = $db->query("SELECT * FROM menuler ORDER BY sira ASC, id ASC");
$menuler = $menu_sorgu->fetchAll(PDO::FETCH_ASSOC);

//Kullanıcıları Çek
$kullanici_sorgu = $db->query("SELECT id, kullanici_adi, yetki FROM kullanicilar ORDER BY yetki ASC, id DESC");
$tum_kullanicilar = $kullanici_sorgu->fetchAll(PDO::FETCH_ASSOC);

$ayar_sorgu = $db->query("SELECT * FROM ayarlar WHERE id = 1");
$ayar = $ayar_sorgu->fetch(PDO::FETCH_ASSOC);

if(!$ayar) { 
    $ayar = [
        'site_baslik' => 'Atlas Music Library', 'tema_renk' => '#ffffff', 'alt_baslik' => 'Diğer Müzikler',
        'kategori_baslik' => 'MÜZİK KATEGORİLERİ',
        'modal_giris_baslik' => 'SİSTEME GİRİŞ', 'modal_kadi_placeholder' => 'Kullanıcı Adı',
        'modal_sifre_placeholder' => 'Şifre', 'modal_giris_btn' => 'GİRİŞ YAP',
        'modal_kayit_baslik' => 'KÜTÜPHANEYE KATIL', 'modal_kayit_btn' => 'KAYIT OL'
    ]; 
}

$sayfa = 'admin'; 
include 'header.php'; 
?>

<div class="admin-konteynir">
    
    <div class="admin-masonry">
        
        <div class="admin-kart">
            <h2 class="form-baslik"><?php echo $menu_duzenle_modu ? "MENÜYÜ DÜZENLE" : "SİTE MENÜ YÖNETİMİ"; ?></h2>
            <form action="islem.php" method="POST" style="display: flex; gap: 10px; margin-bottom: 20px; align-items: center;">
                <?php if($menu_duzenle_modu): ?>
                    <input type="hidden" name="menu_id" value="<?php echo $d_menu['id']; ?>">
                <?php endif; ?>
                <input type="text" name="menu_adi" placeholder="Menü Adı" value="<?php echo $d_menu['menu_adi']; ?>" required style="flex: 1; padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff;">
                <input type="text" name="menu_link" placeholder="Link" value="<?php echo $d_menu['menu_link']; ?>" required style="flex: 1; padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff;">
                <button type="submit" name="<?php echo $menu_duzenle_modu ? 'menu_guncelle' : 'menu_ekle'; ?>" class="guncelle-btn" style="margin: 0; padding: 10px 15px; border-radius: 8px;">
                    <?php echo $menu_duzenle_modu ? 'KAYDET' : 'EKLE'; ?>
                </button>
            </form>

            <ul id="menu-listesi" style="list-style: none; padding: 0; margin: 0;">
                <?php foreach($menuler as $m): ?>
                <li class="menu-item" data-id="<?php echo $m['id']; ?>" draggable="true" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #111; margin-bottom: 8px; border-radius: 8px; border: 1px solid #222;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span class="drag-handle" style="cursor: grab; color: #888;">☰</span>
                        <span style="color: #fff; font-weight: bold;"><?php echo $m['menu_adi']; ?></span>
                    </div>
                    <div>
                        <a href="admin.php?menu_duzenle=<?php echo $m['id']; ?>" class="duzenle-link" style="font-size:11px;">DÜZENLE</a>
                        <a href="islem.php?menu_sil=<?php echo $m['id']; ?>" class="sil-link" style="font-size:11px;" onclick="return confirm('Silinsin mi?')">SİL</a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="admin-kart">
            <h2 class="form-baslik">SİTE GENEL AYARLARI</h2>
            <form action="islem.php" method="POST" class="admin-form">
                <div style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label style="color: #888; font-size: 11px;">Site Başlığı:</label>
                        <input type="text" name="site_baslik" value="<?php echo $ayar['site_baslik']; ?>" required>
                    </div>
                    <div style="flex:1;">
                        <label style="color: #888; font-size: 11px;">Liste Başlığı:</label>
                        <input type="text" name="alt_baslik" value="<?php echo $ayar['alt_baslik']; ?>" required>
                    </div>
                </div>

                <label style="color: #888; font-size: 11px; margin-top:10px;">Kategoriler Bölüm Başlığı:</label>
                <input type="text" name="kategori_baslik" value="<?php echo isset($ayar['kategori_baslik']) ? $ayar['kategori_baslik'] : 'MÜZİK KATEGORİLERİ'; ?>" placeholder="Örn: MÜZİK KATEGORİLERİ">

                <label style="color: #888; font-size: 11px; margin-top:10px;">Giriş/Kayıt Yazıları:</label>
                <input type="text" name="modal_giris_baslik" value="<?php echo $ayar['modal_giris_baslik']; ?>" placeholder="Giriş Başlığı">
                <div style="display:flex; gap:10px;">
                    <input type="text" name="modal_kadi_placeholder" value="<?php echo $ayar['modal_kadi_placeholder']; ?>" placeholder="K.Adı İpucu" style="flex:1;">
                    <input type="text" name="modal_sifre_placeholder" value="<?php echo $ayar['modal_sifre_placeholder']; ?>" placeholder="Şifre İpucu" style="flex:1;">
                </div>
                
                <div style="display:flex; align-items:center; gap:15px; margin-top:10px;">
                    <div style="flex:1;">
                        <label style="color: #888; font-size: 11px;">Tema Rengi:</label>
                        <input type="color" name="tema_renk" value="<?php echo $ayar['tema_renk']; ?>" style="height: 40px; cursor: pointer; padding: 5px; width: 100%;">
                    </div>
                    <button type="submit" name="ayarlari_guncelle" class="guncelle-btn" style="flex:2; margin-top:20px;">GÜNCELLE</button>
                </div>
            </form>
        </div>

        <div class="admin-kart">
            <h2 class="form-baslik"><?php echo $kat_duzenle_modu ? "KATEGORİYİ DÜZENLE" : "KATEGORİ YÖNETİMİ"; ?></h2>
            <form action="islem.php" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                <?php if($kat_duzenle_modu): ?>
                    <input type="hidden" name="kategori_id" value="<?php echo $d_kat['id']; ?>">
                <?php endif; ?>
                
                <input type="text" name="kategori_adi" placeholder="Kategori Adı" value="<?php echo $d_kat['kategori_adi']; ?>" required style="padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff;">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <input type="text" name="kategori_resim" placeholder="Resim URL'si" value="<?php echo isset($d_kat['kategori_resim']) ? $d_kat['kategori_resim'] : ''; ?>" style="padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff;">
                    
                    <div style="position: relative;">
                        <input type="file" name="kat_dosya" accept="image/*" style="background: #111; color: #888; padding: 7px; border: 1px dashed #555; border-radius: 8px; cursor: pointer; width: 100%; font-size: 11px;">
                    </div>
                </div>
                
                <button type="submit" name="<?php echo $kat_duzenle_modu ? 'kategori_guncelle' : 'kategori_ekle'; ?>" class="guncelle-btn" style="margin: 0; padding: 12px; border-radius: 8px;">
                    <?php echo $kat_duzenle_modu ? 'KATEGORİYİ KAYDET' : 'YENİ KATEGORİ EKLE'; ?>
                </button>
                <?php if($kat_duzenle_modu): ?><a href="admin.php" style="color:#888; font-size:12px; text-decoration:none; text-align:center;">Vazgeç</a><?php endif; ?>
            </form>

            <ul style="list-style: none; padding: 0; margin: 0; max-height: 250px; overflow-y: auto;">
                <?php foreach($kategoriler as $k): ?>
                <li style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #111; margin-bottom: 8px; border-radius: 8px; border: 1px solid #222;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img src="<?php echo $k['kategori_resim']; ?>" width="35" height="35" style="border-radius:5px; object-fit:cover; background:#222;">
                        <span style="color: #fff; font-weight: bold;"><?php echo mb_strtoupper($k['kategori_adi'], 'UTF-8'); ?></span>
                    </div>
                    <div>
                        <a href="admin.php?kat_duzenle=<?php echo $k['id']; ?>" class="duzenle-link" style="font-size:11px;">DÜZENLE</a>
                        <a href="islem.php?kategori_sil=<?php echo $k['id']; ?>" class="sil-link" style="font-size:11px;" onclick="return confirm('Silinsin mi?')">SİL</a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="admin-kart">
            <h2 class="form-baslik"><?php echo $duzenle_modu ? "MÜZİĞİ DÜZENLE" : "YENİ MÜZİK EKLE"; ?></h2>
            <form action="islem.php" method="POST" class="admin-form" enctype="multipart/form-data">
                <input type="hidden" name="sarki_id" value="<?php echo $d_sarki['id']; ?>">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                    <input type="text" name="sarki_adi" placeholder="Şarkı Adı" value="<?php echo $d_sarki['sarki_adi']; ?>" required>
                    <input type="text" name="sanatci_adi" placeholder="Sanatçı Adı" value="<?php echo $d_sarki['sanatci_adi']; ?>" required>
                    <select name="kategori" required>
                        <option value="" disabled <?php echo !$duzenle_modu ? 'selected' : ''; ?>>Kategori Seçin</option>
                        <?php foreach($kategoriler as $kat): ?>
                            <option value="<?php echo $kat['kategori_adi']; ?>" <?php echo ($d_sarki['kategori'] == $kat['kategori_adi']) ? 'selected' : ''; ?>><?php echo mb_strtoupper($kat['kategori_adi'], 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="sure" placeholder="Süre (Örn: 03:45)" value="<?php echo $d_sarki['sure']; ?>">
                </div>
                <input type="text" name="resim_yolu" placeholder="Resim URL" value="<?php echo $d_sarki['resim_yolu']; ?>">
                <input type="file" name="muzik_dosyasi" accept=".mp3, .wav, .ogg" <?php echo !$duzenle_modu ? 'required' : ''; ?> style="background: #111; color: #888; padding: 10px; border: 1px dashed #555; cursor: pointer;">
                
                <button type="submit" name="<?php echo $duzenle_modu ? 'muzik_guncelle' : 'muzik_ekle'; ?>" class="<?php echo $duzenle_modu ? 'guncelle-btn' : 'ekle-btn'; ?>">
                    <?php echo $duzenle_modu ? 'DEĞİŞİKLİKLERİ KAYDET' : 'KÜTÜPHANEYE EKLE'; ?>
                </button>
                <?php if($duzenle_modu): ?><a href="admin.php" style="text-align:center; color:#888; font-size:12px; text-decoration:none;">Vazgeç</a><?php endif; ?>
            </form>
        </div>

        <!-- YENİ EKLENEN: KULLANICI YÖNETİMİ KARTI -->
        <div class="admin-kart">
            <h2 class="form-baslik"><?php echo $kul_duzenle_modu ? "KULLANICIYI DÜZENLE" : "YENİ KULLANICI EKLE"; ?></h2>
            <form action="islem.php" method="POST" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                <?php if($kul_duzenle_modu): ?>
                    <input type="hidden" name="kullanici_id" value="<?php echo $d_kul['id']; ?>">
                <?php endif; ?>
                
                <input type="text" name="kullanici_adi" placeholder="Kullanıcı Adı" value="<?php echo $d_kul['kullanici_adi']; ?>" required style="padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff;">
                <input type="password" name="sifre" placeholder="<?php echo $kul_duzenle_modu ? 'Şifreyi Değiştir (İsteğe Bağlı)' : 'Şifre'; ?>" <?php echo !$kul_duzenle_modu ? 'required' : ''; ?> style="padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff;">
                
                <select name="yetki" required style="padding: 10px; border-radius: 8px; background: #111; border: 1px solid #333; color: #fff;">
                    <option value="uye" <?php echo ($d_kul['yetki'] == 'uye') ? 'selected' : ''; ?>>Üye</option>
                    <option value="admin" <?php echo ($d_kul['yetki'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>

                <button type="submit" name="<?php echo $kul_duzenle_modu ? 'kullanici_guncelle' : 'kullanici_ekle'; ?>" class="guncelle-btn" style="margin: 0; padding: 12px; border-radius: 8px;">
                    <?php echo $kul_duzenle_modu ? 'KULLANICIYI KAYDET' : 'YENİ KULLANICI EKLE'; ?>
                </button>
                <?php if($kul_duzenle_modu): ?><a href="admin.php" style="color:#888; font-size:12px; text-decoration:none; text-align:center;">Vazgeç</a><?php endif; ?>
            </form>

            <ul style="list-style: none; padding: 0; margin: 0; max-height: 250px; overflow-y: auto;">
                <?php foreach($tum_kullanicilar as $kul): ?>
                <li style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #111; margin-bottom: 8px; border-radius: 8px; border: 1px solid #222;">
                    <div style="display:flex; flex-direction:column;">
                        <span style="color: #fff; font-weight: bold;"><?php echo $kul['kullanici_adi']; ?></span>
                        <span style="color: <?php echo ($kul['yetki'] == 'admin') ? '#ff4d4d' : '#888'; ?>; font-size: 11px;"><?php echo strtoupper($kul['yetki']); ?></span>
                    </div>
                    <div>
                        <!-- Kendisini silmesini engellemek için küçük bir kontrol -->
                        <?php if($_SESSION['kullanici'] != $kul['kullanici_adi']): ?>
                            <a href="admin.php?kul_duzenle=<?php echo $kul['id']; ?>" class="duzenle-link" style="font-size:11px;">DÜZENLE</a>
                            <a href="islem.php?kullanici_sil=<?php echo $kul['id']; ?>" class="sil-link" style="font-size:11px;" onclick="return confirm('Bu kullanıcıyı kalıcı olarak silmek istediğinize emin misiniz?')">SİL</a>
                        <?php else: ?>
                            <span style="color:var(--tema-renk); font-size:11px; font-weight:bold;">(Sen)</span>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div> <div class="admin-liste">
        <h2 class="form-baslik">KÜTÜPHANE LİSTESİ</h2>
        <table class="admin-tablo">
            <thead><tr><th>Resim</th><th>Şarkı / Sanatçı</th><th>Kategori</th><th>İşlem</th></tr></thead>
            <tbody>
                <?php foreach($muzikler as $muzik): ?>
                <tr>
                    <td><img src="<?php echo $muzik['resim_yolu']; ?>" width="40" style="border-radius:5px;"></td>
                    <td><strong><?php echo $muzik['sarki_adi']; ?></strong><br><small style="color:#888;"><?php echo $muzik['sanatci_adi']; ?></small></td>
                    <td><?php echo $muzik['kategori']; ?></td>
                    <td>
                        <a href="admin.php?duzenle=<?php echo $muzik['id']; ?>" class="duzenle-link">DÜZENLE</a>
                        <a href="islem.php?sil=<?php echo $muzik['id']; ?>" class="sil-link" onclick="return confirm('Müziği tamamen silmek istediğinize emin misiniz?')">SİL</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const list = document.getElementById('menu-listesi');
    let draggingItem = null;
    list.addEventListener('dragstart', e => { draggingItem = e.target.closest('.menu-item'); setTimeout(() => { draggingItem.style.opacity = '0.4'; }, 0); });
    list.addEventListener('dragend', e => { draggingItem.style.opacity = '1'; draggingItem = null; siralamaGuncelle(); });
    list.addEventListener('dragover', e => { e.preventDefault(); const afterElement = getDragAfterElement(list, e.clientY); const currentItem = document.querySelector('.menu-item[style*="opacity: 0.4"]'); if (afterElement == null) { list.appendChild(currentItem); } else { list.insertBefore(currentItem, afterElement); } });
    function getDragAfterElement(container, y) { const draggableElements = [...container.querySelectorAll('.menu-item:not([style*="opacity: 0.4"])')]; return draggableElements.reduce((closest, child) => { const box = child.getBoundingClientRect(); const offset = y - box.top - box.height / 2; if (offset < 0 && offset > closest.offset) { return { offset: offset, element: child }; } else { return closest; } }, { offset: Number.NEGATIVE_INFINITY }).element; }
    function siralamaGuncelle() { const items = [...list.querySelectorAll('.menu-item')]; const siralama = items.map((item, index) => ({ id: item.getAttribute('data-id'), sira: index })); fetch('islem.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'menu_sirala=1&veri=' + JSON.stringify(siralama) }); }
</script>
</body>
</html>