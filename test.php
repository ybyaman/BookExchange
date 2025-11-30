<?php
// Sürücü Kontrolü
if (extension_loaded("pdo_sqlsrv")) {
    echo "<h1>Harika! MS SQL Sürücüleri Yüklü.</h1>";
} else {
    echo "<h1>HATA: MS SQL Sürücüleri YÜKLÜ DEĞİL!</h1>";
    echo "<p>Lütfen 'php_pdo_sqlsrv' eklentisini kurun. Aksi takdirde siteniz çalışmaz.</p>";
}

include 'db.php';
if($conn) {
    echo "<h2>Veritabanı Bağlantısı BAŞARILI! ✅</h2>";
}
?>