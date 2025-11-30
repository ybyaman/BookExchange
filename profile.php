<?php
session_start();
include 'db.php';

// Giriş yapılmamışsa login'e at
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini çek
$stmt = $conn->prepare("SELECT * FROM Users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Kitap Takas</a>
            <div class="ms-auto">
                <a class="btn btn-light" href="index.php">Ana Sayfaya Dön</a>
                <a class="btn btn-danger ms-2" href="logout.php">Çıkış</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <img src="https://via.placeholder.com/150" class="rounded-circle mb-3" alt="Profil">
                        <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                        <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                        <hr>
                        <p><strong>E-mail:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Kayıt Tarihi:</strong> <?php echo date("d.m.Y", strtotime($user['created_at'])); ?></p>
                        
                        <a href="addBook.html" class="btn btn-success w-100 mt-3">Yeni Kitap Ekle</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>