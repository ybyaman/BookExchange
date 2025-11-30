<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $category = $_POST['category'];
    $condition = $_POST['book_condition'];
    $description = $_POST['description'];

    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_url = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    // Tablo adı 'Books' yerine 'books' yapıldı
    $sql = "INSERT INTO books (user_id, title, author, publisher, category, book_condition, description, image_url) 
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