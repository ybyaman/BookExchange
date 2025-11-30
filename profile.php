<?php
session_start();
include 'db.php';

// Giriş yapılmamışsa login sayfasına at
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['user_name'] ?? 'Kullanıcı';

// 1. Kullanıcının kitaplarını çek
$sql = "SELECT * FROM books WHERE user_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$myBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kitap sayısı
$bookCount = count($myBooks);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim - Kitap Takas</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { background-color: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; font-family: 'Segoe UI', sans-serif; }
        .main-content { flex: 1; padding-top: 20px; }
        footer { margin-top: auto; }
        .navbar { background-color: #fff !important; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; color: #0d6efd !important; display: flex; align-items: center; }
        .nav-icon-btn { color: #6c757d; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; text-decoration: none; }
        .nav-icon-btn:hover { background-color: #f0f2f5; color: #0d6efd; }
        .profile-header { background-color: #fff; border-radius: 12px; padding: 30px; border: 1px solid #eef0f3; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .profile-pic-wrapper { width: 120px; height: 120px; margin-right: 1.5rem; }
        .avatar-img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid #f8f9fa; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .add-book-card { border: 2px dashed #dee2e6 !important; background-color: #fff; min-height: 100%; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .add-book-card:hover { background-color: #f1f8ff !important; border-color: #0d6efd !important; color: #0d6efd; }
    </style>
</head>
<body>
 
    <nav class="navbar mb-4 sticky-top">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand" href="index.php"><i class="fas fa-book-open me-2"></i>Kitap Takas</a>
            <div class="d-flex align-items-center gap-2">
                <a href="index.php" class="nav-icon-btn" title="Ana Sayfa"><i class="fas fa-home fa-lg"></i></a>
                <div class="vr mx-2 text-secondary"></div>
                <a href="logout.php" class="nav-icon-btn text-danger" title="Güvenli Çıkış"><i class="fas fa-sign-out-alt fa-lg"></i></a>
            </div>
        </div>
    </nav>
 
    <div class="container main-content">
        
        <div class="row justify-content-center mb-4">
            <div class="col-md-8">
                <div class="profile-header d-flex align-items-center shadow-sm">
                    <div class="profile-pic-wrapper">
                        <img src="https://via.placeholder.com/150?text=Profil" class="avatar-img">
                    </div>
                    <div class="flex-grow-1">
                        <h2 class="mb-0 me-3"><?php echo htmlspecialchars($full_name); ?></h2>
                        <p class="text-muted mb-2">Kitap Sever</p>
                        <div class="d-flex gap-3 mt-2">
                            <span class="badge bg-primary p-2"><i class="fas fa-book me-1"></i> <span id="kitapSayisi"><?php echo $bookCount; ?></span> Kitap</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
        <div class="row justify-content-center">
            <div class="col-md-10">
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item"><button class="nav-link active">Kitaplarım</button></li>
                </ul>
 
                <div class="tab-content">
                    <div class="tab-pane fade show active">
                        <div class="row">
                            
                            <?php foreach ($myBooks as $book): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm border-start border-primary border-4">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="status-dot bg-success me-2" style="width:10px; height:10px; border-radius:50%; display:inline-block;"></span>
                                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($book['title']); ?></h5>
                                            </div>
                                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($book['book_condition']); ?></span>
                                        </div>
                                        <p class="text-muted small mb-3">
                                            <i class="fas fa-pen-nib me-1"></i><?php echo htmlspecialchars($book['author']); ?>
                                        </p>
                                        <div class="d-flex gap-2 mt-auto">
                                            <button class="btn btn-outline-secondary btn-sm flex-grow-1" disabled>Düzenle</button>
                                            <button class="btn btn-outline-danger btn-sm" disabled>Sil</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm border-2 add-book-card d-flex align-items-center justify-content-center bg-light"
                                     data-bs-toggle="modal" data-bs-target="#addBookModal">
                                    <div class="text-center p-3 text-muted">
                                        <i class="fas fa-plus-circle fa-3x mb-2"></i>
                                        <h6 class="mb-0">Yeni Kitap Ekle</h6>
                                    </div>
                                </div>
                            </div>
 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <footer class="bg-dark text-white text-center py-3 mt-5"><div class="container"><p class="mb-0">© 2024 Kitap Takas Platformu</p></div></footer>
 
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered"> 
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title text-primary fw-bold">Yeni Kitap İlanı</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <form action="addBook.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-lg-4 mb-4 mb-lg-0">
                                <label class="form-label fw-bold">Kitap Kapağı</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                            </div>
 
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Kitap Adı *</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Yazar *</label>
                                        <input type="text" class="form-control" name="author" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Kondisyon *</label>
                                        <select class="form-select" name="book_condition" required>
                                            <option>Yeni Gibi</option>
                                            <option>Çok İyi</option>
                                            <option>İdare Eder</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="publisher" value="-">
                                <input type="hidden" name="category" value="Diğer">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Açıklama</label>
                                    <textarea class="form-control" name="description" rows="4"></textarea>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Vazgeç</button>
                                    <button type="submit" class="btn btn-success px-5">Yayınla</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>