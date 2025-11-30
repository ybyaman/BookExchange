<?php
// register.php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adsoyad = $_POST['adsoyad'];
    $kullanici = $_POST['kullaniciadi'];
    $email = $_POST['email'];
    $sifre = password_hash($_POST['sifre'], PASSWORD_DEFAULT); // Şifreyi güvenli hale getir

    // SQL Sorgusu
    $sql = "INSERT INTO Users (full_name, username, email, password_hash) VALUES (?, ?, ?, ?)";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$adsoyad, $kullanici, $email, $sifre]);
        
        // Başarılı olursa giriş sayfasına yönlendir
        echo "<script>alert('Kayıt Başarılı! Giriş yapabilirsiniz.'); window.location.href='login.html';</script>";
    } catch (PDOException $e) {
        echo "Kayıt hatası: " . $e->getMessage();
    }
}
?>