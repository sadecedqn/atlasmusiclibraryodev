<?php
session_start();
session_destroy(); // Oturumu tamamen sıfırlar
header("Location: anasayfa.php"); // Çıkış yapınca ana sayfaya yollar
exit;
?>