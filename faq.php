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
    <title>Frequently Asked Questions | ATK Perfumes</title>
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
            transition: var(--transition);
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
            padding: 180px 0 100px;
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

        /* FAQ Content Section */
        .content-section {
            padding: 6rem 0;
        }

        .content-container {
            max-width: 900px;
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

        /* FAQ Accordion Styles */
        .faq-container {
            background: var(--white);
            padding: 3rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .faq-category {
            margin-bottom: 3rem;
        }

        .faq-category:last-child {
            margin-bottom: 0;
        }

        .category-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--gold);
            font-family: 'Cinzel', serif;
            border-bottom: 2px solid var(--light-gray);
            padding-bottom: 0.5rem;
        }

        .faq-item {
            margin-bottom: 1rem;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
        }

        .faq-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .faq-question {
            background: var(--light-gray);
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            transition: var(--transition);
        }

        .faq-question:hover {
            background: rgba(212, 175, 55, 0.1);
        }

        .faq-question.active {
            background: rgba(212, 175, 55, 0.15);
            color: var(--gold);
        }

        .faq-icon {
            transition: var(--transition);
            color: var(--gold);
        }

        .faq-question.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
            background: var(--white);
        }

        .faq-answer-content {
            padding: 1.5rem;
            border-top: 1px solid var(--light-gray);
        }

        .faq-answer p {
            margin-bottom: 1rem;
            color: var(--muted);
        }

        .faq-answer p:last-child {
            margin-bottom: 0;
        }

        .faq-answer ul {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }

        .faq-answer li {
            margin-bottom: 0.5rem;
            color: var(--muted);
        }

        /* FAQ Search and Navigation */
        .faq-search-container {
            margin-bottom: 3rem;
            position: relative;
        }

        .faq-search {
            width: 100%;
            padding: 1.2rem 1.5rem;
            border: 2px solid var(--light-gray);
            border-radius: 50px;
            font-size: 1rem;
            transition: var(--transition);
            font-family: 'Montserrat', sans-serif;
        }

        .faq-search:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
        }

        .faq-search-icon {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
        }

        .faq-navigation {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .nav-btn {
            padding: 0.75rem 1.5rem;
            background: var(--light-gray);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
        }

        .nav-btn:hover {
            background: rgba(212, 175, 55, 0.1);
            color: var(--gold);
        }

        .nav-btn.active {
            background: var(--gradient-gold);
            color: var(--black);
            font-weight: 600;
        }

        /* Contact CTA */
        .contact-cta {
            text-align: center;
            padding: 3rem;
            background: linear-gradient(135deg, var(--light-gray) 0%, var(--white) 100%);
            border-radius: var(--radius);
            margin-top: 4rem;
        }

        .contact-cta h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--gold);
        }

        .contact-cta p {
            margin-bottom: 2rem;
            color: var(--muted);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
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
            
            .faq-container {
                padding: 2rem;
            }
            
            .faq-navigation {
                flex-direction: column;
                align-items: center;
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
            
            .faq-question {
                padding: 1rem;
            }
            
            .faq-answer-content {
                padding: 1rem;
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
                <h1 class="page-title">Frequently Asked Questions</h1>
                <p class="page-subtitle">Find answers to common questions about our products, ordering process, and more.</p>
            </div>
        </div>
    </section>

    <!-- FAQ Content Section -->
    <section class="content-section">
        <div class="container">
            <div class="content-container">
                <div class="section-header">
                    <h2 class="section-title">How Can We Help?</h2>
                    <p>Browse through our frequently asked questions or search for specific information</p>
                </div>
                
                <div class="faq-search-container">
                    <input type="text" class="faq-search" placeholder="Search for answers...">
                    <i class="fas fa-search faq-search-icon"></i>
                </div>
                
                <div class="faq-navigation">
                    <button class="nav-btn active" data-category="all">All Questions</button>
                    <button class="nav-btn" data-category="products">Products</button>
                    <button class="nav-btn" data-category="ordering">Ordering & Payment</button>
                    <button class="nav-btn" data-category="shipping">Shipping Policy</button>
                    <button class="nav-btn" data-category="account">Account & Support</button>
                </div>
                
                <div class="faq-container fade-in">
                    <!-- Products Category -->
                    <div class="faq-category" data-category="products">
                        <h3 class="category-title">Products</h3>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>What makes ATK Perfumes unique?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>ATK Perfumes are crafted with the finest ingredients sourced from around the world. Our unique formulations combine traditional perfumery techniques with modern innovation, creating distinctive scents that evolve beautifully throughout the day. Each fragrance tells a story and is designed to become a signature scent for our customers.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>Are your perfumes cruelty-free?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>Yes, all ATK Perfumes are 100% cruelty-free. We do not test our products on animals, and we ensure that our suppliers adhere to the same ethical standards. We are committed to creating luxurious fragrances without compromising our values.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>How long does the fragrance last?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>The longevity of our fragrances varies depending on the specific scent and your skin chemistry. On average, our perfumes last 6-8 hours. Eau de Parfum concentrations tend to last longer than Eau de Toilette. For maximum longevity, we recommend applying to pulse points and moisturized skin.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>Do you offer samples before purchasing a full bottle?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>Yes, we offer sample sets that allow you to try multiple fragrances before committing to a full bottle. Our Discovery Sets include 5-7 miniature vials of our most popular scents. This is a great way to find your perfect fragrance match.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ordering & Payment Category -->
                    <div class="faq-category" data-category="ordering">
                        <h3 class="category-title">Ordering & Payment</h3>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>What payment methods do you accept?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>We accept all major credit cards (Visa, MasterCard, American Express), PayPal, Apple Pay, and Google Pay. All transactions are securely processed through our encrypted payment gateway to ensure your information is protected.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>Can I modify or cancel my order after placing it?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>Orders can be modified or canceled within 1 hour of placement, as long as they haven't entered the fulfillment process. Please contact our customer service team immediately if you need to make changes to your order. After this window, modifications may not be possible.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>Do you offer gift wrapping and personalized messages?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>Yes, we offer complimentary gift wrapping and the option to include a personalized message with your order. Simply select the gift wrapping option at checkout and add your message in the provided field. This service is available for all orders.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Policy Category -->
                    <div class="faq-category" data-category="shipping">
                        <h3 class="category-title">Shipping Policy</h3>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>What are your shipping options and costs?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>We offer several shipping options:</p>
                                    <ul>
                                        <li>Standard Shipping (5-7 business days): $5.99 or free on orders over $75</li>
                                        <li>Express Shipping (2-3 business days): $12.99</li>
                                        <li>Overnight Shipping: $24.99 (order by 12 PM EST for next-day delivery)</li>
                                    </ul>
                                    <p>International shipping rates vary by destination.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>What is your return policy?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>We offer a 30-day return policy for unused products in their original packaging. For hygiene reasons, we cannot accept returns on opened fragrance products unless they are defective. If you receive a defective product, please contact us within 7 days of delivery for a replacement or refund.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>Do you ship internationally?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>Yes, we ship to over 50 countries worldwide. International shipping costs and delivery times vary by destination. Please note that customers are responsible for any customs duties, taxes, or import fees that may apply in their country.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Account & Support Category -->
                    <div class="faq-category" data-category="account">
                        <h3 class="category-title">Account & Support</h3>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>How do I create an account?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>You can create an account by clicking on the "Account" icon in the top navigation and selecting "Register." Alternatively, an account will be automatically created when you place your first order. Having an account allows you to track orders, save your favorite products, and receive exclusive offers.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>I forgot my password. How can I reset it?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>Click on the "Account" icon and select "Forgot Password." Enter the email address associated with your account, and we'll send you a link to reset your password. If you don't receive the email within 10 minutes, please check your spam folder or contact our support team.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>How can I contact customer service?</span>
                                <i class="fas fa-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>Our customer service team is available Monday-Friday, 9 AM to 6 PM EST. You can reach us by:</p>
                                    <ul>
                                        <li>Email: support@atkperfumes.com</li>
                                        <li>Phone: +1 (555) 123-4567</li>
                                        <li>Live Chat: Available on our website during business hours</li>
                                    </ul>
                                    <p>We typically respond to emails within 24 hours.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="contact-cta fade-in">
                    <h3>Still Have Questions?</h3>
                    <p>If you couldn't find the answer you were looking for, our customer service team is here to help.</p>
                    <a href="contact.php" class="btn btn-primary">Contact Us</a>
                </div>
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
                        <li class="footer-link"><a href="contact.php">Contact Us</a></li>
                        <li class="footer-link"><a href="shipping.php">Shipping Policy</a></li>
                        <li class="footer-link"><a href="faq.php">FAQ</a></li>
                        <li class="footer-link"><a href="privacy.php">Privacy Policy</a></li>
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
            
            // FAQ Accordion Functionality
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const answer = this.nextElementSibling;
                    const isActive = this.classList.contains('active');
                    
                    // Close all other FAQs
                    document.querySelectorAll('.faq-question').forEach(q => {
                        if (q !== this) {
                            q.classList.remove('active');
                            q.nextElementSibling.style.maxHeight = null;
                        }
                    });
                    
                    // Toggle current FAQ
                    if (isActive) {
                        this.classList.remove('active');
                        answer.style.maxHeight = null;
                    } else {
                        this.classList.add('active');
                        answer.style.maxHeight = answer.scrollHeight + "px";
                    }
                });
            });
            
            // FAQ Search Functionality
            const searchInput = document.querySelector('.faq-search');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const faqItems = document.querySelectorAll('.faq-item');
                
                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question span').textContent.toLowerCase();
                    const answer = item.querySelector('.faq-answer-content').textContent.toLowerCase();
                    
                    if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                        item.style.display = 'block';
                        
                        // Highlight matching text
                        if (searchTerm) {
                            highlightText(item, searchTerm);
                        } else {
                            removeHighlights();
                        }
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
            
            // FAQ Navigation
            const navButtons = document.querySelectorAll('.nav-btn');
            
            navButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const category = this.getAttribute('data-category');
                    
                    // Update active button
                    navButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show/hide categories
                    const categories = document.querySelectorAll('.faq-category');
                    
                    if (category === 'all') {
                        categories.forEach(cat => {
                            cat.style.display = 'block';
                            cat.querySelectorAll('.faq-item').forEach(item => {
                                item.style.display = 'block';
                            });
                        });
                    } else {
                        categories.forEach(cat => {
                            if (cat.getAttribute('data-category') === category) {
                                cat.style.display = 'block';
                                cat.querySelectorAll('.faq-item').forEach(item => {
                                    item.style.display = 'block';
                                });
                            } else {
                                cat.style.display = 'none';
                            }
                        });
                    }
                    
                    // Reset search
                    searchInput.value = '';
                    removeHighlights();
                });
            });
            
            // Text highlighting functions
            function highlightText(element, term) {
                removeHighlights();
                
                const walker = document.createTreeWalker(
                    element,
                    NodeFilter.SHOW_TEXT,
                    null,
                    false
                );
                
                let node;
                while (node = walker.nextNode()) {
                    const parent = node.parentNode;
                    if (parent.nodeName === 'MARK') continue;
                    
                    const index = node.textContent.toLowerCase().indexOf(term);
                    if (index !== -1) {
                        const span = document.createElement('mark');
                        span.style.backgroundColor = 'rgba(212, 175, 55, 0.3)';
                        span.style.padding = '2px 0';
                        
                        const middle = node.splitText(index);
                        const after = middle.splitText(term.length);
                        
                        const highlighted = middle.cloneNode(true);
                        span.appendChild(highlighted);
                        parent.replaceChild(span, middle);
                    }
                }
            }
            
            function removeHighlights() {
                const marks = document.querySelectorAll('mark');
                marks.forEach(mark => {
                    const parent = mark.parentNode;
                    parent.replaceChild(mark.firstChild, mark);
                    parent.normalize();
                });
            }
            
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