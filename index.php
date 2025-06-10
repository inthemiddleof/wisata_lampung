<?php
require_once __DIR__ . '/admin/config/database.php';

$popular_wisata = mysqli_query($conn,
    "SELECT w.*, COUNT(u.id) as total_ulasan, AVG(u.rating) as avg_rating
     FROM wisata w
     LEFT JOIN ulasan u ON w.id = u.wisata_id
     GROUP BY w.id
     ORDER BY total_ulasan DESC, avg_rating DESC
     LIMIT 3");

$upcoming_events = []; // Akan diisi dengan query ke tabel events jika ada

$categories = mysqli_query($conn, "SELECT DISTINCT kategori FROM wisata");
?>
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wisata Lampung - Explore Keindahan Lampung</title>
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
            color: var(--dark);
            overflow-x: hidden;
        }

        .section-container {
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
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark);
            cursor: pointer;
        }

        .jumbotron {
            height: 95vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/base/siger.jpg') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 0 20px;
        }

        .jumbotron-content {
            max-width: 800px;
        }

        .jumbotron h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .jumbotron p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .explore-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 30px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }

        .explore-btn:hover {
            background-color: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }

        .section-title {
            text-align: center;
            /* margin-bottom: 3rem;
            margin-top: 2rem; */
            position: relative;
        }

        .section-title h2 {
            font-size: 2.2rem;
            color: var(--dark);
            display: inline-block;
            position: relative;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 2px;
        }

         .section-title p {
            margin-top: 15px;
        }


        .lokasi-section {
            padding: 3rem 2rem;
            background-color: white;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 2rem;
        }

        .section-title h2 {
            font-size: 2.2rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
            /* position: relative; */
            /* display: inline-block; */
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 2px;
        }

        .section-title p {
            color: var(--gray);
            max-width: 600px;
            margin: 1rem auto 0;
        }

        .lokasi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .lokasi-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 300px;
            cursor: pointer;
        }

        .lokasi-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .lokasi-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            padding: 1.5rem;
            color: white;
            transition: all 0.3s ease;
        }

        .lokasi-overlay p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
            transition: all 0.3s ease;
        }

        .lokasi-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .lokasi-card:hover img {
            transform: scale(1.1);
        }

        .lokasi-card:hover .product-overlay {
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            padding-bottom: 2rem;
        }

        .lokasi-card:hover .product-overlay p {
            opacity: 1;
            transform: translateY(-5px);
        }

        .wisata-section {
            padding: 4rem 0;
            background-color: #fff;
            /* margin: 4rem 0; */
            background-color: var(--primary-light);
        }

        .wisata-section .section-title {
            margin-bottom: 3rem;
            text-align: center;
        }

        .wisata-section .section-title h2 {
            font-size: 2.2rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .wisata-section .section-title p {
            color: var(--gray);
            font-size: 1.1rem;
        }

        .wisata-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }

        .wisata-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .wisata-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .wisata-img-container {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .wisata-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .wisata-card:hover .wisata-img {
            transform: scale(1.05);
        }

        .wisata-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: var(--accent);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .wisata-body {
            padding: 1.5rem;
        }

        .wisata-title {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .wisata-location {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--gray);
            margin-bottom: 0.8rem;
            font-size: 0.9rem;
        }

        .wisata-location i {
            color: var(--accent);
        }

        .wisata-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .wisata-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--warning);
            font-weight: 500;
        }

        .review-count {
            color: var(--gray);
            font-size: 0.8rem;
            font-weight: normal;
            margin-left: 3px;
        }

        .wisata-price {
            font-weight: 600;
            color: var(--success);
        }

        .wisata-btn {
            display: block;
            text-align: center;
            background-color: var(--primary-light);
            color: var(--primary);
            padding: 10px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .wisata-btn:hover {
            background-color: var(--primary);
            color: white;
        }

        .no-data {
            grid-column: 1 / -1;
            text-align: center;
            color: var(--gray);
            padding: 2rem 0;
        }

        /* .events-section {
            padding: 2rem 0;
            background-color: var(--primary-light);
        }

        .events-section .section-title {
            margin-bottom: 3rem;
            text-align: center;
        }

        .events-section .section-title h2 {
            font-size: 2.2rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .events-section .section-title p {
            color: var(--gray);
            font-size: 1.1rem;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .event-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .event-date {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 90px;
        }

        .event-day {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1;
        }

        .event-month {
            font-size: 1rem;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .event-body {
            padding: 1.5rem;
            flex: 1;
        }

        .event-title {
            font-size: 1.2rem;
            margin-bottom: 0.8rem;
            color: var(--dark);
        }

        .event-desc {
            color: var(--gray);
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 15px;
            font-size: 0.9rem;
            color: var(--gray);
            margin-bottom: 1.5rem;
        }

        .event-meta i {
            color: var(--accent);
            margin-right: 5px;
        }

        .event-btn {
            display: inline-block;
            background-color: var(--primary-light);
            color: var(--primary);
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .event-btn:hover {
            background-color: var(--primary);
            color: white;
        } */

                /* Footer */
        .main-footer {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            width: 100%;
            padding: 4rem 0 0;
            margin: 0;
        }

        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-col {
            padding: 0 15px;
        }

        .footer-col h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }

        .footer-col h3::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--accent);
        }

        .footer-col p {
            margin-bottom: 1rem;
            opacity: 0.9;
            line-height: 1.6;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            opacity: 0.9;
            transition: all 0.3s;
            display: block;
            line-height: 1.6;
        }

        .footer-links a:hover {
            opacity: 1;
            transform: translateX(5px);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 1.5rem;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background-color: var(--accent);
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding: 2rem 0;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            opacity: 0.8;
            font-size: 0.9rem;
            width: 100%;
        }

        @media (max-width: 576px) {
            .main-header {
            padding: 1rem;
            }

            .jumbotron {
            height: 80vh;
            }

            .jumbotron h1 {
            font-size: 1.8rem;
            }

            .section-title h2 {
            font-size: 1.8rem;
            }

            .wisata-section, .events-section {
            padding: 3rem 0;
            }
            
            .section-title h2 {
            font-size: 1.8rem;
            }
            
            .section-title p {
            font-size: 1rem;
            }

            .products-grid {
            grid-template-columns: 1fr;
            }
    
            .product-card {
                height: 250px;
            }
        }

        @media (max-width: 768px) {
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

            .jumbotron h1 {
                font-size: 2.2rem;
            }

            .jumbotron p {
                font-size: 1rem;
            }

            .produk-grid {
                flex-direction: column;
                align-items: center;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
            
            .produk-kolom {
                width: 100%;
                max-width: 300px;
            }

            .wisata-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            }
    
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .event-card {
                flex-direction: column;
            }
            
            .event-date {
                flex-direction: row;
                justify-content: space-between;
                padding: 1rem;
            }
            
            .event-day, .event-month {
                font-size: 1.2rem;
            }

            .footer-container {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .footer-col {
                text-align: center;
            }
            
            .footer-col h3::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .social-links {
                justify-content: center;
            }
            
            .footer-links a:hover {
                transform: none;
                padding-left: 5px;
            }
        }

        @media (max-width: 992px) {
            .jumbotron h1 {
                font-size: 2.8rem;
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

        <section class="jumbotron">
            <div class="jumbotron-content">
                <h1>Explore Keindahan Wisata Lampung</h1>
                <p>Temukan pengalaman tak terlupakan dengan berbagai destinasi wisata menarik di Provinsi Lampung</p>
                <a href="wisata.php" class="explore-btn">Jelajahi Sekarang</a>
            </div>
        </section>

    <section id="lokasi" class="lokasi-section">
    <div class="section-title">
        <h2>Mau Kemana Kita Hari Ini?</h2>
        <p>Beberapa provinsi berikut memiliki keindahan dan keunikan yang tidak bisa dilupakan</p>
    </div>
    <div class="lokasi-grid">
        <div class="lokasi-card" onclick="filterByLocation('Lampung Selatan')">
            <img src="assets/base/lampung-selatan.jpeg" alt="Lampung Selatan">
            <div class="lokasi-overlay">
                <p>Lampung Selatan</p>
            </div>
        </div>
        <div class="lokasi-card" onclick="filterByLocation('Lampung Timur')">
            <img src="assets/base/lampung-timur.jpg" alt="Lampung Timur">
            <div class="lokasi-overlay">
                <p>Lampung Timur</p>
            </div>
        </div>
        <div class="lokasi-card" onclick="filterByLocation('Lampung Barat')">
            <img src="assets/base/lampung-barat.jpg" alt="Lampung Barat">
            <div class="lokasi-overlay">
                <p>Lampung Barat</p>
            </div>
        </div>
        <div class="lokasi-card" onclick="filterByLocation('Pesisir Barat')">
            <img src="assets/base/pesisir-barat.jpg" alt="Pesisir Barat">
            <div class="lokasi-overlay">
                <p>Pesisir Barat</p>
            </div>
        </div>
        <div class="lokasi-card" onclick="filterByLocation('Pringsewu')">
            <img src="assets/base/pringsewu.jpg" alt="Pringsewu">
            <div class="lokasi-overlay">
                <p>Pringsewu</p>
            </div>
        </div>
        <div class="lokasi-card" onclick="filterByLocation('Pesawaran')">
            <img src="assets/base/pesawaran.jpg" alt="Pesawaran">
            <div class="lokasi-overlay">
                <p>Pesawaran</p>
            </div>
        </div>
        <div class="lokasi-card" onclick="filterByLocation('Bandar Lampung')">
            <img src="assets/base/bandar-lampung.jpg" alt="Bandar Lampung">
            <div class="lokasi-overlay">
                <p>Bandar Lampung</p>
            </div>
        </div>
        <div class="lokasi-card" onclick="filterByLocation('Lampung Utara')">
            <img src="assets/base/lampung-utara.jpg" alt="Lampung Utara">
            <div class="lokasi-overlay">
                <p>Lampung Utara</p>
            </div>
        </div>
    </div>
</section>


<section id="wisata" class="wisata-section">
    <div class="section-container">
        <div class="section-title">
            <h2>Wisata Populer</h2>
            <p>Destinasi wisata terbaik di Lampung berdasarkan rating pengunjung</p>
        </div>
        <div class="wisata-grid">
            <?php if (mysqli_num_rows($popular_wisata) > 0) { ?>
                <?php while ($wisata = mysqli_fetch_assoc($popular_wisata)) { ?>
                    <div class="wisata-card">
                        <div class="wisata-img-container">
                            <img src="
                            assets/uploads/<?php echo htmlspecialchars($wisata['gambar']); ?>" alt="<?php echo htmlspecialchars($wisata['nama']); ?>" class="wisata-img">
                            <div class="wisata-badge"><?php echo htmlspecialchars($wisata['kategori']); ?></div>
                        </div>
                        <div class="wisata-body">
                            <h3 class="wisata-title"><?php echo htmlspecialchars($wisata['nama']); ?></h3>
                            <div class="wisata-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($wisata['lokasi']); ?></span>
                            </div>
                            <div class="wisata-meta">
                                <div class="wisata-rating">
                                    <i class="fas fa-star"></i>
                                    <span><?php echo number_format($wisata['avg_rating'] ?? 0, 1); ?></span>
                                    <span class="review-count">(<?php echo $wisata['total_ulasan'] ?? 0; ?> ulasan)</span>
                                </div>
                                <div class="wisata-price">
                                    Rp<?php echo number_format($wisata['harga_tiket'] ?? 0, 0, ',', '.'); ?>
                                </div>
                            </div>
                            <a href="detail-wisata.php?id=<?php echo $wisata['id']; ?>" class="wisata-btn">Lihat Detail</a>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p class="no-data">Belum ada data wisata</p>
            <?php } ?>
        </div>
    </div>
</section>


<!-- <section id="events" class="events-section">
    <div class="section-container">
        <div class="section-title">
            <h2>Event Terdekat</h2>
            <p>Acara dan kegiatan menarik yang akan datang di Lampung</p>
        </div>
        <div class="events-grid">
            <div class="event-card">
                <div class="event-date">
                    <span class="event-day">15</span>
                    <span class="event-month">Jun</span>
                </div>
                <div class="event-body">
                    <h3 class="event-title">Festival Krakatau</h3>
                    <p class="event-desc">Festival tahunan untuk memperingati letusan Gunung Krakatau dengan berbagai pertunjukan seni dan budaya.</p>
                    <div class="event-meta">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Bandar Lampung</span>
                        <i class="fas fa-clock"></i>
                        <span>08:00 - 22:00 WIB</span>
                    </div>
                    <a href="#" class="event-btn">Detail Event</a>
                </div>
            </div>
            
            <div class="event-card">
                <div class="event-date">
                    <span class="event-day">22</span>
                    <span class="event-month">Jul</span>
                </div>
                <div class="event-body">
                    <h3 class="event-title">Tour de Lampung</h3>
                    <p class="event-desc">Event bersepeda yang menyusuri berbagai destinasi wisata menarik di Lampung.</p>
                    <div class="event-meta">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Lampung Selatan</span>
                        <i class="fas fa-clock"></i>
                        <span>06:00 - 18:00 WIB</span>
                    </div>
                    <a href="#" class="event-btn">Detail Event</a>
                </div>
            </div>
            
            <div class="event-card">
                <div class="event-date">
                    <span class="event-day">10</span>
                    <span class="event-month">Aug</span>
                </div>
                <div class="event-body">
                    <h3 class="event-title">Pesta Budaya Lampung</h3>
                    <p class="event-desc">Pameran dan pertunjukan berbagai kesenian dan budaya tradisional Lampung.</p>
                    <div class="event-meta">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Lampung Tengah</span>
                        <i class="fas fa-clock"></i>
                        <span>09:00 - 21:00 WIB</span>
                    </div>
                    <a href="#" class="event-btn">Detail Event</a>
                </div>
            </div>
        </div>
    </div>
</section> -->

<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-col">
            <h3>Tentang Kami</h3>
            <p>Wisata Lampung adalah platform informasi wisata yang menyediakan panduan lengkap untuk mengeksplorasi keindahan Provinsi Lampung.</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h3>Link Cepat</h3>
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="#about">Tentang Kami</a></li>
                <li><a href="#wisata">Tempat Wisata</a></li>
                <li><a href="#events">Events</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h3>Kontak Kami</h3>
            <p><i class="fas fa-map-marker-alt"></i> Jl. Soekarno Hatta No. 10, Bandar Lampung</p>
            <p><i class="fas fa-phone"></i> (0721) 123456</p>
            <p><i class="fas fa-envelope"></i> info@wisatalampung.com</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 Wisata Lampung. All Rights Reserved.</p>
    </div>
</footer>

<script>
function filterByLocation(location) {
    window.location.href = 'wisata.php?lokasi=' + encodeURIComponent(location);
}

    function applyLocationFilter() {
        const urlParams = new URLSearchParams(window.location.search);
        const location = urlParams.get('lokasi');
        
        if (location) {
            const wisataSection = document.getElementById('wisata');
            if (wisataSection) {
                setTimeout(() => {
                    window.scrollTo({
                        top: wisataSection.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }, 100);
            }
            
            const semuaWisata = document.querySelectorAll('.wisata-card');
            semuaWisata.forEach(wisata => {
                const wisataLocation = wisata.dataset.location || '';
                if (wisataLocation.includes(location)) {
                    wisata.style.display = 'block';
                } else {
                    wisata.style.display = 'none';
                }
            });
            
            const filterStatus = document.getElementById('filter-status');
            if (filterStatus) {
                filterStatus.textContent = `Menampilkan wisata di: ${location}`;
                filterStatus.style.display = 'block';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        applyLocationFilter();
        
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            document.getElementById('mainNav').classList.toggle('active');
        });

        window.addEventListener('scroll', function() {
            const header = document.getElementById('mainHeader');
            if (header) {
                if (window.scrollY > 10) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    const nav = document.getElementById('mainNav');
                    if (nav) nav.classList.remove('active');
                }
            });
        });
    });
</script>
</body>
</html>