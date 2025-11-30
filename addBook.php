<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

    $image_url = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    $sql = "INSERT INTO books (user_id, title, author, publisher, category, book_condition, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $title, $author, $publisher, $category, $condition, $description, $image_url]);
        echo "<script>alert('Kitap başarıyla eklendi!'); window.location.href='profile.php';</script>";
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kitap Ekle - Kitap Takas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --brand-color: #fd7e14; }
        body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; padding-top: 80px; }
        .navbar { background-color: #fff !important; box-shadow: 0 1px 4px rgba(0,0,0,0.08); height: 70px; }
        .navbar-brand { color: var(--brand-color) !important; font-weight: 700; font-size: 1.5rem; }
        .nav-link-custom { color: #333; font-weight: 600; text-decoration: none; transition: 0.2s; }
        .nav-link-custom:hover { color: var(--brand-color); }
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .card-header { background-color: var(--brand-color); color: white; font-weight: 600; border-radius: 15px 15px 0 0 !important; padding: 20px; text-align: center; }
        .form-control:focus, .form-select:focus { border-color: var(--brand-color); box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25); }
        .btn-brand { background-color: var(--brand-color); color: white; font-weight: 600; border-radius: 8px; border: none; padding: 10px; transition: 0.2s; }
        .btn-brand:hover { background-color: #e36b09; color: white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-book-reader me-2"></i>Kitap Takas</a>
            <div class="ms-auto">
                <a href="profile.php" class="nav-link-custom"><i class="fas fa-arrow-left me-1"></i> Vazgeç</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-5">
                    <div class="card-header">
                        <h4 class="mb-0">Yeni Kitap Ekle</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">KİTAP ADI</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small text-muted">YAZAR</label>
                                    <input type="text" class="form-control" name="author" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small text-muted">YAYINEVİ</label>
                                    <input type="text" class="form-control" name="publisher">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small text-muted">KATEGORİ</label>
                                    <select class="form-select" name="category">
                                        <option value="Roman">Roman</option>
                                        <option value="Bilim">Bilim</option>
                                        <option value="Tarih">Tarih</option>
                                        <option value="Eğitim">Eğitim</option>
                                        <option value="Diğer">Diğer</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small text-muted">KONDİSYON</label>
                                    <select class="form-select" name="book_condition">
                                        <option value="Yeni Gibi">Yeni Gibi</option>
                                        <option value="Çok İyi">Çok İyi</option>
                                        <option value="İyi">İyi</option>
                                        <option value="İdare Eder">İdare Eder</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">AÇIKLAMA</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted">KAPAK FOTOĞRAFI</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-brand">Kitabı Kaydet ve Yayınla</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>