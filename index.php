<?php
session_start();
include 'db.php'; // Veritabanı bağlantısı

// Kitapları ve ekleyen kullanıcı adını çeken sorgu
$sql = "SELECT Books.*, Users.full_name, Users.username 
        FROM Books 
        JOIN Users ON Books.user_id = Users.id 
        ORDER BY Books.created_at DESC";

try {
    $stmt = $conn->query($sql);
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
    <title>Ana Sayfa - Kitap Takas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top { height: 250px; object-fit: cover; } /* Resim boyutlarını sabitle */
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Kitap Takas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="addBook.html">➕ Kitap Ekle</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php">👤 Profilim</a></li>
                        <li class="nav-item"><a class="nav-link btn btn-danger text-white ms-2" href="logout.php">Çıkış Yap</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.html">Giriş Yap</a></li>
                        <li class="nav-item"><a class="nav-link" href="signin.html">Kayıt Ol</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center mb-4">Son Eklenen Kitaplar</h2>

        <div class="row">
            <?php if (count($kitaplar) > 0): ?>
                <?php foreach ($kitaplar as $kitap): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card shadow-sm h-100">
                            <?php 
                                $resimYolu = !empty($kitap['image_url']) ? $kitap['image_url'] : 'https://via.placeholder.com/250x350?text=Resim+Yok';
                            ?>
                            <img src="<?php echo $resimYolu; ?>" class="card-img-top" alt="Kitap Resmi">
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($kitap['title']); ?></h5>
                                <p class="card-text text-muted small">
                                    Yazar: <?php echo htmlspecialchars($kitap['author']); ?> <br>
                                    Kategori: <?php echo htmlspecialchars($kitap['category']); ?>
                                </p>
                                <p class="card-text">
                                    <span class="badge bg-info text-dark"><?php echo htmlspecialchars($kitap['book_condition']); ?></span>
                                </p>
                                <div class="mt-auto">
                                    <p class="small text-end mb-1">Ekleyen: <strong><?php echo htmlspecialchars($kitap['username']); ?></strong></p>
                                    <a href="#" class="btn btn-outline-primary w-100 btn-sm">İncele / Mesaj At</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning text-center">Henüz hiç kitap eklenmemiş. İlk ekleyen sen ol!</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>