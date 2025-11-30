<?php
session_start();
include 'db.php';

// Eğer zaten giriş yapmışsa direkt profile gitsin
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

// Form gönderildi mi? (POST İşlemi)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici = $_POST['username'];
    $sifre = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$kullanici]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($sifre, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        header("Location: profile.php"); 
        exit();
    } else {
        $error = "Hatalı kullanıcı adı veya şifre!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap - Kitap Takas</title>
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
        .login-card { border: none; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); overflow: hidden; background: #fff; }
        .login-header { background: linear-gradient(135deg, #fd7e14, #ff922b); color: white; padding: 30px; text-align: center; }
        
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
        <div class="col-md-5 col-lg-4">
            <div class="card login-card">
                <div class="login-header">
                    <i class="fas fa-user-circle fa-4x mb-3"></i>
                    <h4>Hoşgeldiniz!</h4>
                    <p class="mb-0 opacity-75">Hesabınıza giriş yapın</p>
                </div>
                <div class="card-body p-4">
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">KULLANICI ADI</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user text-warning"></i></span>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">ŞİFRE</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock text-warning"></i></span>
                                <input type="password" class="form-control" id="txtSifre" name="password" required>
                                <span class="input-group-text" onclick="sifreGoster()" style="cursor:pointer;">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-brand shadow-sm">Giriş Yap</button>
                        </div>

                        <div class="text-center">
                            <span class="text-muted">Hesabın yok mu?</span>
                            <a href="signin.php" class="text-brand">Kayıt Ol</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function sifreGoster() {
            const input = document.getElementById('txtSifre');
            const icon = document.getElementById('eyeIcon');
            if (input.type === "password") { input.type = "text"; icon.className = "fas fa-eye-slash"; } 
            else { input.type = "password"; icon.className = "fas fa-eye"; }
        }
    </script>
</body>
</html>