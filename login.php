<?php
// login.php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici = $_POST['username'];
    $sifre = $_POST['password'];

    // Tablo adı 'Users' yerine 'users' yapıldı
    $sql = "SELECT * FROM users WHERE username = ?";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$kullanici]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($sifre, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            header("Location: profile.html"); 
            exit();
        } else {
            echo "<script>alert('Hatalı kullanıcı adı veya şifre!'); window.location.href='login.html';</script>";
        }
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>