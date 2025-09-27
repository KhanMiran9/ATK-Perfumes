<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$database = new Database();
$conn = $database->getConnection();

// Get filters from query string
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$minPrice = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$maxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query with filters
$query = "SELECT SQL_CALC_FOUND_ROWS p.id, p.name, p.slug, p.short_desc, 
                 pv.price, pv.sale_price, pm.file_path as image,
                 c.name as category_name
          FROM products p
          LEFT JOIN product_variations pv ON p.id = pv.product_id AND pv.is_default = 1
          LEFT JOIN product_media pm ON p.id = pm.product_id AND pm.sort_order = 0
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE p.is_active = 1";

$params = [];

if (!empty($category)) {
    $query .= " AND c.slug = :category";
    $params[':category'] = $category;
}

if (!empty($search)) {
    $query .= " AND (p.name LIKE :search OR p.short_desc LIKE :search OR p.long_desc LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($minPrice) && is_numeric($minPrice)) {
    $query .= " AND pv.price >= :min_price";
    $params[':min_price'] = $minPrice;
}

if (!empty($maxPrice) && is_numeric($maxPrice)) {
    $query .= " AND pv.price <= :max_price";
    $params[':max_price'] = $maxPrice;
}

// Add sorting
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY IF(pv.sale_price > 0, pv.sale_price, pv.price) ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY IF(pv.sale_price > 0, pv.sale_price, pv.price) DESC";
        break;
    case 'name_asc':
        $query .= " ORDER BY p.name ASC";
        break;
    case 'name_desc':
        $query .= " ORDER BY p.name DESC";
        break;
    default:
        $query .= " ORDER BY p.created_at DESC";
        break;
}

$query .= " LIMIT :limit OFFSET :offset";

// Prepare and execute query
$stmt = $conn->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$totalResult = $conn->query("SELECT FOUND_ROWS()")->fetch(PDO::FETCH_COLUMN);
$totalPages = ceil($totalResult / $limit);

// Get categories for filter sidebar
$categoryQuery = "SELECT id, name, slug FROM categories WHERE parent_id IS NULL";
$categoryStmt = $conn->prepare($categoryQuery);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// Random images for products (if no image is available)
$randomImages = [
    'perfume1.jpg',
    'perfume2.jpg',
    'perfume3.jpg',
    'perfume4.jpg',
    'perfume5.jpg',
    'perfume6.jpg'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop | ATK Perfumes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --gold: #d4af37;
            --silver: #c0c0c0;
            --black: #0a0a0a;
            --dark-gray: #1a1a1a;
            --light-gray: #f5f5f5;
            --white: #ffffff;
            --muted: #8a8580;
            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            --radius: 8px;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.2);
            --gradient-gold: linear-gradient(45deg, #a66d30, #ffe58e 50%, #e0b057 100%);
            --gradient-silver: linear-gradient(45deg, #7f7f7f, #d9d9d9 50%, #a6a6a6 100%);
            --button-background: 166, 109, 48;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            color: var(--black);
            line-height: 1.6;
            background-color: var(--white);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Cinzel', serif;
            font-weight: 600;
            line-height: 1.2;
        }

        .container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 2.5rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            font-family: 'Cinzel', serif;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            border: none;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(45deg, #a66d30, #ffe58e, #e0b057);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
            color: #0a0a0a;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(166, 109, 48, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: var(--black);
            border: 1px solid var(--black);
        }

        .btn-secondary:hover {
            background: var(--black);
            color: var(--white);
        }

        /* Header Styles */
        .header {
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            position: fixed;
            background: linear-gradient(90deg, #7F6051, #b58c65, #f3dab3, #b58c65, #7F6051);
            background-size: 300% 100%;
            animation: shine 20s linear infinite;
            top: 78px;
            left: 30px;
            width: 95%;
            z-index: 1000;
            padding: 0.5rem 0;
            transition: var(--transition);
            background-color: transparent;
        }
        @media(max-width: 500px) {
            .header {
                top: 71px;
                left: 11px;
            }
        }

        .header.scrolled {
            border-radius:20px;
            position: fixed;
            top: 30px;
            background-color: rgba(255, 255, 255, 0.98);
            padding: 1rem 0;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-family: 'Cinzel', serif;
            font-size: 2rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            letter-spacing: 2px;
        }

        .logo span {
            color: var(--gold);
        }

        .nav {
            display: flex;
            align-items: center;
        }

        .nav-list {
            display: flex;
            list-style: none;
            gap: 2.5rem;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
            font-family: 'Cinzel', serif;
            letter-spacing: 1px;
        }

        .nav-link:after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient-gold);
            transition: var(--transition);
        }

        .nav-link:hover:after {
            width: 100%;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .header-action {
            color:white;
            font-size: 1.2rem;
            transition: var(--transition);
            position: relative;
        }

        .header-action:hover {
            color: var(--gold);
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--gradient-gold);
            color: var(--black);
            font-size: 0.7rem;
            font-weight: 600;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-toggle {
            display: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Page Header */
        .page-header {
            position: relative;
            padding: 100px 0 50px;
            background: linear-gradient(135deg, var(--light-gray) 0%, var(--white) 100%);
            text-align: center;
            overflow: hidden;
        }

        .page-header-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .page-title {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            background: linear-gradient(45deg, var(--gold), var(--silver));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            font-size: 1.2rem;
            color: var(--muted);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Shop Layout */
        .shop-layout {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }

        /* Filters Sidebar */
        .shop-filters {
            width: 280px;
            flex-shrink: 0;
            background: var(--white);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            height: fit-content;
            position: sticky;
            top: 150px;
        }

        .shop-filters h3 {
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .shop-filters h3:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--gradient-gold);
        }

        .filter-group {
            margin-bottom: 2rem;
        }

        .filter-group h4 {
            margin-bottom: 1rem;
            font-size: 1.1rem;
            color: var(--dark-gray);
        }

        .filter-list {
            list-style: none;
        }

        .filter-list li {
            margin-bottom: 0.5rem;
        }

        .filter-checkbox {
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: var(--transition);
            padding: 0.25rem 0;
        }

        .filter-checkbox:hover {
            color: var(--gold);
        }

        .filter-checkbox input {
            margin-right: 0.75rem;
            width: 18px;
            height: 18px;
            accent-color: var(--gold);
        }

        .price-range {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .price-range input {
            width: 100px;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Montserrat', sans-serif;
        }

        .price-range span {
            color: var(--muted);
        }

        .shop-filters .btn {
            width: 100%;
            margin-bottom: 0.75rem;
            padding: 0.75rem;
            font-size: 0.85rem;
        }

        /* Shop Main Content */
        .shop-main {
            flex: 1;
        }

        .shop-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .results-count {
            font-weight: 500;
            color: var(--muted);
        }

        .sort-options select {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Montserrat', sans-serif;
            background: var(--white);
            cursor: pointer;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .product-card {
            background: var(--white);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .product-image {
            position: relative;
            overflow: hidden;
            height: 300px;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }

        .product-card:hover .product-overlay {
            opacity: 1;
        }

        .quick-view, .add-wishlist {
            background: var(--white);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            margin: 0 0.5rem;
        }

        .quick-view:hover {
            background: var(--gold);
            color: var(--white);
        }

        .add-wishlist {
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .add-wishlist:hover {
            background: var(--gold);
            color: var(--white);
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-category {
            font-size: 0.8rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            display: block;
        }

        .product-name {
            font-size: 1.2rem;
            margin-bottom: 0.75rem;
            font-family: 'Cinzel', serif;
        }

        .product-price {
            margin-bottom: 1rem;
        }

        .current-price, .sale-price {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--gold);
        }

        .original-price {
            text-decoration: line-through;
            color: var(--muted);
            margin-left: 0.5rem;
            font-size: 0.9rem;
        }

        .product-actions {
            display: flex;
            gap: 0.75rem;
        }

        .product-actions .btn {
            flex: 1;
            padding: 0.75rem;
            font-size: 0.8rem;
        }

        /* No Products */
        .no-products {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
        }

        .no-products h3 {
            margin-bottom: 1rem;
            color: var(--muted);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--white);
            color: var(--black);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .page-link:hover, .page-link.active {
            background: var(--gradient-gold);
            color: var(--black);
        }

        /* Footer */
        .footer {
            background-color: var(--black);
            color: var(--white);
            padding: 4rem 0 2rem;
            margin-top: 4rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-brand {
            grid-column: 1 / -1;
            text-align: center;
            margin-bottom: 2rem;
        }

        .footer-logo {
            font-family: 'Cinzel', serif;
            font-size: 2rem;
            color: var(--white);
            text-decoration: none;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .footer-logo span {
            color: var(--gold);
        }

        .footer-description {
            color: var(--muted);
            max-width: 400px;
            margin: 0 auto;
        }

        .footer-heading {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
            font-family: 'Cinzel', serif;
        }

        .footer-heading:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 30px;
            height: 2px;
            background: var(--gradient-gold);
        }

        .footer-links {
            list-style: none;
        }

        .footer-link {
            margin-bottom: 0.75rem;
        }

        .footer-link a {
            color: var(--muted);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-link a:hover {
            color: var(--gold);
        }

        .footer-social {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--white);
            transition: var(--transition);
        }

        .social-link:hover {
            background: var(--gradient-gold);
            color: var(--black);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--muted);
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .page-title {
                font-size: 3rem;
            }
            
            .shop-layout {
                flex-direction: column;
            }
            
            .shop-filters {
                width: 100%;
                position: static;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1.5rem;
            }
            
            .header {
                padding: 0.5rem 0;
            }
            
            .nav {
                position: fixed;
                top: 0;
                right: -100%;
                width: 280px;
                height: 100vh;
                background-color: var(--white);
                box-shadow: var(--shadow-lg);
                transition: var(--transition);
                z-index: 1001;
                padding: 2rem;
                flex-direction: column;
                align-items: flex-start;
            }
            
            .nav.active {
                right: 0;
            }
            
            .nav-list {
                flex-direction: column;
                gap: 1.5rem;
                margin-bottom: 2rem;
            }
            
            .mobile-toggle {
                display: block;
                z-index: 1002;
            }
            
            .page-title {
                font-size: 2.5rem;
            }
            
            .shop-toolbar {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .page-header {
                padding: 150px 0 50px;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .product-actions {
                flex-direction: column;
            }
        }

        /* Loading Animation */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .loader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader-content {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
        }

        .loader-gif {
            width: 150px;
            height: 150px;
            object-fit: contain;
            border-radius: 50%;
        }

        .loader-brand {
            font-family: 'Cinzel', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--gold);
            letter-spacing: 3px;
            text-align: center;
        }

        .loader-brand span {
            color: var(--black);
        }

        @media (max-width: 768px) {
            .loader-gif {
                width: 120px;
                height: 120px;
            }
            
            .loader-brand {
                font-size: 2rem;
            }
        }

        /* Animations */
        @keyframes shine {
            0% {
                background-position: 0 200%;
            }
            100% {
                background-position: 0 -200%;
            }
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Loading Animation -->
    <div class="loader">
        <div class="loader-content">
            <img src="assets/images/perfume.gif" alt="ATK Perfumes Loading" class="loader-gif">
            <div class="loader-brand">ATK Perfumes</div>
        </div>
    </div>
    
    <?php include 'annountment-bar.php'; ?>    
    
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">ATK</a>
                
                <nav class="nav">
                    <ul class="nav-list">
                        <li><a href="index.php" class="nav-link">Home</a></li>
                        <li><a href="shop.php" class="nav-link active">Shop</a></li>
                        <li><a href="about.php" class="nav-link">About Us</a></li>
                        <li><a href="index.php#ingredients" class="nav-link">Ingredients</a></li>
                        <li><a href="index.php#testimonials" class="nav-link">Testimonials</a></li>
                    </ul>
                </nav>
                
                <div class="header-actions">
                    <a href="#" class="header-action">
                        <i class="fas fa-search"></i>
                    </a>
                    <a href="#" class="header-action" style="position: relative;">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-count">0</span>
                    </a>
                    <a href="#" class="header-action">
                        <i class="fas fa-user"></i>
                    </a>
                    <div class="mobile-toggle">
                        <i class="fas fa-bars"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1 class="page-title">Our Fragrance Collection</h1>
                <p class="page-subtitle">Discover our exquisite range of luxury perfumes crafted with the finest ingredients</p>
            </div>
        </div>
    </section>

    <!-- Shop Content -->
    <section class="content-section">
        <div class="container">
            <div class="shop-layout">
                <!-- Filters Sidebar -->
                <aside class="shop-filters">
                    <h3>Filters</h3>
                    
                    <div class="filter-group">
                        <h4>Categories</h4>
                        <ul class="filter-list">
                            <li>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="category" value="" <?php echo empty($category) ? 'checked' : ''; ?>>
                                    <span>All Categories</span>
                                </label>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                            <li>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="category" value="<?php echo $cat['slug']; ?>" 
                                        <?php echo $category === $cat['slug'] ? 'checked' : ''; ?>>
                                    <span><?php echo $cat['name']; ?></span>
                                </label>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="filter-group">
                        <h4>Price Range</h4>
                        <div class="price-range">
                            <input type="number" placeholder="Min" name="min_price" value="<?php echo $minPrice; ?>">
                            <span>-</span>
                            <input type="number" placeholder="Max" name="max_price" value="<?php echo $maxPrice; ?>">
                        </div>
                    </div>

                    <button class="btn btn-primary apply-filters">Apply Filters</button>
                    <button class="btn btn-secondary clear-filters">Clear All</button>
                </aside>

                <!-- Products Grid -->
                <main class="shop-main">
                    <div class="shop-toolbar">
                        <div class="results-count">
                            Showing <?php echo count($products); ?> of <?php echo $totalResult; ?> products
                        </div>
                        <div class="sort-options">
                            <select id="sortSelect">
                                <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                                <option value="name_desc" <?php echo $sort === 'name_desc' ? 'selected' : ''; ?>>Name: Z to A</option>
                            </select>
                        </div>
                    </div>

                    <div class="products-grid">
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $product): 
                                // Use product image if available, otherwise use random image
                                $imagePath = $product['image'] ? 'assets/uploads/products/' . $product['image'] : 'assets/images/' . $randomImages[array_rand($randomImages)];
                            ?>
                            <div class="product-card fade-in">
                                <div class="product-image">
                                    <img src="<?php echo $imagePath; ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="product-overlay">
                                        <button class="quick-view" data-product-id="<?php echo $product['id']; ?>">Quick View</button>
                                        <button class="add-wishlist" data-product-id="<?php echo $product['id']; ?>">♡</button>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <span class="product-category"><?php echo $product['category_name']; ?></span>
                                    <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="product-desc"><?php echo htmlspecialchars($product['short_desc']); ?></p>
                                    <div class="product-price">
                                        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
                                            <span class="sale-price">$<?php echo number_format($product['sale_price'], 2); ?></span>
                                            <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                                        <?php else: ?>
                                            <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-actions">
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary">View Details</a>
                                        <button class="btn btn-primary add-to-cart" 
                                                data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-products">
                                <h3>No products found</h3>
                                <p>Try adjusting your filters or search terms</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="page-link">Previous</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                               class="page-link <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="page-link">Next</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="index.php" class="footer-logo">ATK<span>Perfumes</span></a>
                    <p class="footer-description">Crafting timeless fragrances with the world's finest ingredients.</p>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-heading">Collections</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="shop.php?category=men">For Men</a></li>
                        <li class="footer-link"><a href="shop.php?category=women">For Women</a></li>
                        <li class="footer-link"><a href="shop.php?category=unisex">Unisex</a></li>
                        <li class="footer-link"><a href="shop.php?category=limited">Limited Edition</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-heading">Company</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="about.php">Our Story</a></li>
                        <li class="footer-link"><a href="about.php#sustainability">Sustainability</a></li>
                        <li class="footer-link"><a href="about.php#press">Press</a></li>
                        <li class="footer-link"><a href="careers.php">Careers</a></li>
                    </ul>
                </div>
                
               <div class="footer-col">
                    <h3 class="footer-heading">Support</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="contact.php">Contact Us</a></li>
                        <li class="footer-link"><a href="shipping.php">Shipping Policy</a></li>
                        <li class="footer-link"><a href="faq.php">FAQ</a></li>
                        <li class="footer-link"><a href="privacy.php">Privacy Policy</a></li>
                        <li class="footer-link"><a href="terms.php">Terms & Conditions</a></li>
                        <li class="footer-link"><a href="refund.php">Return & Refund Policy</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-heading">Connect</h3>
                    <p>Follow us for updates and behind-the-scenes content.</p>
                    <div class="footer-social">
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-pinterest"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> ATK Perfumes. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/ScrollTrigger.min.js"></script>
    <script>
        // Wait for DOM to load
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loader after page loads
            setTimeout(function() {
                document.querySelector('.loader').classList.add('hidden');
            }, 1500);
            
            // Mobile menu toggle
            const mobileToggle = document.querySelector('.mobile-toggle');
            const nav = document.querySelector('.nav');
            
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    nav.classList.toggle('active');
                    mobileToggle.innerHTML = nav.classList.contains('active') ? 
                        '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
                });
            }
            
            // Header scroll effect
            const header = document.querySelector('.header');
            
            window.addEventListener('scroll', function() {
                if (window.scrollY > 100) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });
            
            // Filter functionality
            const applyFiltersBtn = document.querySelector('.apply-filters');
            const clearFiltersBtn = document.querySelector('.clear-filters');
            const sortSelect = document.getElementById('sortSelect');
            
            applyFiltersBtn.addEventListener('click', function() {
                const urlParams = new URLSearchParams(window.location.search);
                
                // Get category
                const categoryCheckboxes = document.querySelectorAll('input[name="category"]:checked');
                if (categoryCheckboxes.length > 0) {
                    // Use the last checked category (single selection)
                    urlParams.set('category', categoryCheckboxes[categoryCheckboxes.length - 1].value);
                } else {
                    urlParams.delete('category');
                }
                
                // Get price range
                const minPrice = document.querySelector('input[name="min_price"]').value;
                const maxPrice = document.querySelector('input[name="max_price"]').value;
                
                if (minPrice) {
                    urlParams.set('min_price', minPrice);
                } else {
                    urlParams.delete('min_price');
                }
                
                if (maxPrice) {
                    urlParams.set('max_price', maxPrice);
                } else {
                    urlParams.delete('max_price');
                }
                
                // Reset to page 1 when applying new filters
                urlParams.set('page', '1');
                
                window.location.href = 'shop.php?' + urlParams.toString();
            });
            
            clearFiltersBtn.addEventListener('click', function() {
                window.location.href = 'shop.php';
            });
            
            sortSelect.addEventListener('change', function() {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('sort', this.value);
                urlParams.set('page', '1'); // Reset to page 1 when changing sort
                window.location.href = 'shop.php?' + urlParams.toString();
            });
            
            // Initialize GSAP ScrollTrigger
            if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
                gsap.registerPlugin(ScrollTrigger);
                
                // Fade-in animation for elements
                const fadeElements = document.querySelectorAll('.fade-in');
                
                fadeElements.forEach(element => {
                    gsap.to(element, {
                        opacity: 1,
                        y: 0,
                        duration: 1,
                        scrollTrigger: {
                            trigger: element,
                            start: 'top 85%',
                            toggleActions: 'play none none none'
                        }
                    });
                });
            }
            
            // Add to cart functionality
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    // Simple cart functionality - in a real app, this would be an AJAX call
                    const cartCount = document.querySelector('.cart-count');
                    let count = parseInt(cartCount.textContent) || 0;
                    count++;
                    cartCount.textContent = count;
                    
                    // Show a simple notification
                    const notification = document.createElement('div');
                    notification.textContent = 'Product added to cart!';
                    notification.style.position = 'fixed';
                    notification.style.bottom = '20px';
                    notification.style.right = '20px';
                    notification.style.background = 'var(--gradient-gold)';
                    notification.style.color = 'var(--black)';
                    notification.style.padding = '1rem 2rem';
                    notification.style.borderRadius = '50px';
                    notification.style.zIndex = '10000';
                    notification.style.fontWeight = '600';
                    notification.style.boxShadow = 'var(--shadow)';
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                });
            });
            
            // Quick view and wishlist functionality (placeholder)
            const quickViewButtons = document.querySelectorAll('.quick-view');
            quickViewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    alert('Quick view feature would open a modal with product details.');
                });
            });
            
            const wishlistButtons = document.querySelectorAll('.add-wishlist');
            wishlistButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.textContent = this.textContent === '♡' ? '♥' : '♡';
                });
            });
        });
    </script>
</body>
</html>