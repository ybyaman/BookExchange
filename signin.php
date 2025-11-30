<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adsoyad = $_POST['adsoyad'];
    $kullanici = $_POST['kullaniciadi'];
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];
    $sifre2 = $_POST['sifre2'];

    if ($sifre !== $sifre2) {
        $error = "Şifreler uyuşmuyor!";
    } else {
        $sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, username, email, password_hash) VALUES (?, ?, ?, ?)";
        
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$adsoyad, $kullanici, $email, $sifre_hash]);
            $success = "Kayıt Başarılı! Giriş yapabilirsiniz.";
        } catch (PDOException $e) {
            $error = "Kayıt hatası: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol - Kitap Takas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --brand-color: #fd7e14; }
        body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; padding-top: 80px; min-height: 100vh; display: flex; flex-direction: column; }
        .navbar { background-color: #fff !important; box-shadow: 0 1px 4px rgba(0,0,0,0.08); height: 70px; }
        .navbar-brand { color: var(--brand-color) !important; font-weight: 700; font-size: 1.5rem; }
        .nav-link-custom { color: #333; font-weight: 600; text-decoration: none; transition: 0.2s; }
        .nav-link-custom:hover { color: var(--brand-color); }
        .main-content { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 0; }
        .register-card { border: none; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); overflow: hidden; background: #fff; }
        .register-header { background: linear-gradient(135deg, #fd7e14, #ff922b); color: white; padding: 30px 20px; text-align: center; }
        .form-control:focus { border-color: var(--brand-color); box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25); }
        .btn-brand { background-color: var(--brand-color); color: white; font-weight: 600; padding: 10px; border-radius: 8px; border: none; width: 100%; transition: 0.2s; }
        .btn-brand:hover { background-color: #e36b09; color: white; transform: translateY(-1px); }
        .text-brand { color: var(--brand-color); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
 
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-book-reader me-2"></i>Kitap Takas</a>
            <a href="index.php" class="nav-link-custom"><i class="fas fa-arrow-left me-1"></i> Ana Sayfa</a>
        </div>
    </nav>
 
    <div class="main-content container">
        <div class="col-md-6 col-lg-5">
            <div class="card register-card">
                <div class="register-header">
                    <i class="fas fa-user-plus fa-4x mb-3"></i>
                    <h4>Yeni Üyelik Oluştur</h4>
                    <p class="mb-0 opacity-75">Kitap takası dünyasına katılın!</p>
                </div>
                <div class="card-body p-4">
                    
                    <?php if($success): ?>
                        <div class="alert alert-success text-center">
                            <?php echo $success; ?> <br> <a href="login.php" class="alert-link">Giriş Yap</a>
                        </div>
                    <?php endif; ?>

                    <?php if($error): ?>
                        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">AD SOYAD</label>
                            <input type="text" class="form-control" name="adsoyad" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">KULLANICI ADI</label>
                            <input type="text" class="form-control" name="kullaniciadi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">E-MAİL</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">ŞİFRE</label>
                            <input type="password" class="form-control" name="sifre" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">ŞİFRE (TEKRAR)</label>
                            <input type="password" class="form-control" name="sifre2" required>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-brand shadow-sm">Kayıt Ol</button>
                        </div>
                        <div class="text-center">
                            <span class="text-muted">Zaten hesabın var mı?</span>
                            <a href="login.php" class="text-brand">Giriş Yap</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>