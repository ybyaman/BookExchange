<?php
session_start();
include 'db.php';

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $offer_id = $_GET['id'];
    $action = $_GET['action'];
    $user_id = $_SESSION['user_id'];

    // Güvenlik: Bu teklif gerçekten bana mı yapılmış?
    // Sadece alıcı (receiver_id) durumu değiştirebilir.
    $checkSql = "SELECT * FROM offers WHERE id = ? AND receiver_id = ?";
    $stmtCheck = $conn->prepare($checkSql);
    $stmtCheck->execute([$offer_id, $user_id]);
    $offer = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($offer) {
        $newStatus = ($action === 'accept') ? 'accepted' : 'rejected';

        // Durumu güncelle
        $updateSql = "UPDATE offers SET status = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($updateSql);
        $stmtUpdate->execute([$newStatus, $offer_id]);
    }
}

// İşlem bitince profil sayfasına geri dön
header("Location: profile.php");
exit();
?>