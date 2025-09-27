<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';

$auth = new Auth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Policy | ATK Perfumes</title>
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
        }

        .btn-primary {
            background: linear-gradient(45deg, #a66d30, #ffe58e, #e0b057);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
            color: #0a0a0a;
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(166, 109, 48, 0.4);
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
            padding: 100px 0 1px;
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

        /* Content Section */
        .content-section {
            padding: 1rem 0;
        }

        .content-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--gradient-gold);
        }

        .policy-content {
            background: var(--white);
            padding: 3rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .policy-section {
            margin-bottom: 3rem;
        }

        .policy-section:last-child {
            margin-bottom: 0;
        }

        .policy-section h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--gold);
            font-family: 'Cinzel', serif;
        }

        .policy-section p {
            margin-bottom: 1.5rem;
            color: var(--muted);
        }

        .policy-section ul {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }

        .policy-section li {
            margin-bottom: 0.5rem;
            color: var(--muted);
        }

        .info-box {
            background: linear-gradient(135deg, #f9f5f0 0%, #f0e6d6 100%);
            border-left: 4px solid var(--gold);
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 0 var(--radius) var(--radius) 0;
        }

        .info-box h4 {
            color: var(--gold);
            margin-bottom: 0.5rem;
            font-family: 'Cinzel', serif;
        }

        .shipping-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .shipping-option {
            background: var(--light-gray);
            padding: 2rem;
            border-radius: var(--radius);
            text-align: center;
            transition: var(--transition);
        }

        .shipping-option:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .shipping-option i {
            font-size: 2.5rem;
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .shipping-option h4 {
            font-family: 'Cinzel', serif;
            margin-bottom: 0.5rem;
            color: var(--black);
        }

        .timeline {
            position: relative;
            margin: 2rem 0;
        }

        .timeline:before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--gradient-gold);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
            padding-left: 3rem;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: 15px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--gold);
            border: 2px solid var(--white);
            box-shadow: 0 0 0 3px var(--gold);
        }

        .timeline-title {
            font-family: 'Cinzel', serif;
            color: var(--gold);
            margin-bottom: 0.5rem;
        }

        .last-updated {
            text-align: center;
            margin-top: 3rem;
            font-style: italic;
            color: var(--muted);
            border-top: 1px solid var(--light-gray);
            padding-top: 2rem;
        }

        /* Footer */
        .footer {
            background-color: var(--black);
            color: var(--white);
            padding: 4rem 0 2rem;
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
            
            .policy-content {
                padding: 2rem;
            }
            
            .shipping-options {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .content-section {
                padding: 4rem 0;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .page-header {
                padding: 150px 0 80px;
            }
            
            .timeline:before {
                left: 15px;
            }
            
            .timeline-item {
                padding-left: 2.5rem;
            }
            
            .timeline-item:before {
                left: 10px;
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
                        <li><a href="index.php#products" class="nav-link">Collections</a></li>
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
                <h1 class="page-title">Shipping Policy</h1>
                <p class="page-subtitle">We ensure your fragrances arrive safely and offer a seamless return experience if needed.</p>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="content-section">
        <div class="container">
            <div class="content-container">
                <div class="policy-content fade-in">
                    <!-- Shipping Information -->
                    <div class="policy-section">
                        <h3>Shipping Policy Information</h3>
                        <p>At ATK Perfumes, we take great care in packaging and shipping your luxury fragrances to ensure they arrive in perfect condition. All orders are processed within 1-2 business days.</p>
                        
                        <div class="shipping-options">
                            <div class="shipping-option">
                                <i class="fas fa-shipping-fast"></i>
                                <h4>Standard Shipping</h4>
                                <p>3-5 business days</p>
                            </div>
                            <div class="shipping-option">
                                <i class="fas fa-rocket"></i>
                                <h4>Express Shipping</h4>
                                <p>2-3 business days</p>
                            </div>
                            <div class="shipping-option">
                                <i class="fas fa-crown"></i>
                                <h4>Premium Delivery</h4>
                                <p>Next business day</p>
                            </div>
                            <div class="shipping-option">
                                <i class="fas fa-piggy-bank"></i>
                                <h4>Budget Friendly</h4>
                                <p>Affordable & reliable</p>
                            </div>
                        </div>
                        
                        <div class="info-box">
                            <h4>Free Shipping</h4>
                            <p>Enjoy free standard shipping on all orders over $75. This offer applies to domestic shipments within the United States.</p>
                        </div>
                    </div>

                    <!-- Delivery Times -->
                    <div class="policy-section">
                        <h3>Delivery Times & Tracking</h3>
                        <p>Once your order ships, you will receive a tracking number via email. Our delivery times vary based on your location and the shipping method selected.</p>
                        
                        <div class="timeline">
                            <div class="timeline-item">
                                <h4 class="timeline-title">Order Processing</h4>
                                <p>1-2 business days after order placement</p>
                            </div>
                            <div class="timeline-item">
                                <h4 class="timeline-title">In Transit</h4>
                                <p>Time varies based on shipping method selected</p>
                            </div>
                            <div class="timeline-item">
                                <h4 class="timeline-title">Delivery</h4>
                                <p>Signature may be required for high-value orders</p>
                            </div>
                        </div>
                        
                        <p>Please note that delivery times are estimates and not guaranteed. During peak seasons or adverse weather conditions, deliveries may experience delays.</p>
                    </div>

                    <!-- International Shipping -->
                    <div class="policy-section">
                        <h3>International Shipping</h3>
                        <p>We currently ship to select international destinations. International orders may be subject to import duties, taxes, and customs fees, which are the responsibility of the recipient.</p>
                        
                        <h4>Available Regions:</h4>
                        <ul>
                            <li>Canada: 7-10 business days - $15.95</li>
                            <li>European Union: 10-14 business days - $24.95</li>
                            <li>United Kingdom: 10-14 business days - $22.95</li>
                            <li>Australia & New Zealand: 12-16 business days - $29.95</li>
                        </ul>
                        
                        <div class="info-box">
                            <h4>Important Note</h4>
                            <p>Due to international shipping regulations, some fragrance ingredients may be restricted in certain countries. Please check your local customs regulations before ordering.</p>
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
                        <li class="footer-link"><a href="index.php#products">For Men</a></li>
                        <li class="footer-link"><a href="index.php#products">For Women</a></li>
                        <li class="footer-link"><a href="index.php#products">Unisex</a></li>
                        <li class="footer-link"><a href="index.php#products">Limited Edition</a></li>
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
                        <li class="footer-link"><a href="http://localhost/ATK-Perfumes/contact.php">Contact Us</a></li>
                        <li class="footer-link"><a href="http://localhost/ATK-Perfumes/shipping.php">Shipping Policy</a></li>
                        <li class="footer-link"><a href="http://localhost/ATK-Perfumes/faq.php">FAQ</a></li>
                        <li class="footer-link"><a href="http://localhost/ATK-Perfumes/privacy.php">Privacy Policy</a></li>
                        <li class="footer-link"><a href="http://localhost/ATK-Perfumes/terms.php">Terms & Conditions</a></li>
                        <li class="footer-link"><a href="http://localhost/ATK-Perfumes/refund.php">Return & Refund Policy</a></li>
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
        });
    </script>
</body>
</html>