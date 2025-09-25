<?php
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userRole = $isLoggedIn ? $_SESSION['user_role'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxePerfume | Luxury Fragrances</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">LuxePerfume</a>
                </div>
                <nav class="nav">
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="shop.php">Shop</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <?php if ($isLoggedIn && $userRole <= 3): ?>
                            <li><a href="admin/dashboard.php">Admin</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="header-actions">
                    <button class="search-btn" aria-label="Search">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                    <a href="cart.php" class="cart-btn" aria-label="Shopping Cart">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span class="cart-count">0</span>
                    </a>
                    <?php if ($isLoggedIn): ?>
                        <div class="user-menu">
                            <button class="user-btn"><?php echo htmlspecialchars($userName); ?> ▼</button>
                            <div class="user-dropdown">
                                <a href="profile.php">Profile</a>
                                <a href="wishlist.php">Wishlist</a>
                                <a href="orders.php">Orders</a>
                                <a href="logout.php">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="auth-btn">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Search Modal -->
    <div class="search-modal" id="searchModal">
        <div class="search-modal-content">
            <span class="close-search">&times;</span>
            <form class="search-form">
                <input type="text" placeholder="Search for fragrances..." id="searchInput">
                <button type="submit">Search</button>
            </form>
            <div class="search-results" id="searchResults"></div>
        </div>
    </div>