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
    <title>Return & Refund Policy | ATK Perfumes</title>
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

        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .process-step {
            background: var(--light-gray);
            padding: 2rem;
            border-radius: var(--radius);
            text-align: center;
            transition: var(--transition);
            position: relative;
        }

        .process-step:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .step-number {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 40px;
            background: var(--gradient-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--black);
            font-family: 'Cinzel', serif;
            font-size: 1.2rem;
        }

        .process-step i {
            font-size: 2.5rem;
            color: var(--gold);
            margin-bottom: 1rem;
            margin-top: 1rem;
        }

        .process-step h4 {
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

        .table-container {
            overflow-x: auto;
            margin: 2rem 0;
        }

        .policy-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .policy-table th {
            background: var(--gradient-gold);
            color: var(--black);
            font-family: 'Cinzel', serif;
            padding: 1rem;
            text-align: left;
        }

        .policy-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--light-gray);
        }

        .policy-table tr:nth-child(even) {
            background-color: rgba(245, 245, 245, 0.5);
        }

        .policy-table tr:hover {
            background-color: rgba(212, 175, 55, 0.1);
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
            
            .process-steps {
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
                <h1 class="page-title">Return & Refund Policy</h1>
                <p class="page-subtitle">Your satisfaction is our priority. Learn about our return process and refund options.</p>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="content-section">
        <div class="container">
            <div class="content-container">
                <div class="policy-content fade-in">
                    <!-- Return Policy Overview -->
                    <div class="policy-section">
                        <h3>Return Policy Overview</h3>
                        <p>At ATK Perfumes, we want you to be completely satisfied with your purchase. If you're not happy with your order for any reason, we offer a straightforward return process within 30 days of delivery.</p>
                        
                        <div class="info-box">
                            <h4>Quick Return Facts</h4>
                            <p>• 30-day return window from delivery date<br>
                               • Items must be in original, unopened condition<br>
                               • Original proof of purchase required<br>
                               • Refunds processed within 5-10 business days</p>
                        </div>
                    </div>

                    <!-- Return Process -->
                    <div class="policy-section">
                        <h3>How to Return an Item</h3>
                        <p>Returning an item is simple. Follow these steps to initiate your return:</p>
                        
                        <div class="process-steps">
                            <div class="process-step">
                                <span class="step-number">1</span>
                                <i class="fas fa-envelope"></i>
                                <h4>Contact Us</h4>
                                <p>Email our customer service team at returns@atkperfumes.com with your order number and reason for return.</p>
                            </div>
                            <div class="process-step">
                                <span class="step-number">2</span>
                                <i class="fas fa-print"></i>
                                <h4>Print Label</h4>
                                <p>We'll email you a prepaid return shipping label to use for your return package.</p>
                            </div>
                            <div class="process-step">
                                <span class="step-number">3</span>
                                <i class="fas fa-box"></i>
                                <h4>Package & Ship</h4>
                                <p>Securely package your item with all original packaging and ship using our provided label.</p>
                            </div>
                            <div class="process-step">
                                <span class="step-number">4</span>
                                <i class="fas fa-check-circle"></i>
                                <h4>Receive Refund</h4>
                                <p>Once we receive and inspect your return, we'll process your refund within 5-10 business days.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Refund Information -->
                    <div class="policy-section">
                        <h3>Refund Information</h3>
                        <p>Refunds are issued to the original payment method used for purchase. Please allow 5-10 business days for the refund to appear in your account after we process it.</p>
                        
                        <div class="table-container">
                            <table class="policy-table">
                                <thead>
                                    <tr>
                                        <th>Refund Type</th>
                                        <th>Processing Time</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Credit/Debit Card</td>
                                        <td>5-10 business days</td>
                                        <td>Time varies by bank</td>
                                    </tr>
                                    <tr>
                                        <td>PayPal</td>
                                        <td>3-5 business days</td>
                                        <td>Refunded to PayPal balance</td>
                                    </tr>
                                    <tr>
                                        <td>Gift Card</td>
                                        <td>24-48 hours</td>
                                        <td>New e-gift card issued</td>
                                    </tr>
                                    <tr>
                                        <td>Store Credit</td>
                                        <td>24 hours</td>
                                        <td>Immediate credit to account</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="info-box">
                            <h4>Return Shipping Costs</h4>
                            <p>We provide free return shipping for items that are defective or incorrect. For returns based on preference, a $7.95 return shipping fee will be deducted from your refund amount.</p>
                        </div>
                    </div>

                    <!-- Exceptions & Special Cases -->
                    <div class="policy-section">
                        <h3>Exceptions & Special Cases</h3>
                        <p>While we strive to accommodate all returns, there are some exceptions to our policy:</p>
                        
                        <div class="warning-box">
                            <h4>Non-Returnable Items</h4>
                            <p>For health and safety reasons, we cannot accept returns on:</p>
                            <ul>
                                <li>Opened or used fragrance products</li>
                                <li>Gift sets with broken seals</li>
                                <li>Personalized or monogrammed items</li>
                                <li>Final sale items (clearly marked)</li>
                            </ul>
                        </div>
                        
                        <h4>Damaged or Defective Items</h4>
                        <p>If your item arrives damaged or defective, please contact us within 7 days of delivery. We will arrange for a replacement or full refund, including any shipping costs.</p>
                        
                        <h4>International Returns</h4>
                        <p>International customers are responsible for return shipping costs and any applicable customs fees. Refunds will be issued in the original currency, minus any currency conversion fees.</p>
                    </div>

                    <!-- Exchange Policy -->
                    <div class="policy-section">
                        <h3>Exchange Policy</h3>
                        <p>We're happy to exchange your item for a different fragrance or size, subject to availability.</p>
                        
                        <div class="timeline">
                            <div class="timeline-item">
                                <h4 class="timeline-title">Exchange Request</h4>
                                <p>Contact us within 30 days of delivery to request an exchange</p>
                            </div>
                            <div class="timeline-item">
                                <h4 class="timeline-title">Return Original Item</h4>
                                <p>Follow our standard return process with the original packaging</p>
                            </div>
                            <div class="timeline-item">
                                <h4 class="timeline-title">New Item Shipped</h4>
                                <p>Once we receive your return, we'll ship your exchange item</p>
                            </div>
                        </div>
                        
                        <div class="info-box">
                            <h4>Price Differences</h4>
                            <p>If your exchange item has a higher value, you'll be charged the difference. If it has a lower value, we'll refund the difference to your original payment method.</p>
                        </div>
                    </div>

                    <!-- Gift Returns -->
                    <div class="policy-section">
                        <h3>Gift Returns</h3>
                        <p>Items received as gifts can be returned for store credit within 30 days of purchase. The gift giver will receive notification of the return.</p>
                        
                        <p>To return a gift:</p>
                        <ul>
                            <li>Contact our customer service team with the order number</li>
                            <li>We'll verify the purchase and provide return instructions</li>
                            <li>Once received, we'll issue a store credit e-gift card</li>
                        </ul>
                    </div>

                    <!-- Contact Information -->
                    <div class="policy-section">
                        <h3>Need Help With a Return?</h3>
                        <p>Our customer service team is here to help with any questions about returns or refunds.</p>
                        
                        <div class="info-box">
                            <h4>Contact Information</h4>
                            <p>Email: returns@atkperfumes.com<br>
                               Phone: 1-800-ATK-PERF (1-800-285-7373)<br>
                               Hours: Monday-Friday, 9am-6pm EST</p>
                        </div>
                        
                        <p>For faster processing, please have your order number ready when contacting us.</p>
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