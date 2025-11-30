<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_pic'];

    // Hata kontrolü
    if ($file['error'] != 0) {
        echo "<script>alert('Dosya yüklenirken hata oluştu.'); window.location.href='profile.php';</script>";
        exit();
    }

    // Dosya uzantısı kontrolü
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        echo "<script>alert('Sadece JPG, PNG ve GIF yüklenebilir.'); window.location.href='profile.php';</script>";
        exit();
    }

    // Dosya adı çakışmasını önlemek için benzersiz isim
    $new_name = "profile_" . $user_id . "_" . time() . "." . $ext;
    $destination = "uploads/" . $new_name;

    if (!file_exists("uploads")) { mkdir("uploads", 0777, true); }

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Veritabanını güncelle
        $sql = "UPDATE users SET profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$destination, $user_id]);
        
        header("Location: profile.php");
    } else {
        echo "<script>alert('Dosya kaydedilemedi.'); window.location.href='profile.php';</script>";
    }
}
?>