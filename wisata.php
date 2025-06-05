<?php
require_once __DIR__ . '/admin/config/database.php';

$popular_wisata = mysqli_query($conn,
    "SELECT w.*, COUNT(u.id) as total_ulasan, AVG(u.rating) as avg_rating
     FROM wisata w
     LEFT JOIN ulasan u ON w.id = u.wisata_id
     GROUP BY w.id
     ORDER BY avg_rating DESC, total_ulasan DESC
     LIMIT 12");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tempat Wisata - Wisata Lampung</title>
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

        .wisata-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .wisata-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .wisata-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .wisata-img {
            height: 200px;
            overflow: hidden;
        }

        .wisata-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s;
        }

        .wisata-card:hover .wisata-img img {
            transform: scale(1.1);
        }

        .wisata-info {
            padding: 1.5rem;
        }

        .wisata-card h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .wisata-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: var(--gray);
        }

        .wisata-meta i {
            color: var(--primary);
            margin-right: 5px;
        }

        .wisata-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .stars {
            color: var(--warning);
        }

        .reviews {
            font-size: 0.9rem;
            color: var(--gray);
        }

        .wisata-desc {
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
            .wisata-grid {
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
            <li><a href="index.php#about">Tentang Kami</a></li>
            <li><a href="wisata.php" class="active">Tempat Wisata</a></li>
            <li><a href="index.php#events">Events</a></li>
        </ul>
        <a href="login.php" class="login-btn">
            <i class="fas fa-sign-in-alt"></i>
            <span>Login</span>
        </a>
    </nav>
</header>

    <main class="main-content">
        <h1 class="section-title">Tempat Wisata di Lampung</h1>
        
        <div class="search-filter">
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Cari tempat wisata...">
                <button type="button" id="search-btn"><i class="fas fa-search"></i></button>
            </div>
            <div class="filter-options">
                <select id="location-filter">
                    <option value="">Semua Lokasi</option>
                    <option value="Bandar Lampung">Bandar Lampung</option>
                    <option value="Lampung Selatan">Lampung Selatan</option>
                    <option value="Lampung Timur">Lampung Timur</option>
                    <option value="Lampung Utara">Lampung Utara</option>
                    <option value="Pesawaran">Pesawaran</option>
                    <option value="Tanggamus">Tanggamus</option>
                </select>
                <select id="category-filter">
                    <option value="">Semua Kategori</option>
                    <option value="Pantai">Pantai</option>
                    <option value="Alam">Alam</option>
                    <option value="Bukit">Bukit</option>
                    <option value="Budaya">Budaya</option>
                </select>
                <select id="rating-filter">
                    <option value="">Semua Rating</option>
                    <option value="5">5 Bintang</option>
                    <option value="4">4 Bintang ke atas</option>
                    <option value="3">3 Bintang ke atas</option>
                </select>
            </div>
        </div>
        
        <div class="wisata-grid" id="wisata-container">
            <?php if (mysqli_num_rows($popular_wisata) > 0) { ?>
                <?php while ($wisata = mysqli_fetch_assoc($popular_wisata)) { ?>
                    <div class="wisata-card" 
                         data-name="<?php echo strtolower(htmlspecialchars($wisata['nama'])); ?>"
                         data-location="<?php echo htmlspecialchars($wisata['lokasi']); ?>"
                         data-category="<?php echo htmlspecialchars($wisata['kategori']); ?>"
                         data-rating="<?php echo round($wisata['avg_rating'] ?? 0); ?>">
                        
                        <div class="wisata-img">
                            <img src="assets/uploads/<?php echo htmlspecialchars($wisata['gambar']); ?>" alt="<?php echo htmlspecialchars($wisata['nama']); ?>">
                        </div>
                        
                        <div class="wisata-info">
                            <h3><?php echo htmlspecialchars($wisata['nama']); ?></h3>
                            
                            <div class="wisata-meta">
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($wisata['lokasi']); ?></span>
                                <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($wisata['kategori']); ?></span>
                            </div>
                            
                            <div class="wisata-rating">
                                <div class="stars">
                                    <?php
                                    $rating = round($wisata['avg_rating'] ?? 0);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                    }
                                    ?>
                                </div>
                                <span class="reviews">(<?php echo $wisata['total_ulasan'] ?? 0; ?> ulasan)</span>
                            </div>
                            
                            <p class="wisata-desc"><?php echo htmlspecialchars(substr($wisata['deskripsi'], 0, 100)); ?>...</p>
                            
                            <a href="detail-wisata.php?id=<?php echo $wisata['id']; ?>" class="btn-detail">Lihat Detail</a>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p class="no-results">Tidak ada data wisata</p>
            <?php } ?>
        </div>
        
        <div class="no-results" id="no-results-message" style="display: none;">
            Tidak ditemukan tempat wisata yang sesuai dengan kriteria pencarian.
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
                <li><a href="index.php#events">Events</a></li>
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
            const wisataContainer = document.getElementById('wisata-container');
            const wisataCards = document.querySelectorAll('.wisata-card');
            const noResultsMessage = document.getElementById('no-results-message');
        
            function filterWisata() {
                const searchTerm = searchInput.value.toLowerCase();
                const locationValue = locationFilter.value;
                const categoryValue = categoryFilter.value;
                const ratingValue = ratingFilter.value;
                
                let visibleCount = 0;
                
                wisataCards.forEach(card => {
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
            
            searchInput.addEventListener('input', filterWisata);
            searchBtn.addEventListener('click', filterWisata);
            locationFilter.addEventListener('change', filterWisata);
            categoryFilter.addEventListener('change', filterWisata);
            ratingFilter.addEventListener('change', filterWisata);
            
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
            
            filterWisata();
        });
    </script>
</body>
</html>