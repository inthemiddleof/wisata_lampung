<?php
require_once __DIR__ . '/admin/config/database.php';

if (!isset($_GET['id'])) {
    header('Location: wisata.php');
    exit;
}

$wisataId = (int)$_GET['id'];

$wisata = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT w.*, 
            AVG(u.rating) as avg_rating,
            COUNT(u.id) as total_ulasan
     FROM wisata w
     LEFT JOIN ulasan u ON w.id = u.wisata_id
     WHERE w.id = $wisataId
     GROUP BY w.id"));

if (!$wisata) {
    header('Location: wisata.php');
    exit;
}

$reviews = mysqli_query($conn,
    "SELECT u.*, a.username as nama_user
     FROM ulasan u
     JOIN admin a ON u.id = a.id
     WHERE u.wisata_id = $wisataId
     ORDER BY u.created_at DESC
     LIMIT 5");


$avg_rating = number_format($wisata['avg_rating'] ?? 0, 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($wisata['nama']); ?> - Wisata Lampung</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #ebefff;
            --secondary: #3f37c9;
            --dark: #1f2937;
            --light: #f9fafb;
            --accent: #f72585;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray: #6b7280;
            --gray-light: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.6;
        }

        
        .main-header {
            background-color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }
                
        .container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 0 20px;
                }


        .main-header.scrolled {
            padding: 0.7rem 2rem;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.15);
        }

        .main-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        .main-brand i {
            font-size: 1.8rem;
            color: var(--accent);
        }

        .main-nav {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 25px;
        }

        .nav-menu a {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
            padding: 5px 0;
        }

        .nav-menu a:hover {
            color: var(--primary);
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: width 0.3s;
        }

        .nav-menu a:hover::after {
            width: 100%;
        }

        .nav-menu a.active {
            color: var(--primary);
        }

        .nav-menu a.active::after {
            width: 100%;
        }

        .login-btn {
            color: white;
            background-color: var(--primary);
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }

        .login-btn:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(63, 102, 255, 0.3);
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark);
            cursor: pointer;
        }

        .main-content {
            padding: 3rem 0;
        }

        .wisata-detail {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .wisata-header {
            padding: 2rem;
            border-bottom: 1px solid var(--gray-light);
        }

        .wisata-header h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .wisata-meta {
            display: flex;
            gap: 30px;
            margin-bottom: 1rem;
        }

        .location, .rating {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray);
        }

        .location i {
            color: var(--primary);
        }

        .stars {
            color: var(--warning);
        }

        .rating-value {
            font-weight: 600;
            color: var(--dark);
        }

        .wisata-gallery {
            position: relative;
        }

        .main-image {
            height: 400px;
            overflow: hidden;
        }

        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .main-image:hover img {
            transform: scale(1.05);
        }

        .wisata-content {
            padding: 2rem;
        }

        .wisata-description, .wisata-map, .wisata-reviews {
            margin-bottom: 1.5rem;
        }

        .wisata-content h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 10px;
        }

        .wisata-content h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 2px;
        }

        .wisata-content p {
            color: var(--gray);
            margin-bottom: 1rem;
            line-height: 1.8;
        }

        .map-card {
            background: var(--primary-light);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .map-container {
            height: 400px;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .add-review {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .add-review h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--gray-light);
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }

        .form-group select:focus, 
        .form-group textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3);
        }

        .login-prompt {
            background: var(--primary-light);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-prompt a {
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
        }

        .login-prompt a:hover {
            text-decoration: underline;
        }

        .reviews-list {
            display: grid;
            gap: 1.5rem;
        }

        .review-item {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .review-header h4 {
            font-size: 1.1rem;
            color: var(--dark);
        }

        .review-rating {
            color: var(--warning);
        }

        .review-date {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .review-content p {
            color: var(--dark);
        }

        .footer {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 3rem 0 1.5rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--accent);
        }

        .footer-section.about p {
            margin-bottom: 1rem;
        }

        .footer-section.links ul {
            list-style: none;
        }

        .footer-section.links li {
            margin-bottom: 10px;
        }

        .footer-section.links a {
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }

        .footer-section.links a:hover {
            color: var(--accent);
            padding-left: 5px;
        }

        .footer-section.contact p {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .wisata-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .wisata-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .wisata-img {
            height: 180px;
            overflow: hidden;
        }

        .wisata-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .wisata-card:hover .wisata-img img {
            transform: scale(1.05);
        }

        .wisata-info {
            padding: 1.2rem;
        }

        .wisata-info h3 {
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
            color: var(--dark);
        }

        .wisata-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .wisata-gallery {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .wisata-gallery img {
            width: 100%;
            height: auto;
            display: block;
        }

        .wisata-description {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .wisata-description h2 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .wisata-description p {
            color: var(--gray);
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .wisata-meta {
            display: flex;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray);
        }

        .meta-item i {
            color: var(--primary);
        }

        .map-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .wisata-price {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 1.5rem 0;
            font-weight: 500;
            color: var(--success);
        }

        .wisata-price i {
            color: var(--success);
        }

        .wisata-reviews {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-light);
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            margin: 10px 0;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            color: var(--gray-light);
            cursor: pointer;
            font-size: 1.5rem;
            padding: 0 2px;
        }

        .star-rating input:checked ~ label,
        .star-rating input:hover ~ label {
            color: var(--warning);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--gray-light);
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
        }

        .reviews-list {
            display: grid;
            gap: 1.5rem;
        }

        .review-item {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .review-date {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .no-reviews {
            text-align: center;
            color: var(--gray);
            padding: 2rem;
        }

        @media (max-width: 992px) {
            .main-image {
                height: 350px;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .hamburger {
                display: block;
            }
            
            .wisata-meta {
                flex-direction: column;
                gap: 15px;
            }
            
            .main-image {
                height: 300px;
            }
            
            .wisata-main {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .wisata-header h1 {
                font-size: 1.8rem;
            }
            
            .main-image {
                height: 250px;
            }
            
            .map-container {
                height: 300px;
            }
        }
    </style>
</head>
<body>

<header class="main-header" id="mainHeader">
    <a href="index.php" class="main-brand">
        <i class="fas fa-map-marked-alt"></i>
        <span>Wisata Lampung</span>
    </a>

    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
    </button>

    <nav class="main-nav" id="mainNav">
        <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="wisata.php">Tempat Wisata</a></li>
                <li><a href="hotel.php">Hotel</a></li>
                <li><a href="#about">Tentang Kami</a></li>
        </ul>
        <a href="login.php" class="login-btn">
            <i class="fas fa-sign-in-alt"></i>
            <span>Login</span>
        </a>
    </nav>
</header>

    <main class="main-content">
        <section class="wisata-detail">
            <div class="container">
                <div class="wisata-header">
                    <h1><?php echo htmlspecialchars($wisata['nama']); ?></h1>
                    <div class="wisata-content">
                    

                        <div class="wisata-main">
                            <div class="wisata-gallery">
                                <img src="assets/uploads/<?php echo htmlspecialchars($wisata['gambar']); ?>" alt="<?php echo htmlspecialchars($wisata['nama']); ?>">
                            </div>
                            <div class="wisata-description">
                                <h2>Deskripsi</h2>
                                <p><?php echo nl2br(htmlspecialchars($wisata['deskripsi'])); ?></p>
                                
                                <div class="wisata-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($wisata['lokasi']); ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-tag"></i>
                                        <span><?php echo htmlspecialchars($wisata['kategori']); ?></span>
                                    </div>
                                    <div class="wisata-price">
                                        <i class="fas fa-ticket-alt"></i>
                                        <span>Rp <?php echo number_format($wisata['harga_tiket'], 0, ',', '.'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    
                        <?php if ($wisata['alamat']): ?>
                        <div class="wisata-address">
                            <h3>Alamat Lengkap</h3>
                            <p><?php echo htmlspecialchars($wisata['alamat']); ?></p>
                        </div>
                        <?php endif; ?>

                    
                        <div class="wisata-map">
                            <h2>Lokasi</h2>
                            <div class="map-container">
                                <?php if ($wisata['latitude'] && $wisata['longitude']): ?>
                                <iframe 
                                    src="https://maps.google.com/maps?q=<?php echo $wisata['latitude']; ?>,<?php echo $wisata['longitude']; ?>&hl=id&z=14&output=embed"
                                    style="height: 300px;"
                                    allowfullscreen>
                                </iframe>
                                <?php else: ?>
                                <p>Peta tidak tersedia</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                 
                    <div class="wisata-reviews">
                        <h2>Ulasan Pengunjung</h2>
                        
                        <div class="add-review">
                            <h3>Tambah Ulasan Anda</h3>
                            <form id="review-form" method="POST" action="submit_review.php">
                                <input type="hidden" name="wisata_id" value="<?php echo $wisataId; ?>">
                                
                                <div class="form-group">
                                    <label for="rating">Rating</label>
                                    <div class="star-rating">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                        <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="komentar">Komentar</label>
                                    <textarea id="komentar" name="komentar" rows="4" required></textarea>
                                </div>
                                
                                <button type="submit" class="btn">Kirim Ulasan</button>
                            </form>
                        </div>
                        
                        <div class="reviews-list">
                            <?php if (mysqli_num_rows($reviews) > 0): ?>
                                <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <h4><?php echo htmlspecialchars($review['nama_user']); ?></h4>
                                        <div class="review-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= $review['rating']): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="review-date">
                                        <?php echo date('d M Y', strtotime($review['created_at'])); ?>
                                    </div>
                                    <div class="review-content">
                                        <p><?php echo nl2br(htmlspecialchars($review['komentar'])); ?></p>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="no-reviews">Belum ada ulasan untuk tempat ini.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section about">
                    <h3>Tentang Kami</h3>
                    <p>WisataLampung adalah platform yang menyediakan informasi lengkap tentang tempat wisata di Lampung.</p>
                </div>
                <div class="footer-section links">
                    <h3>Link Cepat</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="wisata.php">Tempat Wisata</a></li>
                        <li><a href="events.php">Event</a></li>
                    </ul>
                </div>
                <div class="footer-section contact">
                    <h3>Kontak Kami</h3>
                    <p><i class="fas fa-envelope"></i> info@wisatalampung.com</p>
                    <p><i class="fas fa-phone"></i> +62 812 3456 7890</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> WisataLampung. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>