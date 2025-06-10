<?php
require_once __DIR__ . '/admin/config/database.php';

// Get location filter from URL if exists
$location_filter = isset($_GET['lokasi']) ? $_GET['lokasi'] : '';

// Build the query with optional location filter
$query = "SELECT * FROM hotel";

if (!empty($location_filter)) {
    $query .= " WHERE lokasi = '" . mysqli_real_escape_string($conn, $location_filter) . "'";
}

$query .= " ORDER BY bintang DESC, harga ASC LIMIT 12";

$hotels = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel - Wisata Lampung</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 2rem;
            margin-bottom: 2rem;
            margin-top: 5rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 10px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 2px;
        }

        .search-filter {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .search-box {
            display: flex;
            margin-bottom: 1rem;
        }

        .search-box input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid var(--gray-light);
            border-radius: 6px 0 0 6px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s;
        }

        .search-box input:focus {
            border-color: var(--primary);
        }

        .search-box button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
            transition: all 0.3s;
        }

        .search-box button:hover {
            background: var(--secondary);
        }

        .filter-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .filter-options select {
            padding: 10px 15px;
            border: 1px solid var(--gray-light);
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s;
        }

        .filter-options select:focus {
            border-color: var(--primary);
        }

        .hotel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .hotel-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .hotel-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .hotel-img {
            height: 200px;
            overflow: hidden;
        }

        .hotel-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s;
        }

        .hotel-card:hover .hotel-img img {
            transform: scale(1.1);
        }

        .hotel-info {
            padding: 1.5rem;
        }

        .hotel-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .hotel-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: var(--gray);
        }

        .hotel-meta i {
            color: var(--primary);
            margin-right: 5px;
        }

        .hotel-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .stars {
            color: var(--warning);
        }

        .hotel-price {
            font-weight: 600;
            color: var(--success);
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .hotel-desc {
            color: var(--gray);
            font-size: 0.95rem;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .btn-detail {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-detail:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3);
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
            display: none;
            grid-column: 1 / -1;
        }

        .main-footer {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 4rem 0 2rem;
            width: 100%;
        }

        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
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
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            opacity: 0.8;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {    
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

        @media (max-width: 576px) {
            .hotel-grid {
                grid-template-columns: 1fr;
            }
            
            .main-header {
                padding: 1rem;
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
        <h1 class="section-title">Hotel di Lampung</h1>
        
        <div class="search-filter">
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Cari hotel...">
                <button type="button" id="search-btn"><i class="fas fa-search"></i></button>
            </div>
            <div class="filter-options">
                <select id="location-filter">
                    <option value="">Semua Lokasi</option>
                    <option value="Bandar Lampung">Bandar Lampung</option>
                    <option value="Lampung Selatan">Lampung Selatan</option>
                    <option value="Lampung Timur">Lampung Timur</option>
                    <option value="Lampung Barat">Lampung Barat</option>
                </select>
                <select id="category-filter">
                    <option value="">Semua Kategori</option>
                    <option value="Hotel">Hotel</option>
                    <option value="Penginapan">Penginapan</option>
                    <option value="Resort">Resort</option>
                </select>
                <select id="rating-filter">
                    <option value="">Semua Rating</option>
                    <option value="5">5 Bintang</option>
                    <option value="4">4 Bintang ke atas</option>
                    <option value="3">3 Bintang ke atas</option>
                </select>
            </div>
        </div>
        
        <div class="hotel-grid" id="hotel-container">
            <?php if (mysqli_num_rows($hotels) > 0) { ?>
                <?php while ($hotel = mysqli_fetch_assoc($hotels)) { ?>
                    <div class="hotel-card" 
                         data-name="<?php echo strtolower(htmlspecialchars($hotel['nama'])); ?>"
                         data-location="<?php echo htmlspecialchars($hotel['lokasi']); ?>"
                         data-category="<?php echo htmlspecialchars($hotel['kategori']); ?>"
                         data-rating="<?php echo $hotel['bintang']; ?>">
                        
                        <div class="hotel-img">
                            <img src="assets/uploads/<?php echo htmlspecialchars($hotel['gambar']); ?>" alt="<?php echo htmlspecialchars($hotel['nama']); ?>">
                        </div>
                        
                        <div class="hotel-info">
                            <h3><?php echo htmlspecialchars($hotel['nama']); ?></h3>
                            
                            <div class="hotel-meta">
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel['lokasi']); ?></span>
                                <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($hotel['kategori']); ?></span>
                            </div>
                            
                            <div class="hotel-rating">
                                <div class="stars">
                                    <?php
                                    $rating = $hotel['bintang'];
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <div class="hotel-price">
                                Rp <?php echo number_format($hotel['harga'], 0, ',', '.'); ?>/malam
                            </div>
                            
                            <p class="hotel-desc"><?php echo htmlspecialchars(substr($hotel['deskripsi'], 0, 100)); ?>...</p>
                            
                            <a href="detail-hotel.php?id=<?php echo $hotel['id']; ?>" class="btn-detail">Lihat Detail</a>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p class="no-results">Tidak ada data hotel</p>
            <?php } ?>
        </div>
        
        <div class="no-results" id="no-results-message" style="display: none;">
            Tidak ditemukan hotel yang sesuai dengan kriteria pencarian.
        </div>
    </main>

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
                <li><a href="index.php#about">Tentang Kami</a></li>
                <li><a href="wisata.php">Tempat Wisata</a></li>
                <li><a href="hotel.php">Hotel</a></li>
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
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const searchBtn = document.getElementById('search-btn');
            const locationFilter = document.getElementById('location-filter');
            const categoryFilter = document.getElementById('category-filter');
            const ratingFilter = document.getElementById('rating-filter');
            const hotelContainer = document.getElementById('hotel-container');
            const hotelCards = document.querySelectorAll('.hotel-card');
            const noResultsMessage = document.getElementById('no-results-message');
            const urlParams = new URLSearchParams(window.location.search);
            const locationParam = urlParams.get('lokasi');
        
            function filterHotels() {
                const searchTerm = searchInput.value.toLowerCase();
                const locationValue = locationFilter.value;
                const categoryValue = categoryFilter.value;
                const ratingValue = ratingFilter.value;
                
                let visibleCount = 0;
                
                hotelCards.forEach(card => {
                    const name = card.dataset.name;
                    const location = card.dataset.location;
                    const category = card.dataset.category;
                    const rating = parseInt(card.dataset.rating);
                    
                    const nameMatch = name.includes(searchTerm);
                    const locationMatch = locationValue === '' || location === locationValue;
                    const categoryMatch = categoryValue === '' || category === categoryValue;
                    const ratingMatch = ratingValue === '' || rating >= parseInt(ratingValue);
                    
                    if (nameMatch && locationMatch && categoryMatch && ratingMatch) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                if (visibleCount === 0) {
                    noResultsMessage.style.display = 'block';
                } else {
                    noResultsMessage.style.display = 'none';
                }
            }
            
            searchInput.addEventListener('input', filterHotels);
            searchBtn.addEventListener('click', filterHotels);
            locationFilter.addEventListener('change', filterHotels);
            categoryFilter.addEventListener('change', filterHotels);
            ratingFilter.addEventListener('change', filterHotels);
            
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
                        
                        document.getElementById('mainNav').classList.remove('active');
                    }
                });
            });
            
            // Get location from URL parameter if exists              
            if (locationParam) {
                // Set the location filter dropdown
                locationFilter.value = locationParam;
                
                // Scroll to the hotel section
                setTimeout(() => {
                    window.scrollTo({
                        top: document.querySelector('.main-content').offsetTop - 80,
                        behavior: 'smooth'
                    });
                }, 100);
            }

            filterHotels();
        });
    </script>
</body>
</html>