<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Önce giriş yapmalısınız.'); window.location.href='login.php';</script>";
    exit();
}

$my_id = $_SESSION['user_id'];

if (!isset($_GET['book_id'])) {
    header("Location: index.php");
    exit();
}

$requested_book_id = $_GET['book_id'];

// İstenen Kitap
$sqlTarget = "SELECT books.*, users.full_name, users.id as owner_id FROM books JOIN users ON books.user_id = users.id WHERE books.id = ?";
$stmtTarget = $conn->prepare($sqlTarget);
$stmtTarget->execute([$requested_book_id]);
$targetBook = $stmtTarget->fetch(PDO::FETCH_ASSOC);

if (!$targetBook) { die("Kitap bulunamadı."); }
if ($targetBook['owner_id'] == $my_id) { echo "<script>alert('Kendi kitabına teklif veremezsin!'); window.location.href='index.php';</script>"; exit(); }

// Benim Kitaplarım
$sqlMyBooks = "SELECT * FROM books WHERE user_id = ?";
$stmtMyBooks = $conn->prepare($sqlMyBooks);
$stmtMyBooks->execute([$my_id]);
$myBooks = $stmtMyBooks->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $offered_book_id = $_POST['offered_book_id'];
    $message = $_POST['message'];
    $receiver_id = $targetBook['owner_id'];

    $sqlInsert = "INSERT INTO offers (sender_id, receiver_id, requested_book_id, offered_book_id, message) VALUES (?, ?, ?, ?, ?)";
    try {
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->execute([$my_id, $receiver_id, $requested_book_id, $offered_book_id, $message]);
        echo "<script>alert('Teklifiniz başarıyla gönderildi!'); window.location.href='profile.php';</script>";
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Teklif Ver - Kitap Takas</title>
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
        
        .card { border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .btn-brand { background-color: var(--brand-color); color: white; font-weight: 600; border-radius: 8px; border: none; padding: 10px 30px; transition: 0.2s; }
        .btn-brand:hover { background-color: #e36b09; color: white; }
        .btn-brand:disabled { background-color: #e9ecef; color: #adb5bd; }
        .form-control:focus, .form-select:focus { border-color: var(--brand-color); box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-book-reader me-2"></i>Kitap Takas</a>
            <div class="ms-auto">
                <a href="index.php" class="nav-link-custom"><i class="fas fa-arrow-left me-1"></i> Vazgeç</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card p-5">
                    <h3 class="text-center mb-5 fw-bold text-dark">Takas Teklifi Oluştur</h3>
                    
                    <div class="row align-items-center">
                        <div class="col-md-5 text-center">
                            <h6 class="text-muted fw-bold mb-3">İSTEDİĞİN KİTAP</h6>
                            <?php $img = !empty($targetBook['image_url']) ? $targetBook['image_url'] : 'https://via.placeholder.com/150'; ?>
                            <img src="<?php echo $img; ?>" class="img-fluid rounded mb-3 shadow-sm" style="height: 250px; object-fit: cover;">
                            <h5 class="fw-bold"><?php echo htmlspecialchars($targetBook['title']); ?></h5>
                            <p class="text-muted small">Sahibi: <?php echo htmlspecialchars($targetBook['full_name']); ?></p>
                        </div>

                        <div class="col-md-2 text-center my-4 my-md-0">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-exchange-alt fa-lg text-secondary"></i>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <h6 class="text-muted fw-bold mb-3">SENİN TEKLİFİN</h6>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label small text-muted">HANGİ KİTABINI VERECEKSİN?</label>
                                    <select class="form-select" name="offered_book_id" required>
                                        <option value="" selected disabled>Kitap Seç...</option>
                                        <?php foreach ($myBooks as $book): ?>
                                            <option value="<?php echo $book['id']; ?>"><?php echo htmlspecialchars($book['title']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if(count($myBooks) == 0): ?>
                                        <div class="text-danger small mt-2"><i class="fas fa-exclamation-circle"></i> Hiç kitabın yok! Önce profilinden kitap ekle.</div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small text-muted">KİTAP SAHİBİNE NOTUN</label>
                                    <textarea class="form-control" name="message" rows="4" placeholder="Merhaba, kitabınla ilgileniyorum..."></textarea>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-brand" <?php echo (count($myBooks) == 0) ? 'disabled' : ''; ?>>Teklifi Gönder</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>