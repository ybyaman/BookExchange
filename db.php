<?php
// db.php

// 1. BAŞINA 'tcp:' EKLEDİK: Bu PHP'yi TCP kullanmaya zorlar.
$serverName = "tcp:localhost,54617"; 

$database = "BookExchange"; 

// Windows Authentication (Kullanıcı adı şifre boş)
$uid = ""; 
$pwd = ""; 

try {
    // 2. EKLEME: "TrustServerCertificate=1" 
    // Bu, "Sertifika hatası verip bağlantıyı kesme, sunucuya güven" demektir.
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database;TrustServerCertificate=1", $uid, $pwd);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Bağlantı başarılıysa sessizce devam eder
} catch(PDOException $e) {
    // Hatayı ekrana bas
    die("<h3>Bağlantı Hatası:</h3> " . $e->getMessage());
}
?>