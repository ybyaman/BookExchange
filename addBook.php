<?php
session_start();
include 'db.php'; // Veritabanı bağlantı dosyanız

// Kullanıcı giriş yapmamışsa login sayfasına at
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $category = $_POST['category'];
    $condition = $_POST['book_condition'];
    $description = $_POST['description'];

    // Resim Yükleme İşlemi
    $target_dir = "uploads/"; // Resimlerin yükleneceği klasör
    // Eğer klasör yoksa oluştur
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_url = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        // Resmi klasöre taşı
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    // SQL Sorgusu (Veritabanına Kayıt)
    $sql = "INSERT INTO Books (user_id, title, author, publisher, category, book_condition, description, image_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $title, $author, $publisher, $category, $condition, $description, $image_url]);
        
        echo "<script>alert('Kitap başarıyla eklendi!'); window.location.href='index.html';</script>";
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>