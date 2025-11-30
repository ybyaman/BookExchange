<?php
// db.php - MySQL Bağlantısı
// Bu kodda "sqlsrv" kelimesi ASLA geçmemeli!

$host = 'localhost';
$dbname = 'bookexchange'; // phpMyAdmin'deki ismin aynısı
$username = 'root';
$password = ''; // XAMPP şifresi boştur
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // echo "Bağlantı Başarılı!"; // Test bitince bu satırı silebilirsiniz
} catch (PDOException $e) {
    // Türkçe karakter sorunu olmasın diye:
    header('Content-Type: text/html; charset=utf-8');
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>