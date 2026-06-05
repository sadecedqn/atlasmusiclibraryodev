const motor = document.getElementById('ses-motoru');
const widget = document.getElementById('muzik-calar-widget');
const ilerlemeAlani = document.getElementById('mc-ilerleme-alani');
const ilerlemeCubugu = document.getElementById('mc-ilerleme-cubugu');
const headerAlan = document.querySelector('.ust-alan'); 
const widgetOynatCheckbox = document.getElementById('mc-oynat-duraklat-checkbox');

let aktifCheckbox = null; 
let suAnkiIndeks = -1; 

function muzikBaslat(checkboxElem, isim, sanatci, resim, dosyaYolu) {
    suAnkiIndeks = tumSarkilar.findIndex(s => s.muzik_dosyasi === dosyaYolu);
    document.querySelectorAll('.kapsayici-3d').forEach(kart => kart.classList.remove('aktif-kart'));

    if (checkboxElem.checked) {
        if (aktifCheckbox && aktifCheckbox !== checkboxElem) { aktifCheckbox.checked = false; }
        aktifCheckbox = checkboxElem;
        checkboxElem.closest('.kapsayici-3d').classList.add('aktif-kart');

        widget.style.display = 'flex';
        widget.classList.remove('gizle');
        widget.classList.add('goster');
        
        sarkiBilgileriniGuncelle(isim, sanatci, resim, dosyaYolu);
        motor.play();
        widgetOynatCheckbox.checked = true;
    } else {
        motor.pause();
        widgetOynatCheckbox.checked = false;
        if(headerAlan) headerAlan.classList.remove('muzik-caliyor'); 
    }
}

function sarkiBilgileriniGuncelle(isim, sanatci, resim, dosyaYolu) {
    document.getElementById('mc-sarki-adi').innerText = isim;
    document.getElementById('mc-sanatci').innerText = sanatci;
    document.getElementById('mc-kapak').src = resim;
    
    if(headerAlan) {
        headerAlan.style.setProperty('--bg-image', `url(${resim})`);
        headerAlan.classList.add('muzik-caliyor');
    }
    

    motor.src = dosyaYolu;
    motor.load(); 
}

function sonrakiMuzik() {
    if (suAnkiIndeks === -1) return;
    suAnkiIndeks = (suAnkiIndeks + 1) % tumSarkilar.length;
    otomatikGecis();
}

function oncekiMuzik() {
    if (suAnkiIndeks === -1) return;
    suAnkiIndeks = (suAnkiIndeks - 1 + tumSarkilar.length) % tumSarkilar.length;
    otomatikGecis();
}

function otomatikGecis() {
    const sarki = tumSarkilar[suAnkiIndeks];
    sarkiBilgileriniGuncelle(sarki.sarki_adi, sarki.sanatci_adi, sarki.resim_yolu, sarki.muzik_dosyasi);
    motor.play();
    widgetOynatCheckbox.checked = true;
    

    document.querySelectorAll('.kapsayici-3d').forEach(kart => {
        kart.classList.remove('aktif-kart');
        const cb = kart.querySelector('.kart-oynat-btn input');
        if(cb) cb.checked = false;
    });


    document.querySelectorAll('.kart-oynat-btn input').forEach(input => {
        if (input.getAttribute('onclick').includes(sarki.muzik_dosyasi)) {
            input.checked = true;
            aktifCheckbox = input;
            const kart = input.closest('.kapsayici-3d');
            if(kart) kart.classList.add('aktif-kart'); 
        }
    });
}

let oncekiSes = 1; 

function sesAyarla(deger) {
    motor.volume = deger;
    const ikonAcik = document.getElementById('ikon-ses-acik');
    const ikonKapali = document.getElementById('ikon-ses-kapali');
    if(deger == 0) {
        ikonAcik.style.display = 'none';
        ikonKapali.style.display = 'block';
    } else {
        ikonAcik.style.display = 'block';
        ikonKapali.style.display = 'none';
        oncekiSes = deger; 
    }
}

document.getElementById('mc-ses-ikon').addEventListener('click', () => {
    const sesCubugu = document.getElementById('mc-ses-cubugu');
    if (motor.volume > 0) { sesAyarla(0); sesCubugu.value = 0; } 
    else { sesAyarla(oncekiSes); sesCubugu.value = oncekiSes; }
});

function oynaticiKapat() {
    motor.pause();
    widgetOynatCheckbox.checked = false;
    if (aktifCheckbox) {
        aktifCheckbox.checked = false;
        aktifCheckbox.closest('.kapsayici-3d').classList.remove('aktif-kart');
        aktifCheckbox = null;
    }
    widget.classList.remove('goster');
    widget.classList.add('gizle');
    setTimeout(() => { if(widget.classList.contains('gizle')){ widget.style.display = 'none'; } }, 400);
    if(headerAlan) headerAlan.classList.remove('muzik-caliyor'); 
    

    sessionStorage.removeItem('muzik_dosya_yolu');
}

function oynatDuraklatTetikle() {
    if(widgetOynatCheckbox.checked) {
        motor.play();
        if(aktifCheckbox) {
            aktifCheckbox.checked = true;
            aktifCheckbox.closest('.kapsayici-3d').classList.add('aktif-kart');
        }
        if(headerAlan) headerAlan.classList.add('muzik-caliyor'); 
    } else {
        motor.pause();
        if(aktifCheckbox) {
            aktifCheckbox.checked = false; 
            aktifCheckbox.closest('.kapsayici-3d').classList.remove('aktif-kart');
        }
        if(headerAlan) headerAlan.classList.remove('muzik-caliyor'); 
    }
}

motor.addEventListener('timeupdate', () => {
    if(motor.duration) {
        const yuzde = (motor.currentTime / motor.duration) * 100;
        ilerlemeCubugu.style.width = yuzde + '%';
        document.getElementById('mc-gecen-zaman').innerText = zamanFormatla(motor.currentTime);
        document.getElementById('mc-kalan-zaman').innerText = zamanFormatla(motor.duration - motor.currentTime);
    }
});

ilerlemeAlani.addEventListener('click', (e) => {
    const genislik = ilerlemeAlani.clientWidth;
    const tiklananX = e.offsetX;
    motor.currentTime = (tiklananX / genislik) * motor.duration;
});

motor.addEventListener('ended', () => { sonrakiMuzik(); });

function zamanFormatla(saniye) {
    let dk = Math.floor(saniye / 60); let sn = Math.floor(saniye % 60);
    return (dk < 10 ? '0' + dk : dk) + ':' + (sn < 10 ? '0' + sn : sn);
}

window.addEventListener('scroll', function() {
    const navbar = document.getElementById('yapiskan-navbar');
    if (navbar) {
        if (window.scrollY > 350) { navbar.classList.add('aktif'); } 
        else { navbar.classList.remove('aktif'); }
    }
});


// WIDGET SÜRÜKLE - BIRAK MANTIĞI //
const suruklenebilirWidget = document.getElementById('muzik-calar-widget');
let isDragging = false;
let offsetX, offsetY;

suruklenebilirWidget.addEventListener('mousedown', function(e) {
    if (!suruklenebilirWidget.classList.contains('suruklenebilir')) {
        return;
    }

    if (e.target.closest('button') || e.target.closest('input') || e.target.closest('.mc-ilerleme-alani') || e.target.closest('label') || e.target.closest('svg')) {
        return;
    }
    
    isDragging = true;
    const rect = suruklenebilirWidget.getBoundingClientRect();
    offsetX = e.clientX - rect.left;
    offsetY = e.clientY - rect.top;
    
    suruklenebilirWidget.style.bottom = 'auto';
    suruklenebilirWidget.style.right = 'auto';
    suruklenebilirWidget.style.left = rect.left + 'px';
    suruklenebilirWidget.style.top = rect.top + 'px';
    suruklenebilirWidget.style.cursor = 'grabbing';
    document.body.style.userSelect = 'none';
});

document.addEventListener('mousemove', function(e) {
    if (!isDragging) return;
    suruklenebilirWidget.style.left = (e.clientX - offsetX) + 'px';
    suruklenebilirWidget.style.top = (e.clientY - offsetY) + 'px';
});

document.addEventListener('mouseup', function() {
    if (isDragging) {
        isDragging = false;
        suruklenebilirWidget.style.cursor = 'grab';
        document.body.style.userSelect = '';
        
        sessionStorage.setItem('widget_x', suruklenebilirWidget.style.left);
        sessionStorage.setItem('widget_y', suruklenebilirWidget.style.top);
    }
});


// KESİNTİSİZ AJAX SAYFALAMA SİSTEMİ //
document.addEventListener('click', function(e) {
    const link = e.target.closest('.sayfa-ok-btn');
    if (link && !link.classList.contains('pasif')) {
        e.preventDefault();
        const hedefUrl = link.getAttribute('href');
        const yon = link.classList.contains('sag') ? 'sonraki' : 'onceki';
        yonluSayfaGuncelle(hedefUrl, yon);
    }
});

async function yonluSayfaGuncelle(url, yon) {
    const anaKonteyner = document.getElementById('katalog-bolumu');
    const kayanAlan = document.getElementById('muzik-kayan-alan');
    
    kayanAlan.classList.add(yon === 'sonraki' ? 'sola-kay-cik' : 'saga-kay-cik');

    try {
        const response = await fetch(url);
        const html = await response.text();
        const parser = new DOMParser();
        const yeniDoc = parser.parseFromString(html, 'text/html');
        
        const yeniIcerik = yeniDoc.getElementById('katalog-bolumu').innerHTML;

        setTimeout(() => {
            anaKonteyner.innerHTML = yeniIcerik;
            
            const yeniKayanAlan = document.getElementById('muzik-kayan-alan');
            yeniKayanAlan.classList.add(yon === 'sonraki' ? 'sagdan-gel-gir' : 'soldan-gel-gir');
            
            window.history.pushState({}, '', url);
            
            if (typeof suAnkiIndeks !== 'undefined' && suAnkiIndeks !== -1) {
                const sarki = tumSarkilar[suAnkiIndeks];
                document.querySelectorAll('.kart-oynat-btn input').forEach(input => {
                    if (input.getAttribute('onclick').includes(sarki.muzik_dosyasi)) {
                        input.checked = !motor.paused;
                        if (!motor.paused) input.closest('.kapsayici-3d').classList.add('aktif-kart');
                    }
                });
            }

            setTimeout(() => {
                yeniKayanAlan.classList.remove('sagdan-gel-gir', 'soldan-gel-gir');
            }, 500);
            
        }, 400);
    } catch (err) {
        console.error("Hata:", err);
        kayanAlan.classList.remove('sola-kay-cik', 'saga-kay-cik');
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const navbar = document.getElementById('yapiskan-navbar');
    if (navbar && window.scrollY <= 400) {
        navbar.classList.remove('aktif');
    }
});
