<?php
require_once __DIR__ . '/admin/config/database.php';

if (!isset($_GET['id'])) {
    header('Location: hotel.php');
    exit;
}

$hotelId = (int)$_GET['id'];

$hotel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM hotel WHERE id = $hotelId"));

if (!$hotel) {
    header('Location: hotel.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($hotel['nama']); ?> - Wisata Lampung</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Styles similar to detail-wisata.php but adjusted for hotel */
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
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

        /* .nav-menu a.active::after {
            width: 100%;
        } */

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

        .hotel-detail {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .hotel-header {
            margin: 3rem 0;
            padding: 2rem auto;
            /* border-bottom: 1px solid var(--gray-light); */
        }

        .hotel-header h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .hotel-meta {
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

        .hotel-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .hotel-gallery {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .hotel-gallery img {
            width: 100%;
            height: auto;
            display: block;
        }

        .hotel-description {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .hotel-description h2 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .hotel-description p {
            color: var(--gray);
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .hotel-price {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 1.5rem 0;
            font-weight: 500;
            color: var(--success);
            font-size: 1.2rem;
        }

        .hotel-price i {
            color: var(--success);
        }

        .hotel-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--warning);
            margin-bottom: 1rem;
        }

        .map-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
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

        @media (max-width: 768px) {
            .hotel-main {
                grid-template-columns: 1fr;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .main-nav {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 70px);
                background-color: white;
                flex-direction: column;
                justify-content: flex-start;
                padding: 2rem;
                transition: all 0.3s ease;
            }
            
            .main-nav.active {
                left: 0;
            }
            
            .nav-menu {
                flex-direction: column;
                gap: 20px;
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .hotel-header h1 {
                font-size: 1.8rem;
            }
            
            .hotel-meta {
                flex-direction: column;
                gap: 15px;
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
            <li><a href="index.php#about">Tentang Kami</a></li>
            <li><a href="wisata.php">Tempat Wisata</a></li>
            <li><a href="hotel.php" class="active">Hotel</a></li>
        </ul>
        <a href="login.php" class="login-btn">
            <i class="fas fa-sign-in-alt"></i>
            <span>Login</span>
        </a>
    </nav>
</header>

    <main class="main-content">
        <section class="hotel-detail">
            <div class="container">
                <div class="hotel-header">
                    <h1><?php echo htmlspecialchars($hotel['nama']); ?></h1>
                    <div class="hotel-meta">
                        <div class="location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($hotel['lokasi']); ?></span>
                        </div>
                        <div class="rating">
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $hotel['bintang']): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hotel-main">
                    <div class="hotel-gallery">
                        <img src="assets/uploads/<?php echo htmlspecialchars($hotel['gambar']); ?>" alt="<?php echo htmlspecialchars($hotel['nama']); ?>">
                    </div>
                    <div class="hotel-description">
                        <h2>Deskripsi Hotel</h2>
                        <p><?php echo nl2br(htmlspecialchars($hotel['deskripsi'])); ?></p>
                        
                        <div class="hotel-price">
                            <i class="fas fa-tag"></i>
                            <span>Rp <?php echo number_format($hotel['harga'], 0, ',', '.'); ?> / malam</span>
                        </div>
                        
                        <div class="hotel-rating">
                            <span>Rating: </span>
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $hotel['bintang']): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <span><?php echo $hotel['bintang']; ?> Bintang</span>
                        </div>
                        
                        <?php if ($hotel['alamat']): ?>
                        <div class="hotel-address">
                            <h3>Alamat Lengkap</h3>
                            <p><?php echo htmlspecialchars($hotel['alamat']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($hotel['latitude'] && $hotel['longitude']): ?>
                <div class="map-container">
                    <h2>Lokasi</h2>
                    <iframe 
                        src="https://maps.google.com/maps?q=<?php echo $hotel['latitude']; ?>,<?php echo $hotel['longitude']; ?>&hl=id&z=14&output=embed"
                        style="height: 300px; width: 100%; border: none; border-radius: 12px;"
                        allowfullscreen>
                    </iframe>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section about">
                    <h3>Tentang Kami</h3>
                    <p>WisataLampung adalah platform yang menyediakan informasi lengkap tentang tempat wisata dan akomodasi di Lampung.</p>
                </div>
                <div class="footer-section links">
                    <h3>Link Cepat</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="wisata.php">Tempat Wisata</a></li>
                        <li><a href="hotel.php">Hotel</a></li>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('mobileMenuBtn').addEventListener('click', function() {
                document.getElementById('mainNav').classList.toggle('active');
            });

            window.addEventListener('scroll', function() {
                if (window.scrollY > 10) {
                    document.getElementById('mainHeader').classList.add('scrolled');
                } else {
                    document.getElementById('mainHeader').classList.remove('scrolled');
                }
            });
        });
    </script>
</body>
</html>