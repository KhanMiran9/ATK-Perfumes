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
    <title>Terms & Conditions | ATK Perfumes</title>
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

        .terms-content {
            background: var(--white);
            padding: 3rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .terms-section {
            margin-bottom: 3rem;
        }

        .terms-section:last-child {
            margin-bottom: 0;
        }

        .terms-section h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--gold);
            font-family: 'Cinzel', serif;
            position: relative;
            padding-left: 1.5rem;
        }

        .terms-section h3:before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            background: var(--gradient-gold);
            border-radius: 50%;
        }

        .terms-section p {
            margin-bottom: 1.5rem;
            color: var(--muted);
        }

        .terms-section ul {
            margin-bottom: 1.5rem;
            padding-left: 1.5rem;
        }

        .terms-section li {
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

        .warning-box {
            background: linear-gradient(135deg, #fef7f7 0%, #fde8e8 100%);
            border-left: 4px solid #d4af37;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 0 var(--radius) var(--radius) 0;
        }

        .warning-box h4 {
            color: #d4af37;
            margin-bottom: 0.5rem;
            font-family: 'Cinzel', serif;
        }

        .table-container {
            overflow-x: auto;
            margin: 2rem 0;
        }

        .terms-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .terms-table th {
            background: var(--gradient-gold);
            color: var(--black);
            font-family: 'Cinzel', serif;
            padding: 1rem;
            text-align: left;
        }

        .terms-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--light-gray);
        }

        .terms-table tr:nth-child(even) {
            background-color: rgba(245, 245, 245, 0.5);
        }

        .terms-table tr:hover {
            background-color: rgba(212, 175, 55, 0.1);
        }

        .definition-list {
            margin: 1.5rem 0;
        }

        .definition-term {
            font-weight: 600;
            color: var(--black);
            margin-bottom: 0.25rem;
            font-family: 'Cinzel', serif;
        }

        .definition-description {
            margin-bottom: 1rem;
            color: var(--muted);
            padding-left: 1rem;
        }

        .toc-container {
            background: linear-gradient(135deg, #f9f9f9 0%, #f0f0f0 100%);
            border-radius: var(--radius);
            padding: 2rem;
            margin: 2rem 0;
            border: 1px solid var(--light-gray);
        }

        .toc-title {
            font-family: 'Cinzel', serif;
            color: var(--gold);
            margin-bottom: 1rem;
            text-align: center;
        }

        .toc-list {
            list-style: none;
            columns: 2;
        }

        .toc-item {
            margin-bottom: 0.75rem;
            break-inside: avoid;
        }

        .toc-link {
            color: var(--muted);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
        }

        .toc-link:before {
            content: '•';
            color: var(--gold);
            margin-right: 0.5rem;
            font-weight: bold;
        }

        .toc-link:hover {
            color: var(--gold);
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
            
            .toc-list {
                columns: 1;
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
            
            .terms-content {
                padding: 2rem;
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
            
            .toc-container {
                padding: 1.5rem;
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
                <h1 class="page-title">Terms & Conditions</h1>
                <p class="page-subtitle">Please read these terms carefully before using our website or purchasing our products.</p>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="content-section">
        <div class="container">
            <div class="content-container">
                <div class="terms-content fade-in">
                    <!-- Table of Contents -->
                    <div class="toc-container">
                        <h3 class="toc-title">Table of Contents</h3>
                        <ul class="toc-list">
                            <li class="toc-item"><a href="#acceptance" class="toc-link">Acceptance of Terms</a></li>
                            <li class="toc-item"><a href="#account" class="toc-link">Account Registration</a></li>
                            <li class="toc-item"><a href="#products" class="toc-link">Products & Pricing</a></li>
                            <li class="toc-item"><a href="#orders" class="toc-link">Order Process</a></li>
                            <li class="toc-item"><a href="#payments" class="toc-link">Payments</a></li>
                            <li class="toc-item"><a href="#shipping" class="toc-link">Shipping & Delivery</a></li>
                            <li class="toc-item"><a href="#returns" class="toc-link">Returns & Refunds</a></li>
                            <li class="toc-item"><a href="#intellectual" class="toc-link">Intellectual Property</a></li>
                            <li class="toc-item"><a href="#conduct" class="toc-link">User Conduct</a></li>
                            <li class="toc-item"><a href="#liability" class="toc-link">Limitation of Liability</a></li>
                            <li class="toc-item"><a href="#privacy" class="toc-link">Privacy Policy</a></li>
                            <li class="toc-item"><a href="#changes" class="toc-link">Changes to Terms</a></li>
                            <li class="toc-item"><a href="#governing" class="toc-link">Governing Law</a></li>
                            <li class="toc-item"><a href="#contact" class="toc-link">Contact Information</a></li>
                        </ul>
                    </div>

                    <!-- Acceptance of Terms -->
                    <div class="terms-section" id="acceptance">
                        <h3>1. Acceptance of Terms</h3>
                        <p>By accessing and using the ATK Perfumes website (www.atkperfumes.com), you accept and agree to be bound by the terms and provision of this agreement. Additionally, when using this website's particular services, you shall be subject to any posted guidelines or rules applicable to such services.</p>
                        
                        <div class="info-box">
                            <h4>Important Notice</h4>
                            <p>If you do not agree to these terms, please do not use our website or purchase our products. We reserve the right to update or modify these terms at any time without prior notice.</p>
                        </div>
                    </div>

                    <!-- Account Registration -->
                    <div class="terms-section" id="account">
                        <h3>2. Account Registration</h3>
                        <p>To purchase products or access certain features of our website, you may be required to create an account. You agree to provide accurate, current, and complete information during the registration process.</p>
                        
                        <div class="definition-list">
                            <div class="definition-term">Account Responsibilities</div>
                            <div class="definition-description">You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</div>
                            
                            <div class="definition-term">Age Requirement</div>
                            <div class="definition-description">You must be at least 18 years old to create an account and purchase our products.</div>
                            
                            <div class="definition-term">Account Termination</div>
                            <div class="definition-description">We reserve the right to suspend or terminate your account at our discretion if we believe you have violated these terms.</div>
                        </div>
                    </div>

                    <!-- Products & Pricing -->
                    <div class="terms-section" id="products">
                        <h3>3. Products & Pricing</h3>
                        <p>We strive to display our products and their prices as accurately as possible. However, we cannot guarantee that your computer monitor's display of any color will be accurate.</p>
                        
                        <div class="warning-box">
                            <h4>Pricing Information</h4>
                            <p>We reserve the right to modify or discontinue any product at any time without notice. Prices are subject to change without notice. We shall not be liable to you or any third-party for any modification, price change, suspension, or discontinuance of any product.</p>
                        </div>
                        
                        <div class="table-container">
                            <table class="terms-table">
                                <thead>
                                    <tr>
                                        <th>Product Category</th>
                                        <th>Availability</th>
                                        <th>Price Policy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Standard Collection</td>
                                        <td>In stock items ship within 1-2 business days</td>
                                        <td>Prices may vary based on market conditions</td>
                                    </tr>
                                    <tr>
                                        <td>Limited Edition</td>
                                        <td>While supplies last</td>
                                        <td>Fixed pricing, no discounts apply</td>
                                    </tr>
                                    <tr>
                                        <td>Seasonal Releases</td>
                                        <td>Available during specific seasons</td>
                                        <td>Special pricing may apply</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Order Process -->
                    <div class="terms-section" id="orders">
                        <h3>4. Order Process</h3>
                        <p>When you place an order through our website, we will send you an order confirmation email. This email will include your order number, details of the items you have ordered, and delivery information.</p>
                        
                        <p>All orders are subject to acceptance and availability. We may refuse to accept an order for any reason including but not limited to:</p>
                        <ul>
                            <li>Product availability issues</li>
                            <li>Errors in product or pricing information</li>
                            <li>Problems identified by our credit and fraud avoidance department</li>
                            <li>If you do not meet any eligibility criteria set out in these terms</li>
                        </ul>
                    </div>

                    <!-- Payments -->
                    <div class="terms-section" id="payments">
                        <h3>5. Payments</h3>
                        <p>We accept various payment methods including credit cards, debit cards, PayPal, and other payment methods as indicated on our website.</p>
                        
                        <div class="info-box">
                            <h4>Payment Security</h4>
                            <p>All payment transactions are processed through secure payment gateways. We do not store your credit card information on our servers.</p>
                        </div>
                        
                        <p>By placing an order, you confirm that the payment method you are using is yours or that you have been specifically authorized by the owner to use it.</p>
                    </div>

                    <!-- Shipping & Delivery -->
                    <div class="terms-section" id="shipping">
                        <h3>6. Shipping & Delivery</h3>
                        <p>Shipping costs and delivery times vary depending on your location and the shipping method selected. Please refer to our Shipping Policy for detailed information.</p>
                        
                        <p>Risk of loss and title for items purchased from ATK Perfumes pass to you upon our delivery to the carrier. We are not responsible for any delays caused by destination customs clearance processes.</p>
                    </div>

                    <!-- Returns & Refunds -->
                    <div class="terms-section" id="returns">
                        <h3>7. Returns & Refunds</h3>
                        <p>We want you to be completely satisfied with your purchase. If you are not happy with your order, you may return it within 30 days of delivery for a refund or exchange.</p>
                        
                        <div class="warning-box">
                            <h4>Return Conditions</h4>
                            <p>Items must be returned in their original condition with all packaging intact. For health and safety reasons, we cannot accept returns on opened fragrance products.</p>
                        </div>
                        
                        <p>Please refer to our Return & Refund Policy for complete details on the return process, eligibility, and timelines.</p>
                    </div>

                    <!-- Intellectual Property -->
                    <div class="terms-section" id="intellectual">
                        <h3>8. Intellectual Property</h3>
                        <p>All content included on this website, such as text, graphics, logos, button icons, images, audio clips, digital downloads, data compilations, and software, is the property of ATK Perfumes or its content suppliers and protected by international copyright laws.</p>
                        
                        <p>The ATK Perfumes name and logo, and all related product and service names, design marks, and slogans are the trademarks or service marks of ATK Perfumes. All other marks are the property of their respective companies.</p>
                    </div>

                    <!-- User Conduct -->
                    <div class="terms-section" id="conduct">
                        <h3>9. User Conduct</h3>
                        <p>You agree not to use the website:</p>
                        <ul>
                            <li>For any unlawful purpose or to solicit others to perform or participate in any unlawful acts</li>
                            <li>To violate any international, federal, provincial, or state regulations, rules, laws, or local ordinances</li>
                            <li>To infringe upon or violate our intellectual property rights or the intellectual property rights of others</li>
                            <li>To harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate based on gender, sexual orientation, religion, ethnicity, race, age, national origin, or disability</li>
                            <li>To submit false or misleading information</li>
                            <li>To upload or transmit viruses or any other type of malicious code that will or may be used in any way that will affect the functionality or operation of the Service or of any related website, other websites, or the Internet</li>
                        </ul>
                    </div>

                    <!-- Limitation of Liability -->
                    <div class="terms-section" id="liability">
                        <h3>10. Limitation of Liability</h3>
                        <p>To the fullest extent permitted by applicable law, ATK Perfumes shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether incurred directly or indirectly, or any loss of data, use, goodwill, or other intangible losses, resulting from:</p>
                        
                        <ul>
                            <li>Your access to or use of or inability to access or use the service</li>
                            <li>Any conduct or content of any third party on the service</li>
                            <li>Any content obtained from the service</li>
                            <li>Unauthorized access, use, or alteration of your transmissions or content</li>
                        </ul>
                        
                        <div class="info-box">
                            <h4>Maximum Liability</h4>
                            <p>In no event shall ATK Perfumes' aggregate liability for all claims relating to the service exceed the greater of $100 or the amount you paid ATK Perfumes, if any, in the past 12 months for the services giving rise to the claim.</p>
                        </div>
                    </div>

                    <!-- Privacy Policy -->
                    <div class="terms-section" id="privacy">
                        <h3>11. Privacy Policy</h3>
                        <p>Your submission of personal information through the store is governed by our Privacy Policy. Please review our Privacy Policy to understand our practices regarding your personal information.</p>
                    </div>

                    <!-- Changes to Terms -->
                    <div class="terms-section" id="changes">
                        <h3>12. Changes to Terms</h3>
                        <p>We reserve the right, at our sole discretion, to update, change, or replace any part of these Terms of Service by posting updates and changes to our website. It is your responsibility to check our website periodically for changes.</p>
                        
                        <p>Your continued use of or access to our website or the Service following the posting of any changes to these Terms of Service constitutes acceptance of those changes.</p>
                    </div>

                    <!-- Governing Law -->
                    <div class="terms-section" id="governing">
                        <h3>13. Governing Law</h3>
                        <p>These Terms of Service and any separate agreements whereby we provide you Services shall be governed by and construed in accordance with the laws of the State of California, United States.</p>
                    </div>

                    <!-- Contact Information -->
                    <div class="terms-section" id="contact">
                        <h3>14. Contact Information</h3>
                        <p>Questions about the Terms of Service should be sent to us at:</p>
                        
                        <div class="info-box">
                            <h4>ATK Perfumes Customer Service</h4>
                            <p>Email: legal@atkperfumes.com<br>
                               Phone: 1-800-ATK-PERF (1-800-285-7373)<br>
                               Address: 123 Luxury Avenue, Beverly Hills, CA 90210</p>
                        </div>
                    </div>

                    <div class="last-updated">
                        <p>Last updated: <?php echo date('F j, Y'); ?></p>
                    </div>
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
            
            // Smooth scrolling for table of contents links
            document.querySelectorAll('.toc-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                });
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