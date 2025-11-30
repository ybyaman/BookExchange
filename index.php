<?php
session_start();
include 'db.php';

$aranan = "";
$params = [];

// Temel Sorgu
$sql = "SELECT books.*, users.full_name, users.username 
        FROM books 
        JOIN users ON books.user_id = users.id";

// Arama Filtresi
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $aranan = htmlspecialchars($_GET['search']);
    $sql .= " WHERE books.title LIKE ? OR books.author LIKE ?";
    $params[] = "%$aranan%";
    $params[] = "%$aranan%";
}

$sql .= " ORDER BY books.created_at DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $kitaplar = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Veri çekme hatası: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitap Takas</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-color: #fd7e14;
            --bg-light: #f8f9fa;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Poppins', sans-serif;
            padding-top: 80px;
        }

        /* --- NAVBAR --- */
        .navbar {
            background-color: #fff !important;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            height: 70px;
        }

        .navbar-brand {
            color: var(--brand-color) !important;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: -0.5px;
        }

        /* Arama Çubuğu */
        .search-container {
            flex-grow: 1;
            max-width: 600px;
            margin: 0 20px;
        }
        
        .search-input-group {
            background-color: #f1f3f5;
            border-radius: 50px;
            padding: 5px 20px;
            display: flex;
            align-items: center;
            border: 1px solid transparent;
            transition: 0.3s;
        }

        .search-input-group:focus-within {
            background-color: #fff;
            border-color: #dee2e6;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .search-input {
            border: none;
            background: transparent;
            width: 100%;
            padding: 8px;
            outline: none;
            color: #495057;
        }

        .search-btn {
            border: none;
            background: transparent;
            color: #6c757d;
        }

        /* Sağ Butonlar */
        .nav-link-custom {
            color: #333;
            font-weight: 600;
            text-decoration: none;
            margin-right: 15px;
            transition: 0.2s;
            display: flex;
            align-items: center;
        }
        
        .nav-link-custom:hover {
            color: var(--brand-color);
        }

        .btn-brand {
            background-color: var(--brand-color);
            color: white;
            font-weight: 600;
            border-radius: 50px;
            padding: 8px 25px;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-brand:hover {
            background-color: #e36b09;
            color: white;
            transform: translateY(-1px);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.295)), url('kutuphanegorseli.png');
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat;
            color: black; 
            padding: 80px 0; 
            border-radius: 15px; 
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            text-align: center;
        }

        /* Kartlar */
        .card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
            background: #fff;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }

        .card-img-top {
            height: 240px;
            object-fit: cover;
        }

        .card-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 992px) {
            .search-container { margin: 10px 0; max-width: 100%; }
            .navbar { height: auto; padding-bottom: 10px; }
            body { padding-top: 130px; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book-reader me-2"></i>Kitap Takas
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="fas fa-bars"></span>
            </button>

            <div class="collapse navbar-collapse" id="navContent">
                
                <div class="search-container">
                    <form action="index.php" method="GET">
                        <div class="search-input-group">
                            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                            <input type="text" name="search" class="search-input" 
                                   placeholder="Kitap, yazar veya kategori ara..." 
                                   value="<?php echo $aranan; ?>">
                        </div>
                    </form>
                </div>

                <div class="d-flex align-items-center ms-auto mt-3 mt-lg-0">
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="nav-link-custom me-3">
                            <i class="fas fa-user-circle fa-lg me-2"></i> Profilim
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="nav-link-custom">Giriş Yap</a>
                    <?php endif; ?>

                    <a href="<?php echo isset($_SESSION['user_id']) ? 'addBook.php' : 'login.php'; ?>" class="btn btn-brand">
                        <i class="fas fa-camera"></i> Kitap Ekle
                    </a>

                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <?php if(empty($aranan)): ?>
        <div class="hero-section">
            <h1 class="display-4 fw-bold">Kitapların Rafında Tozlanmasın!</h1>
            <p class="lead opacity-75"><b>Okuduğun kitapları paylaş, yenilerine ücretsiz sahip ol.</b></p>
            <div class="mt-4">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="addBook.php" class="btn btn-warning btn-lg px-5 fw-bold">Hemen Başla</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-warning btn-lg px-5 fw-bold">Hemen Başla</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark">
                <?php if(!empty($aranan)): ?>
                    <i class="fas fa-search me-2 text-warning"></i>"<?php echo $aranan; ?>" sonuçları
                <?php else: ?>
                    <i class="fas fa-fire me-2 text-warning"></i>Vitrin İlanları
                <?php endif; ?>
            </h4>
            
            <?php if(!empty($aranan)): ?>
                <a href="index.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Filtreyi Temizle</a>
            <?php endif; ?>
        </div>
        
        <div class="row">
            <?php if (count($kitaplar) > 0): ?>
                <?php foreach ($kitaplar as $kitap): ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-4">
                        <div class="card h-100 shadow-sm">
                            
                            <?php 
                                $resimYolu = !empty($kitap['image_url']) ? $kitap['image_url'] : 'https://via.placeholder.com/250x350?text=Resim+Yok';
                            ?>
                            <div style="position: relative;">
                                <img src="<?php echo htmlspecialchars($resimYolu); ?>" 
                                     class="card-img-top" 
                                     alt="Kitap"
                                     onerror="this.onerror=null;this.src='https://via.placeholder.com/250x350?text=Resim+Yok';">
                                
                                <span class="badge bg-dark position-absolute top-0 start-0 m-2 bg-opacity-75">
                                    <?php echo htmlspecialchars($kitap['book_condition']); ?>
                                </span>
                            </div>
                            
                            <div class="card-body d-flex flex-column p-3">
                                <h5 class="card-title text-dark"><?php echo htmlspecialchars($kitap['title']); ?></h5>
                                <p class="text-muted small mb-2 text-truncate">
                                    <?php echo htmlspecialchars($kitap['author']); ?>
                                </p>
                                
                                <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($kitap['username']); ?>
                                    </small>
                                </div>
                                
                                <a href="offer.php?book_id=<?php echo $kitap['id']; ?>" class="btn btn-brand w-100 mt-3 btn-sm">
                                    Teklif Ver
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-light text-center py-5 border rounded-3">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5>Sonuç Bulunamadı</h5>
                        <p class="text-muted">Aradığınız kriterlere uygun kitap şu an mevcut değil.</p>
                        <a href="index.php" class="btn btn-outline-warning text-dark rounded-pill">Tüm Kitapları Gör</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="bg-white text-center py-4 mt-5 border-top">
        <div class="container">
            <p class="mb-0 text-muted small">© 2025 Kitap Takas Platformu - Öğrenci Projesi</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>