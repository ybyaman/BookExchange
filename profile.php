<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['user_name'] ?? 'Kullanıcı';

// BİLDİRİM & GÖRÜLDÜ YAPMA
$sqlCount = "SELECT COUNT(*) FROM offers WHERE receiver_id = ? AND is_seen = 0";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->execute([$user_id]);
$newOfferCount = $stmtCount->fetchColumn();

if ($newOfferCount > 0) {
    $sqlUpdate = "UPDATE offers SET is_seen = 1 WHERE receiver_id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->execute([$user_id]);
}

// KULLANICI BİLGİSİ
$sqlUser = "SELECT * FROM users WHERE id = ?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->execute([$user_id]);
$currentUser = $stmtUser->fetch(PDO::FETCH_ASSOC);
$profilePic = !empty($currentUser['profile_pic']) ? $currentUser['profile_pic'] : 'https://via.placeholder.com/150?text=Profil';

// SORGULAR (Kitaplarım, Gelen, Giden)
$sql = "SELECT * FROM books WHERE user_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$myBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlIncoming = "SELECT offers.*, b1.title as offered_book_title, b1.image_url as offered_book_img, b2.title as requested_book_title, u.full_name as sender_name FROM offers JOIN books b1 ON offers.offered_book_id = b1.id JOIN books b2 ON offers.requested_book_id = b2.id JOIN users u ON offers.sender_id = u.id WHERE offers.receiver_id = ? ORDER BY offers.created_at DESC";
$stmtInc = $conn->prepare($sqlIncoming);
$stmtInc->execute([$user_id]);
$incomingOffers = $stmtInc->fetchAll(PDO::FETCH_ASSOC);

$sqlOutgoing = "SELECT offers.*, b1.title as offered_book_title, b2.title as requested_book_title, b2.image_url as requested_book_img, u.full_name as receiver_name FROM offers JOIN books b1 ON offers.offered_book_id = b1.id JOIN books b2 ON offers.requested_book_id = b2.id JOIN users u ON offers.receiver_id = u.id WHERE offers.sender_id = ? ORDER BY offers.created_at DESC";
$stmtOut = $conn->prepare($sqlOutgoing);
$stmtOut->execute([$user_id]);
$outgoingOffers = $stmtOut->fetchAll(PDO::FETCH_ASSOC);

$bookCount = count($myBooks);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim - Kitap Takas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --brand-color: #fd7e14; }
        body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; padding-top: 80px; }
        
        /* Navbar */
        .navbar { background-color: #fff !important; box-shadow: 0 1px 4px rgba(0,0,0,0.08); height: 70px; }
        .navbar-brand { color: var(--brand-color) !important; font-weight: 700; font-size: 1.5rem; }
        .nav-link-custom { color: #333; font-weight: 600; text-decoration: none; transition: 0.2s; display: flex; align-items: center; }
        .nav-link-custom:hover { color: var(--brand-color); }
        .btn-logout { background-color: #fff; color: #dc3545; border: 1px solid #dc3545; border-radius: 50px; padding: 5px 20px; font-weight: 600; text-decoration: none; transition: 0.2s; }
        .btn-logout:hover { background-color: #dc3545; color: #fff; }

        /* Profil Kartı */
        .profile-header { background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .profile-pic-wrapper { position: relative; width: 120px; height: 120px; cursor: pointer; }
        .avatar-img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 3px solid #f1f3f5; }
        .profile-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); border-radius: 50%; display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.3s; color: white; }
        .profile-pic-wrapper:hover .profile-overlay { opacity: 1; }

        /* Sekmeler (Tabs) */
        .nav-tabs .nav-link { color: #555; font-weight: 500; border: none; border-bottom: 3px solid transparent; }
        .nav-tabs .nav-link.active { color: var(--brand-color); border-bottom: 3px solid var(--brand-color); background: transparent; font-weight: 600; }
        .nav-tabs { border-bottom: 1px solid #dee2e6; }

        /* Kartlar */
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); transition: 0.2s; }
        .card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .offer-img { width: 60px; height: 80px; object-fit: cover; border-radius: 5px; }

        /* Yeni Kitap Ekle Buton Kartı */
        .add-book-card { border: 2px dashed #dee2e6 !important; background-color: #fff; display: flex; align-items: center; justify-content: center; min-height: 100%; text-decoration: none; transition: 0.2s; }
        .add-book-card:hover { border-color: var(--brand-color) !important; color: var(--brand-color) !important; background-color: #fff5f0; }
        .text-brand { color: var(--brand-color); }
    </style>
</head>
<body>
 
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-book-reader me-2"></i>Kitap Takas</a>
            <div class="d-flex align-items-center ms-auto gap-3">
                <a href="index.php" class="nav-link-custom"><i class="fas fa-home fa-lg"></i></a>
                <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt me-1"></i> Çıkış</a>
            </div>
        </div>
    </nav>
 
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="profile-header d-flex align-items-center">
                    <div class="me-4">
                        <form action="upload_profile.php" method="POST" enctype="multipart/form-data" id="profileForm">
                            <div class="profile-pic-wrapper" onclick="document.getElementById('fileInput').click()">
                                <img src="<?php echo $profilePic; ?>" class="avatar-img">
                                <div class="profile-overlay"><i class="fas fa-camera fa-2x"></i></div>
                            </div>
                            <input type="file" name="profile_pic" id="fileInput" style="display: none;" onchange="document.getElementById('profileForm').submit()">
                        </form>
                    </div>
                    <div>
                        <h2 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($full_name); ?></h2>
                        <p class="text-muted mb-2">Kitap Sever</p>
                        <span class="badge bg-warning text-dark p-2 rounded-pill"><i class="fas fa-book me-1"></i> <?php echo $bookCount; ?> Kitap</span>
                    </div>
                </div>
            </div>
        </div>
 
        <div class="row justify-content-center">
            <div class="col-md-10">
                <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#books">Kitaplarım</button></li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#incoming">
                            Gelen Teklifler
                            <?php if ($newOfferCount > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-1"><?php echo $newOfferCount; ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#outgoing">Giden Teklifler</button></li>
                </ul>
 
                <div class="tab-content">
                    
                    <div class="tab-pane fade show active" id="books">
                        <div class="row">
                            <?php foreach ($myBooks as $book): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 p-3">
                                    <h5 class="card-title text-dark"><?php echo htmlspecialchars($book['title']); ?></h5>
                                    <p class="text-muted small"><?php echo htmlspecialchars($book['author']); ?></p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($book['book_condition']); ?></span>
                                        <a href="deleteBook.php?id=<?php echo $book['id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm('Silmek istediğine emin misin?');">
                                            <i class="fas fa-trash-alt"></i> Sil
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="col-md-4 mb-4">
                                <a href="addBook.php" class="card add-book-card h-100 p-4">
                                    <div class="text-center text-muted">
                                        <i class="fas fa-plus-circle fa-3x mb-2"></i>
                                        <h6 class="mb-0 fw-bold">Yeni Kitap Ekle</h6>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="incoming">
                        <?php if(count($incomingOffers) > 0): ?>
                            <?php foreach($incomingOffers as $offer): ?>
                                <div class="card mb-3 p-3 border-start border-4 <?php echo ($offer['status'] == 'pending') ? 'border-warning' : (($offer['status'] == 'accepted') ? 'border-success' : 'border-danger'); ?>">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 text-center">
                                                <small class="d-block text-muted" style="font-size: 10px;">VERİLEN</small>
                                                <?php $img = (!empty($offer['offered_book_img']) && file_exists($offer['offered_book_img'])) ? $offer['offered_book_img'] : 'https://via.placeholder.com/60?text=Kitap'; ?>
                                                <img src="<?php echo htmlspecialchars($img); ?>" class="offer-img">
                                            </div>
                                            <div class="me-3"><i class="fas fa-exchange-alt fa-lg text-muted"></i></div>
                                            <div class="me-3 text-center">
                                                <small class="d-block text-muted" style="font-size: 10px;">İSTENEN</small>
                                                <strong><?php echo htmlspecialchars($offer['requested_book_title']); ?></strong>
                                            </div>
                                            <div class="ms-3 ps-3 border-start">
                                                <h6 class="mb-1 text-brand"><?php echo htmlspecialchars($offer['sender_name']); ?></h6>
                                                <p class="mb-0 small text-muted">"<?php echo htmlspecialchars($offer['message']); ?>"</p>
                                            </div>
                                        </div>
                                        <div>
                                            <?php if ($offer['status'] == 'pending'): ?>
                                                <a href="handleOffer.php?id=<?php echo $offer['id']; ?>&action=accept" class="btn btn-success btn-sm rounded-pill me-1"><i class="fas fa-check"></i> Kabul</a>
                                                <a href="handleOffer.php?id=<?php echo $offer['id']; ?>&action=reject" class="btn btn-danger btn-sm rounded-pill"><i class="fas fa-times"></i> Reddet</a>
                                            <?php elseif ($offer['status'] == 'accepted'): ?>
                                                <span class="badge bg-success rounded-pill">Kabul Edildi</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger rounded-pill">Reddedildi</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-light text-center">Henüz gelen bir teklif yok.</div>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="outgoing">
                        <?php if(count($outgoingOffers) > 0): ?>
                            <?php foreach($outgoingOffers as $offer): ?>
                                <div class="card mb-3 p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <?php $img = (!empty($offer['requested_book_img']) && file_exists($offer['requested_book_img'])) ? $offer['requested_book_img'] : 'https://via.placeholder.com/60?text=Kitap'; ?>
                                            <img src="<?php echo htmlspecialchars($img); ?>" class="offer-img">
                                        </div>
                                        <div>
                                            <h6 class="mb-1">İstediğin: <?php echo htmlspecialchars($offer['requested_book_title']); ?></h6>
                                            <p class="mb-1 small">Teklif Ettiğin: <strong><?php echo htmlspecialchars($offer['offered_book_title']); ?></strong></p>
                                            <p class="mb-0 small text-muted">Kime: <?php echo htmlspecialchars($offer['receiver_name']); ?></p>
                                        </div>
                                        <div class="ms-auto">
                                            <?php if ($offer['status'] == 'pending'): ?>
                                                <span class="badge bg-warning text-dark rounded-pill">Beklemede</span>
                                            <?php elseif ($offer['status'] == 'accepted'): ?>
                                                <span class="badge bg-success rounded-pill">Kabul Edildi</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger rounded-pill">Reddedildi</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-light text-center">Henüz yaptığın bir teklif yok.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>