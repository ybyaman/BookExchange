<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$book_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Güvenlik: Sadece kendi kitabını silebilirsin
$sql = "DELETE FROM books WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$book_id, $user_id]);

header("Location: profile.php");
exit();
?>