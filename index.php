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
    <title>ATK Perfumes | Luxury Fragrances</title>
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
            /* max-width: 1280px; */
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

    .btn-secondary {
        background: transparent;
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #d4af37;
        transform: translateY(-3px);
    }

        /* Shine Animation */
       @keyframes shine {
  0% {
    background-position: 0 200%;
  }
  100% {
    background-position: 0 -200%;
  }
}


        /* Header Styles */
   .header {
 
    
    border-bottom-left-radius: 50px;
    border-bottom-right-radius: 50px;
    position: fixed;
    background: linear-gradient(90deg, #7F6051, #b58c65, #f3dab3, #b58c65, #7F6051);
    background-size: 300% 100%;
    animation: shine 20s 
linear infinite;
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

        /* Hero Section */
        .hero {
            position: relative;
            height: 100vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            background: linear-gradient(135deg, var(--light-gray) 0%, var(--white) 100%);
        }

        .hero-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 600px;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: var(--muted);
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-family: 'Cinzel', serif;
        }

        .hero-title {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            line-height: 1.1;
            background: linear-gradient(45deg, var(--gold), var(--silver));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-description {
            font-size: 1.1rem;
            margin-bottom: 2.5rem;
            color: var(--muted);
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
        }

        .marquee {
            position: absolute;
            bottom: 2rem;
            left: 0;
            width: 100%;
            overflow: hidden;
            z-index: 2;
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.1), rgba(192, 192, 192, 0.1));
            padding: 1rem 0;
        }

        .marquee-content {
            display: flex;
            animation: marquee 20s linear infinite;
            white-space: nowrap;
        }

        .marquee-item {
            padding: 0 2rem;
            font-size: 1.1rem;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-family: 'Cinzel', serif;
            font-weight: 600;
        }

        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Featured Products */
        .section {
            padding: 6rem 0;
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

        .section-subtitle {
            color: var(--muted);
            max-width: 600px;
            margin: 0 auto;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2.5rem;
        }

        .product-card {
            background: var(--white);
            border-radius: var(--radius);
            overflow: hidden;
            transition: var(--transition);
            box-shadow: var(--shadow);
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }

        .product-image {
            width: 50%;
            height: 320px;
            object-fit: cover;
            transition: var(--transition);
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-category {
            font-size: 0.9rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .product-name {
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
            font-family: 'Cinzel', serif;
        }

        .product-description {
            color: var(--muted);
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .product-price {
            font-weight: 600;
            color: var(--black);
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .product-actions {
            display: flex;
            gap: 0.75rem;
        }

        .product-action {
            flex: 1;
            padding: 0.75rem;
            text-align: center;
            border-radius: var(--radius);
            background: var(--light-gray);
            color: var(--black);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .product-action.primary {
            background: var(--gradient-gold);
            color: var(--white);
        }

        .product-action:hover {
            background: var(--black);
            color: var(--white);
        }

        /* Brand Story */
        .brand-story {
            background-color: var(--light-gray);
            position: relative;
        }

        .story-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .story-content {
            padding-right: 2rem;
        }

        .story-title {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .story-text {
            margin-bottom: 1.5rem;
            color: var(--muted);
        }

        .story-highlights {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 2.5rem;
        }

        .highlight {
            text-align: center;
            /* padding: 1.5rem; */
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .highlight-icon {
            font-size: 2rem;
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .highlight-title {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            font-family: 'Cinzel', serif;
        }

        .highlight-text {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .story-image {
            position: relative;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .story-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Scent Notes */
        .scent-notes {
            text-align: center;
        }

        .notes-container {
            display: flex;
            justify-content: center;
            margin: 3rem 0;
            position: relative;
            height: 400px;
        }

        .note {
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--white);
            box-shadow: var(--shadow);
            cursor: pointer;
            transition: var(--transition);
        }

        .note:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-lg);
        }

        .note-top {
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gradient-gold);
        }

        .note-heart {
            top: 50%;
            left: 30%;
            transform: translateY(-50%);
            background: var(--gradient-silver);
        }

        .note-base {
            top: 50%;
            right: 30%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, var(--dark-gray) 0%, var(--black) 100%);
            color: var(--white);
        }

        .note-content {
            text-align: center;
        }

        .note-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
            font-family: 'Cinzel', serif;
        }

        .note-desc {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .note-detail {
            max-width: 400px;
            margin: 0 auto;
            padding: 2rem;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .note-detail.active {
            opacity: 1;
            visibility: visible;
        }

        /* How It's Made */
        .process {
            background-color: var(--light-gray);
        }

        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .step {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
        }

        .step:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .step-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--gold);
            margin-bottom: 1rem;
            line-height: 1;
        }

        .step-title {
            font-size: 1.4rem;
            margin-bottom: 1rem;
            font-family: 'Cinzel', serif;
        }

        .step-description {
            color: var(--muted);
        }

        /* Testimonials */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .testimonial {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            position: relative;
        }

        .testimonial:before {
            content: '"';
            position: absolute;
            top: 1rem;
            left: 1rem;
            font-size: 4rem;
            color: var(--gold);
            opacity: 0.2;
            font-family: 'Cinzel', serif;
            line-height: 1;
        }

        .testimonial-content {
            margin-bottom: 1.5rem;
            color: var(--muted);
            position: relative;
            z-index: 1;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 1rem;
        }

        .author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .author-info h4 {
            margin-bottom: 0.25rem;
            font-family: 'Cinzel', serif;
        }

        .author-role {
            font-size: 0.9rem;
            color: var(--muted);
        }

        /* Subscribe */
        .subscribe {
            background: linear-gradient(135deg, var(--black) 0%, var(--dark-gray) 100%);
            color: var(--white);
            text-align: center;
            padding: 6rem 0;
        }

        .subscribe .section-title {
            color: var(--white);
        }

        .subscribe .section-title:after {
            background: var(--gradient-gold);
        }

        .subscribe-form {
            max-width: 500px;
            margin: 2rem auto 0;
            display: flex;
            gap: 1rem;
        }

        .subscribe-input {
            flex: 1;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
        }

        .subscribe-btn {
            background: var(--gradient-gold);
            color: var(--black);
            border: none;
            padding: 1rem 2rem;
            border-radius: var(--radius);
            font-family: 'Cinzel', serif;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            letter-spacing: 1px;
        }

        .subscribe-btn:hover {
            background: var(--gradient-silver);
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
            .hero-title {
                font-size: 3rem;
            }
            
            .story-grid {
                grid-template-columns: 1fr;
                gap: 3rem;
            }
            
            .story-content {
                padding-right: 0;
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
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-actions {
                flex-direction: column;
            }
            
            .subscribe-form {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .section {
                padding: 4rem 0;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .story-highlights {
                grid-template-columns: 1fr;
            }
        }

        /* Animation Classes */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Loading Animation */
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
    </style>
</head>
<body>
    <!-- Loading Animation -->
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
                <a href="#" class="logo">ATK</a>
                
                <nav class="nav">
                    <ul class="nav-list">
                        
                        <li><a href="#products" class="nav-link">Collections</a></li>
                        <li><a href="#story" class="nav-link">Our Story</a></li>
                        <li><a href="#ingredients" class="nav-link">Ingredients</a></li>
                        <li><a href="#testimonials" class="nav-link">Testimonials</a></li>
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
 <section class="hero-slider" aria-label="ATK Perfumes Luxury Fragrance Collection">
    <div class="slides-container">
        <!-- Slide 1 -->
        <div class="slide active" style="background-image: url('assets/images/1_bc36bd71-c5b2-463a-aeb3-3a27ca89fde7.webp')">
            <div class="slide-overlay"></div>
            <div class="container">
                <div class="slide-content">
                    <div class="slide-text">
                        <p class="slide-subtitle">Artisanal Fragrances</p>
                        <h1 class="slide-title">Crafting Scents of <span class="highlight">Timeless Elegance</span></h1>
                        <p class="slide-description">Discover our exclusive collection of luxury perfumes, meticulously crafted with the finest ingredients.</p>
                        <div class="slide-actions">
                            <a href="#products" class="btn btn-primary">Explore Collection</a>
                            <a href="#story" class="btn btn-secondary">Our Story</a>
                        </div>
                    </div>
                    <div class="slide-image">
                        <img src="assets/images/1_bc36bd71-c5b2-463a-aeb3-3a27ca89fde7.webp" 
                             alt="Luxury Perfume Bottle - ATK Signature Collection" 
                             loading="lazy">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slide 2 -->
        <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1592945403407-9de659572da6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
            <div class="slide-overlay"></div>
            <div class="container">
                <div class="slide-content">
                    <div class="slide-text">
                        <p class="slide-subtitle">New Arrival</p>
                        <h1 class="slide-title">Midnight Scents of<span class="highlight">Mystique Elegance</span></h1>
                        <p class="slide-description">Discover our exclusive collection of luxury perfumes, meticulously crafted with the finest ingredients.</p>
                        <div class="slide-actions">
                            <a href="#products" class="btn btn-primary">Shop Now</a>
                            <a href="#ingredients" class="btn btn-secondary">Discover Notes</a>
                        </div>
                    </div>
                    <div class="slide-image">
                        <img src="assets/images/1_bc36bd71-c5b2-463a-aeb3-3a27ca89fde7.webp" 
                             alt="Midnight Mystique Perfume - ATK New Collection" 
                             loading="lazy">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slide 3 -->
        <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1595425970377-2f8ded7c7b19?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
            <div class="slide-overlay"></div>
            <div class="container">
                <div class="slide-content">
                    <div class="slide-text">
                        <p class="slide-subtitle">Limited Edition</p>
                        <h1 class="slide-title">Golden Perfumes of<span class="highlight">Ambrosia Scents</span></h1>
                        <p class="slide-description">A rare blend of saffron, oud, and precious resins. Only 500 bottles available. Buy Now and Grab the Best Perfumes</p>
                        <div class="slide-actions">
                            <a href="#products" class="btn btn-primary">Pre-Order</a>
                            <a href="#story" class="btn btn-secondary">Learn More</a>
                        </div>
                    </div>
                    <div class="slide-image">
                        <img src="assets/images/1_bc36bd71-c5b2-463a-aeb3-3a27ca89fde7.webp" 
                             alt="Golden Ambrosia Limited Edition - ATK Exclusive" 
                             loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Progress Bar -->
    <div class="progress-container">
        <div class="progress-bar"></div>
    </div>
    
    <!-- Slider Controls (Desktop Only) -->
    <div class="slider-controls desktop-only">
        <button class="slider-btn prev-btn" aria-label="Previous slide">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="slider-btn next-btn" aria-label="Next slide">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</section>

<style>
    /* --- Shared slide layout --- */
.hero-slider .slide-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2.5rem;
  align-items: start; /* align at top so short content doesn't center */
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 18px;
}

/* Force the left text column to occupy a fixed vertical space
   so headings/descriptions/layout remain consistent across slides. */
.hero-slider .slide-text {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  min-height: 320px;         /* adjust this value to suit your design */
  max-height: 320px;         /* keep consistent height */
  box-sizing: border-box;
  overflow: hidden;          /* hide any overflow; line-clamp will handle truncation */
  padding-right: 6px;
}

/* Title: allow up to 2 lines, then ellipsis */
.hero-slider .slide-title {
  font-size: 2.8rem;
  line-height: 1.05;
  margin: 0 0 12px 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;     /* limit to 2 lines */
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Subtitle small one-line */
.hero-slider .slide-subtitle {
  margin-bottom: 10px;
  letter-spacing: 2px;
}

/* Description: allow up to N lines, then ellipsis */
.hero-slider .slide-description {
  font-size: 1.05rem;
  line-height: 1.5;
  margin-top: 8px;
  display: -webkit-box;
  -webkit-line-clamp: 4;     /* limit description to 4 lines */
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 12px;
}

/* Buttons section pinned to bottom of the text box (keeps buttons consistent) */
.hero-slider .slide-actions {
  margin-top: auto; /* pushes actions to bottom of the fixed-height text column */
  display: flex;
  gap: 1rem;
}

/* Make the image column align to top and match height visually */
.hero-slider .slide-image {
  display: flex;
  align-items: flex-start;
  justify-content: center;
}
.hero-slider .slide-image img {
  max-height: 320px; /* match text area height visually */
  width: auto;
  border-radius: 12px;
  object-fit: contain;
}

/* Responsive: stack on smaller screens and keep visual parity */
@media (max-width: 1024px) {
  .hero-slider .slide-content {
    grid-template-columns: 1fr;
    text-align: center;
  }
  .hero-slider .slide-text {
    min-height: 360px;  /* slightly taller on mobile if needed */
    max-height: 360px;
  }
  .hero-slider .slide-title { -webkit-line-clamp: 2; font-size: 2.0rem; }
  .hero-slider .slide-description { -webkit-line-clamp: 5; }
  .hero-slider .slide-image img { max-height: 280px; margin: 0 auto 12px; }
}

    /* Modern Hero Slider */
    .hero-slider {
        position: relative;
        height: 100vh;
        min-height: 700px;
        overflow: hidden;
        
    }

    .slides-container {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        display: flex;
        align-items: center;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .slide.active {
        opacity: 1;
        z-index: 2;
    }

    .slide-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #0d0d0d 100%);
        z-index: 1;
    }

    .slide-content {
        position: relative;
        z-index: 3;
        width: 100%;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
    }

    .slide-text {
        color: white;
        text-align: left;
    }

    .slide-subtitle {
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 3px;
        margin-bottom: 1rem;
        color: var(--gold);
        font-weight: 500;
        opacity: 0;
        transform: translateY(30px);
        animation: fadeInUp 0.8s ease 0.2s forwards;
    }

    .slide-title {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        line-height: 1.1;
        opacity: 0;
        transform: translateY(30px);
        animation: fadeInUp 0.8s ease 0.4s forwards;
    }

    .slide-title .highlight {
        background: linear-gradient(45deg, var(--gold), var(--silver));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .slide-description {
        font-size: 1.1rem;
        margin-bottom: 2.5rem;
        line-height: 1.6;
        opacity: 0;
        transform: translateY(30px);
        animation: fadeInUp 0.8s ease 0.6s forwards;
    }

    .slide-actions {
        display: flex;
        gap: 1.5rem;
        opacity: 0;
        transform: translateY(30px);
        animation: fadeInUp 0.8s ease 0.8s forwards;
    }

    .slide-image {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        opacity: 0;
        transform: translateY(30px);
        animation: fadeInUp 0.8s ease 0.8s forwards;
    }

    .slide-image img {
        max-width: 100%;
        height: auto;
        max-height: 500px;
        object-fit: contain;
        border-radius: 20px;
        box-shadow: var(--shadow-lg);
        transition: transform 0.5s ease;
    }

    .slide-image img:hover {
        transform: translateY(-10px) scale(1.02);
    }

    /* Progress Bar */
    .progress-container {
        position: absolute;
        bottom: 50px;
        left: 50%;
        transform: translateX(-50%);
        width: 300px;
        height: 4px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
        z-index: 3;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        width: 0%;
        background: linear-gradient(45deg, #a66d30, #ffe58e, #e0b057);
        border-radius: 2px;
        transition: width 0.1s linear;
    }

    /* Slider Controls */
    .slider-controls {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        z-index: 10;
        display: flex;
        justify-content: space-between;
        padding: 0 2rem;
        pointer-events: none;
    }

    .slider-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        cursor: pointer;
        transition: var(--transition);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        pointer-events: all;
    }

    .slider-btn:hover {
        background: var(--gold);
        transform: scale(1.1);
    }

    /* Mobile Styles */
    @media (max-width: 1024px) {
        .slide-content {
            grid-template-columns: 1fr;
            gap: 2rem;
            text-align: center;
        }
        
        .slide-image {
            order: -1;
            margin-bottom: 2rem;
        }
        
        .slide-image img {
            max-height: 350px;
        }
        
        .slider-controls.desktop-only {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .hero-slider {
            height: 90vh;
            min-height: 600px;
            
        }

        .slide-title {
            font-size: 2.5rem;
        }

        .slide-description {
            font-size: 1rem;
        }

        .slide-actions {
            flex-direction: column;
            gap: 1rem;
        }

        .slide-image img {
            max-height: 280px;
        }
    }

    @media (max-width: 480px) {
        .slide-title {
            font-size: 2rem;
        }

        .slide-subtitle {
            font-size: 0.9rem;
        }
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
<?php include 'limited.php';?>
  <style>
        :root {
            --brand-1: #7F6051;
            --ink: #000000;
            --paper: #FFFFFF;
            --accent-1: #e3bc6c;
            --accent-2: #f3dab3;
            --bg: #f9f5f0;
            --primary: #550000;
            --gap: 1.8rem;
            --shiny-gradient: linear-gradient(90deg, var(--brand-1), var(--accent-1), var(--accent-2), var(--accent-1), var(--brand-1));
        }

        .circle-menu-section { 
            padding: 2.6rem 1.25rem; 
            background-color: #f5f5f5; 
            font-family:'Cinzel', serif;
        }
        .circle-menu-container { 
            max-width: 1400px; 
            margin: 0 auto; 
        }
        .circle-menu-heading { 
            text-align: center; 
            margin-bottom: 2.25rem; 
            color: var(--primary); 
            font-family: 'Cinzel', serif;
            font-size: clamp(2rem, 2.4vw + 1rem, 3rem); 
            font-weight: 800; 
            letter-spacing: -0.4px; 
            line-height: 1.15; 
        }
        .circle-menu-heading::after { 
            content: ''; 
            display: block; 
            width: 90px; 
            height: 4px; 
            background-image: var(--shiny-gradient); 
            margin: 1rem auto 0; 
            border-radius: 999px; 
        }

        .circle-menu-scroll {
            touch-action: pan-x;
            -webkit-overflow-scrolling: touch;
            display: flex;
            overflow-x: auto;
            gap: var(--gap);
            padding: 1.75rem 1rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
            scroll-behavior: auto;
            cursor: grab;
            position: relative;
            overscroll-behavior-x: contain;
            overscroll-behavior-y: none;
        }
        .circle-menu-scroll.dragging { 
            cursor: grabbing; 
        }
        .circle-menu-scroll.no-snap { 
            scroll-snap-type: none !important; 
        }
        .circle-menu-scroll::-webkit-scrollbar { 
            display: none; 
        }

        .circle-item {
            flex: 0 0 auto;
            scroll-snap-align: start;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            transition: transform 0.25s ease;
            width: 170px;
            user-select: none;
            -webkit-user-drag: none;
            outline: none;
        }
        .circle-item:active { 
            transform: scale(0.985); 
        }
        .circle-item:focus-visible .circle-wrapper {
            box-shadow: 0 0 0 3px var(--paper), 0 0 0 6px var(--brand-1);
        }

        .circle-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 50%;
            background-color: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
        }

        .circle-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 999px;
            background: var(--shiny-gradient);
            background-size: 300% 100%;
            animation: shine 15s linear infinite;
            z-index: 0;
            pointer-events: none;
        }
        .circle-wrapper:hover::before,
        .circle-wrapper:focus-within::before {
            animation-play-state: paused;
        }

        .circle-wrapper > .circle-image,
        .circle-wrapper > .circle-fallback-letter {
            position: relative;
            z-index: 2;
            width: calc(100% - 6px);
            height: calc(100% - 6px);
            margin: 3px;
            border-radius: 50%;
            object-fit: cover;
            line-height: 1;
            -webkit-user-drag: none;
        }

        .circle-item:hover .circle-wrapper { 
            transform: scale(1.06); 
            box-shadow: 0 0 22px rgba(127, 96, 81, 0.65); 
        }

        .circle-image {
            opacity: 0.92;
            transition: opacity 0.25s ease;
        }
        .circle-item:hover .circle-image { 
            opacity: 1; 
        }

        .circle-fallback-letter {
            background-image: var(--shiny-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: #550000;
            font-weight: 800;
            font-size: clamp(1.8rem, 3.2vw, 2.4rem);
            font-family: 'Playfair Display', serif;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .circle-title {
            margin-top: 0.9rem;
            color: var(--primary);
            font-weight: 700;
            font-size: clamp(1rem, 0.9vw + 0.6rem, 1.15rem);
            text-align: center;
            max-width: 140px;
            line-height: 1.25;
            font-family: 'Cinzel', serif;
        }

        @media (min-width: 1200px) { 
            .circle-menu-scroll { 
                padding: 2rem; 
            } 
            .circle-item { 
                width: 190px; 
            } 
        }
        @media (max-width: 1199px) and (min-width: 768px) { 
            .circle-item { 
                width: 160px; 
            } 
        }

        @media (max-width: 767px) {
            .circle-menu-section { 
                padding: 3rem 0.75rem; 
            }
            .circle-menu-heading { 
                margin-bottom: 1.5rem; 
            }
            .circle-menu-scroll { 
                padding: 1.25rem 0.5rem; 
                gap: var(--gap); 
                scroll-snap-type: x proximity; 
                scroll-behavior: smooth; 
            }
            .circle-item { 
                width: calc((100% - (2 * var(--gap))) / 3); 
                max-width: none; 
            }
            .circle-title { 
                font-size: 1.05rem; 
                max-width: 90%; 
            }
        }

        .circle-pagination { 
            display: none; 
            justify-content: center; 
            align-items: center; 
            gap: 10px; 
            margin-top: 0.75rem; 
        }
        @media (max-width: 767px) { 
            .circle-pagination { 
                display: flex; 
            } 
        }
        .circle-bullet { 
            width: 8px; 
            height: 8px; 
            border-radius: 999px; 
            background: rgba(0, 0, 0, 0.25); 
            outline: 1px solid rgba(127, 96, 81, 0.45); 
            transition: transform 0.2s ease, background 0.2s ease, width 0.2s ease; 
            cursor: pointer; 
        }
        .circle-bullet.active { 
            background: var(--brand-1); 
            width: 22px; 
            transform: translateZ(0); 
        }

        .scroll-hint { 
            text-align: center; 
            margin-top: 1rem; 
            color: #4b3a31; 
            font-size: 0.95rem; 
            opacity: 0.75; 
            display: none; 
        }
        @media (min-width: 768px) { 
            .scroll-hint { 
                display: block; 
            } 
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
            font-size: 1.2rem;
            color: var(--primary);
        }

        @keyframes shine { 
            0% { 
                background-position: 0% 50%; 
            } 
            100% { 
                background-position: 100% 50%; 
            } 
        }
    </style>
</head>
<body>
    <section class="circle-menu-section">
        <div class="circle-menu-container">
            <h2 class="circle-menu-heading">Shop by Category</h2>
            
            <div id="loadingSpinner" class="loading-spinner">
                Loading categories...
            </div>
            
            <div class="circle-menu-scroll" id="circleMenuScroll" aria-label="Category carousel" role="region" style="display: none;">
                <!-- Categories will be loaded here dynamically -->
            </div>

            <div class="circle-pagination" id="circlePagination" aria-label="Carousel pagination"></div>
            <p class="scroll-hint">Use your mouse wheel or drag to scroll →</p>
        </div>
    </section>

    <script>
        // Configuration
        const API_ENDPOINT = 'api/get_categories.php'; // Update this path to match your API endpoint
        
        // DOM Elements
        const scroller = document.getElementById('circleMenuScroll');
        const pagination = document.getElementById('circlePagination');
        const loadingSpinner = document.getElementById('loadingSpinner');
        
        // State variables
        let items = [];
        let isPointerDown = false;
        let isDragging = false;
        let startScrollLeft = 0;
        let lastClientX = 0;
        let lastClientY = 0;
        let lastMoveTime = 0;
        let velocity = 0;
        let targetLeft = null;
        let animating = false;
        let rafId = null;
        let justDragged = false;
        let downInfo = null;
        
        // Constants
        const DRAG_THRESHOLD = 12;
        const JUST_DRAGGED_MS = 300;
        const AUTO_MS = 3000;
        
        // Utility functions
        const clamp = (v, a, b) => Math.min(b, Math.max(a, v));
        const maxLeft = () => Math.max(0, scroller.scrollWidth - scroller.clientWidth);
        const isMobile = () => window.matchMedia('(max-width: 767px)').matches;
        
        // Animation functions
        function startAnimLoop() {
            if (animating) return;
            animating = true;
            rafId = requestAnimationFrame(step);
        }
        
        function stopAnimLoop() {
            animating = false;
            if (rafId) cancelAnimationFrame(rafId);
            rafId = null;
            lastMoveTime = 0;
        }
        
        function step() {
            const current = scroller.scrollLeft;
            let needsMore = false;

            if (targetLeft != null) {
                const dist = targetLeft - current;
                const accel = dist * 0.18;
                velocity = (velocity + accel) * 0.82;
                let next = current + velocity;
                if (Math.abs(dist) < 0.5 && Math.abs(velocity) < 0.2) {
                    next = targetLeft;
                    targetLeft = null;
                    velocity = 0;
                } else {
                    needsMore = true;
                }
                scroller.scrollLeft = clamp(next, 0, maxLeft());
            } else if (Math.abs(velocity) > 0.05) {
                scroller.scrollLeft = clamp(current + velocity, 0, maxLeft());
                velocity *= 0.95;
                needsMore = true;
            }

            if (needsMore) rafId = requestAnimationFrame(step);
            else stopAnimLoop();
        }
        
        // Event handlers for desktop interaction
        function setupDesktopInteractions() {
            // Wheel scrolling
            scroller.addEventListener('wheel', (e) => {
                if (isMobile()) return;
                e.preventDefault();

                let delta = (Math.abs(e.deltaX) > Math.abs(e.deltaY) ? e.deltaX : e.deltaY) || 0;
                if (e.deltaMode === 1) delta *= 16;
                else if (e.deltaMode === 2) delta *= scroller.clientHeight;

                const multiplier = 1.15;
                const proposed = (targetLeft == null ? scroller.scrollLeft : targetLeft) + delta * multiplier;

                targetLeft = clamp(proposed, 0, maxLeft());
                velocity = (delta * multiplier) * 0.02;
                startAnimLoop();
            }, { passive: false });

            // Pointer events for mouse/pen dragging
            scroller.addEventListener('pointerdown', (e) => {
                if (e.pointerType !== 'mouse' && e.pointerType !== 'pen') return;
                if (e.button !== 0) return;

                isPointerDown = true;
                isDragging = false;

                downInfo = { x: e.clientX, y: e.clientY, time: performance.now() };
                startScrollLeft = scroller.scrollLeft;
                lastClientX = e.clientX;
                lastClientY = e.clientY;
                targetLeft = null;
                velocity = 0;
                stopAnimLoop();
            }, { passive: true });

            scroller.addEventListener('pointermove', (e) => {
                if (!isPointerDown) return;
                if (e.pointerType !== 'mouse' && e.pointerType !== 'pen') return;

                const cx = e.clientX;
                const cy = e.clientY;
                lastClientX = cx;
                lastClientY = cy;

                const dxFromStart = cx - (downInfo ? downInfo.x : cx);
                const dyFromStart = cy - (downInfo ? downInfo.y : cy);

                if (!isDragging) {
                    if (Math.abs(dxFromStart) > DRAG_THRESHOLD && Math.abs(dxFromStart) > Math.abs(dyFromStart)) {
                        isDragging = true;
                        scroller.classList.add('dragging', 'no-snap');
                    }
                }

                if (isDragging) {
                    if (e.cancelable) e.preventDefault();
                    const prevLeft = scroller.scrollLeft;
                    scroller.scrollLeft = clamp(startScrollLeft - dxFromStart, 0, maxLeft());

                    const now = performance.now();
                    if (lastMoveTime) {
                        const dt = Math.max(1, now - lastMoveTime);
                        const deltaScroll = scroller.scrollLeft - prevLeft;
                        velocity = (deltaScroll / dt) * 16;
                    }
                    lastMoveTime = now;
                }
            }, { passive: false });

            function endPointer() {
                if (!isPointerDown) return;
                isPointerDown = false;

                let wasReallyDragged = false;
                if (downInfo) {
                    const dx = lastClientX - downInfo.x;
                    const dy = lastClientY - downInfo.y;
                    wasReallyDragged = isDragging && Math.abs(dx) > DRAG_THRESHOLD && Math.abs(dx) > Math.abs(dy);
                }

                if (wasReallyDragged) {
                    justDragged = true;
                    scroller.classList.remove('dragging', 'no-snap');
                    if (Math.abs(velocity) > 0.1) startAnimLoop();
                    setTimeout(() => { justDragged = false; }, JUST_DRAGGED_MS);
                } else {
                    justDragged = false;
                    scroller.classList.remove('dragging', 'no-snap');
                }

                isDragging = false;
                lastMoveTime = 0;
                downInfo = null;
            }

            scroller.addEventListener('pointerup', endPointer, { passive: true });
            scroller.addEventListener('pointercancel', endPointer, { passive: true });
            document.addEventListener('pointerup', endPointer, { passive: true });

            // Click management
            scroller.addEventListener('click', (e) => {
                const anchor = e.target && e.target.closest && e.target.closest('a');
                if (!anchor) return;

                if (justDragged) {
                    e.preventDefault();
                    e.stopImmediatePropagation && e.stopImmediatePropagation();
                    return;
                }

                // Desktop reliable fallback only
                if (
                    !isMobile() &&
                    e.button === 0 &&
                    !e.defaultPrevented &&
                    !e.metaKey && !e.ctrlKey && !e.shiftKey && !e.altKey &&
                    (!anchor.target || anchor.target === '_self')
                ) {
                    setTimeout(() => {
                        if (!document.hidden) window.location.href = anchor.href;
                    }, 0);
                }
            }, true);
        }
        
        // Mobile pagination and autoplay
        let perPage = 3;
        let pages = 1;
        let pageOffsets = [];
        let activePage = 0;
        let autoTimer = null;

        function computePageOffsets() {
            items = Array.from(scroller.querySelectorAll('.circle-item'));
            const styles = getComputedStyle(scroller);
            const padLeft = parseFloat(styles.paddingLeft) || 0;
            perPage = 3; // show 3 items per "page" on mobile
            pages = Math.max(1, Math.ceil(items.length / perPage));
            pageOffsets = [];
            for (let i = 0; i < pages; i++) {
                const idx = i * perPage;
                const item = items[idx];
                if (!item) continue;
                pageOffsets.push(Math.max(0, item.offsetLeft - padLeft));
            }
        }

        function renderPagination() {
            if (!pagination) return;
            pagination.innerHTML = '';

            if (!isMobile() || pages <= 1) {
                pagination.style.display = 'none';
                return;
            }

            pagination.style.display = 'flex';

            for (let i = 0; i < pages; i++) {
                const b = document.createElement('div');
                b.className = 'circle-bullet' + (i === activePage ? ' active' : '');
                b.setAttribute('role', 'button');
                b.setAttribute('aria-label', `Go to page ${i + 1}`);
                b.setAttribute('tabindex', '0');
                b.addEventListener('click', () => goToPage(i));
                b.addEventListener('keydown', (ev) => {
                    if (ev.key === 'Enter' || ev.key === ' ') {
                        ev.preventDefault();
                        goToPage(i);
                    }
                });
                pagination.appendChild(b);
            }
        }

        function updateActivePage() {
            if (!isMobile() || pageOffsets.length === 0) return;
            const current = scroller.scrollLeft;
            let nearestIndex = 0;
            let nearestDist = Infinity;
            pageOffsets.forEach((offset, idx) => {
                const dist = Math.abs(current - offset);
                if (dist < nearestDist) {
                    nearestDist = dist;
                    nearestIndex = idx;
                }
            });
            if (nearestIndex !== activePage) {
                activePage = nearestIndex;
                renderPagination();
            }
        }

        function goToPage(idx) {
            if (!isMobile()) return;
            idx = Math.max(0, Math.min(idx, pages - 1));
            activePage = idx;
            renderPagination();
            const target = pageOffsets[idx];
            scroller.scrollTo({ left: target, behavior: 'smooth' });
            resetAuto();
        }

        function resetAuto() {
            if (autoTimer) clearInterval(autoTimer);
            if (!isMobile() || pages <= 1) return;
            autoTimer = setInterval(() => {
                const next = (activePage + 1) % pages;
                goToPage(next);
            }, AUTO_MS);
        }

        function setupMobileInteractions() {
            // Touch interactions
            scroller.addEventListener('touchstart', () => {
                resetAuto();
            }, { passive: true });

            scroller.addEventListener('scroll', () => {
                updateActivePage();
            });
        }
        
        // Category loading and rendering
    // Category loading and rendering
async function loadCategories() {
    try {
        const response = await fetch(API_ENDPOINT);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const categories = await response.json();
        
        // Check if we got any categories from the API
        if (categories && categories.length > 0) {
            renderCategories(categories);
        } else {
            // Show empty state instead of sample data
            showEmptyState();
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        // Show error state instead of sample data
        showErrorState();
    }
}

// Function to show empty state when no categories are found
function showEmptyState() {
    const scroller = document.getElementById('circleMenuScroll');
    loadingSpinner.style.display = 'none';
    scroller.style.display = 'flex';
    scroller.innerHTML = `
        <div style="width: 100%; text-align: center; padding: 2rem; color: var(--primary);">
            <p>No categories found.</p>
        </div>
    `;
}

// Function to show error state when API fails
function showErrorState() {
    const scroller = document.getElementById('circleMenuScroll');
    loadingSpinner.style.display = 'none';
    scroller.style.display = 'flex';
    scroller.innerHTML = `
        <div style="width: 100%; text-align: center; padding: 2rem; color: var(--primary);">
            <p>Unable to load categories. Please try again later.</p>
        </div>
    `;
}

function renderCategories(categories) {
    const scroller = document.getElementById('circleMenuScroll');
    scroller.innerHTML = '';
    
    categories.forEach(category => {
        const categoryElement = document.createElement('a');
        categoryElement.href = `category.php?id=${category.id}`;
        categoryElement.className = 'circle-item';
        categoryElement.draggable = false;
        
        categoryElement.innerHTML = `
            <div class="circle-wrapper">
                ${category.image_url ? 
                    `<img src="${category.image_url}" alt="${category.name}" class="circle-image" loading="lazy" draggable="false">` :
                    `<div class="circle-fallback-letter" aria-hidden="true">${category.name.charAt(0)}</div>`
                }
            </div>
            <span class="circle-title">${category.name}</span>
        `;
        
        scroller.appendChild(categoryElement);
    });
    
    // Hide loading spinner and show scroller
    loadingSpinner.style.display = 'none';
    scroller.style.display = 'flex';
    
    // Initialize interactions
    setupDesktopInteractions();
    setupMobileInteractions();
    
    // Initialize mobile pagination
    const recalc = () => {
        computePageOffsets();
        renderPagination();
        updateActivePage();
        resetAuto();
    };

    const ro = new ResizeObserver(recalc);
    ro.observe(scroller);
    window.addEventListener('resize', recalc, { passive: true });
    window.addEventListener('orientationchange', recalc);

    // Initial calculation
    recalc();
}
        
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            loadCategories();
        });
    </script>
    <!-- Featured Products -->
   <?php include 'product1.php'?>

    <!-- Brand Story -->
    <section class="section brand-story" id="story">
        <div class="container">
            <div class="story-grid">
                <div class="story-content">
                    <h2 class="section-title">Our Story</h2>
                    <p class="story-text">Founded in 2010, ATK Perfumes began as a small boutique perfumery with a passion for creating exceptional scents that tell a story. Our journey started in a quaint studio in Paris, where our master perfumers combined traditional techniques with innovative approaches.</p>
                    <p class="story-text">Today, we've grown into a globally recognized brand, but our commitment to quality, craftsmanship, and authenticity remains unchanged. Each fragrance is still meticulously crafted by hand, using only the finest ingredients from around the world.</p>
                    
                    <div class="story-highlights">
                        <div class="highlight">
                            <div class="highlight-icon">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <h4 class="highlight-title">Natural Ingredients</h4>
                            <p class="highlight-text">Sourced from sustainable partners worldwide</p>
                        </div>
                        
                        <div class="highlight">
                            <div class="highlight-icon">
                                <i class="fas fa-hand-sparkles"></i>
                            </div>
                            <h4 class="highlight-title">Handcrafted</h4>
                            <p class="highlight-text">Each bottle is carefully crafted with precision</p>
                        </div>
                    </div>
                </div>
                
                <div class="story-image fade-in">
                    <img src="https://images.unsplash.com/photo-1615634376658-7d0cd0e18982?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Perfume Making Process">
                </div>
            </div>
        </div>
    </section>

    <!-- Scent Notes -->
    <section class="section scent-notes" id="ingredients">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">The Art of Scent</h2>
                <p class="section-subtitle">Discover the intricate layers that create our unique fragrances.</p>
            </div>
            
            <div class="notes-container">
                <div class="note note-top">
                    <div class="note-content">
                        <div class="note-name">Top Notes</div>
                        <div class="note-desc">Bergamot, Lemon</div>
                    </div>
                </div>
                
                <div class="note note-heart">
                    <div class="note-content">
                        <div class="note-name">Heart Notes</div>
                        <div class="note-desc">Jasmine, Rose</div>
                    </div>
                </div>
                
                <div class="note note-base">
                    <div class="note-content">
                        <div class="note-name">Base Notes</div>
                        <div class="note-desc">Sandalwood, Musk</div>
                    </div>
                </div>
            </div>
            
            <div class="note-detail">
                <h3>Top Notes</h3>
                <p>The initial impression of a fragrance, top notes are the lightest and most volatile scents that you smell immediately after application. They typically evaporate quickly, making way for the heart notes.</p>
            </div>
        </div>
    </section>

    <!-- How It's Made -->
    <section class="section process">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Craftsmanship Process</h2>
                <p class="section-subtitle">Each fragrance undergoes a meticulous creation process that takes months to perfect.</p>
            </div>
            
            <div class="process-steps">
                <div class="step fade-in">
                    <div class="step-number">01</div>
                    <h3 class="step-title">Inspiration</h3>
                    <p class="step-description">Each fragrance begins with a story, a memory, or an emotion that we want to capture.</p>
                </div>
                
                <div class="step fade-in">
                    <div class="step-number">02</div>
                    <h3 class="step-title">Sourcing</h3>
                    <p class="step-description">We carefully select the finest natural ingredients from trusted partners around the world.</p>
                </div>
                
                <div class="step fade-in">
                    <div class="step-number">03</div>
                    <h3 class="step-title">Creation</h3>
                    <p class="step-description">Our perfumers blend notes with precision, often taking months to perfect a single fragrance.</p>
                </div>
                
                <div class="step fade-in">
                    <div class="step-number">04</div>
                    <h3 class="step-title">Aging</h3>
                    <p class="step-description">The blended fragrance is aged to allow the notes to marry and develop complexity.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section" id="testimonials">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Editor's Choice</h2>
                <p class="section-subtitle">What fragrance connoisseurs and editors are saying about our creations.</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial fade-in">
                    <p class="testimonial-content">"ATK Perfumes has redefined luxury fragrances. Their attention to detail and use of rare ingredients sets them apart in an overcrowded market."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80" alt="Sarah Johnson">
                        </div>
                        <div class="author-info">
                            <h4>Sarah Johnson</h4>
                            <p class="author-role">Vogue Magazine</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial fade-in">
                    <p class="testimonial-content">"The Mystic Oud fragrance is a masterpiece. It's rare to find a scent that's both complex and wearable, but ATK Perfumes has achieved exactly that."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80" alt="Michael Chen">
                        </div>
                        <div class="author-info">
                            <h4>Michael Chen</h4>
                            <p class="author-role">GQ Magazine</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial fade-in">
                    <p class="testimonial-content">"As a perfumer with 20 years of experience, I can confidently say that ATK Perfumes represents the pinnacle of artisan fragrance craftsmanship."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="https://images.unsplash.com/photo-1552058544-f2b08422138a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=100&q=80" alt="Emily Rodriguez">
                        </div>
                        <div class="author-info">
                            <h4>Emily Rodriguez</h4>
                            <p class="author-role">Master Perfumer</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Subscribe -->
    <section class="subscribe">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Experience Luxury</h2>
                <p class="section-subtitle">Join our newsletter to receive exclusive offers and fragrance insights.</p>
            </div>
            
            <form class="subscribe-form">
                <input type="email" class="subscribe-input" placeholder="Your email address" required>
                <button type="submit" class="subscribe-btn">Subscribe</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="#" class="footer-logo">ATK<span>Perfumes</span></a>
                    <p class="footer-description">Crafting timeless fragrances with the world's finest ingredients.</p>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-heading">Collections</h3>
                    <ul class="footer-links">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <li class="footer-link"><a href="#"><?php echo htmlspecialchars($category['name']); ?></a></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="footer-link"><a href="#">For Men</a></li>
                            <li class="footer-link"><a href="#">For Women</a></li>
                            <li class="footer-link"><a href="#">Unisex</a></li>
                            <li class="footer-link"><a href="#">Limited Edition</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-heading">Company</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="#">Our Story</a></li>
                        <li class="footer-link"><a href="#">Sustainability</a></li>
                        <li class="footer-link"><a href="#">Press</a></li>
                        <li class="footer-link"><a href="#">Careers</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3 class="footer-heading">Support</h3>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="#">Contact Us</a></li>
                        <li class="footer-link"><a href="#">Shipping & Returns</a></li>
                        <li class="footer-link"><a href="#">FAQ</a></li>
                        <li class="footer-link"><a href="#">Privacy Policy</a></li>
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
                <p>&copy; 2023 ATK Perfumes. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
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
            
            // Note interaction
            const notes = document.querySelectorAll('.note');
            const noteDetail = document.querySelector('.note-detail');
            
            notes.forEach(note => {
                note.addEventListener('click', function() {
                    const noteType = this.classList[1];
                    let content = '';
                    
                    if (noteType === 'note-top') {
                        content = '<h3>Top Notes</h3><p>The initial impression of a fragrance, top notes are the lightest and most volatile scents that you smell immediately after application. They typically evaporate quickly, making way for the heart notes.</p>';
                    } else if (noteType === 'note-heart') {
                        content = '<h3>Heart Notes</h3><p>Also known as middle notes, the heart notes emerge just as the top notes dissipate. These scents form the core of the fragrance and determine its main character.</p>';
                    } else if (noteType === 'note-base') {
                        content = '<h3>Base Notes</h3><p>The final fragrance notes that appear once the top notes have completely evaporated. Base notes are the foundation of the perfume, providing depth and richness that can last for hours.</p>';
                    }
                    
                    noteDetail.innerHTML = content;
                    noteDetail.classList.add('active');
                });
            });
            
            // Simple Three.js background for hero section
            try {
                const canvas = document.getElementById('hero-canvas');
                const scene = new THREE.Scene();
                const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
                const renderer = new THREE.WebGLRenderer({ canvas, alpha: true });
                
                renderer.setSize(window.innerWidth, window.innerHeight);
                renderer.setClearColor(0x000000, 0);
                
                // Create particles for background
                const particlesGeometry = new THREE.BufferGeometry();
                const particlesCount = 1000;
                
                const posArray = new Float32Array(particlesCount * 3);
                
                for (let i = 0; i < particlesCount * 3; i++) {
                    posArray[i] = (Math.random() - 0.5) * 10;
                }
                
                particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
                
                const particlesMaterial = new THREE.PointsMaterial({
                    size: 0.02,
                    color: 0xd4af37,
                    transparent: true,
                    opacity: 0.6
                });
                
                const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
                scene.add(particlesMesh);
                
                camera.position.z = 2;
                
                // Animation
                function animate() {
                    requestAnimationFrame(animate);
                    
                    particlesMesh.rotation.x += 0.0005;
                    particlesMesh.rotation.y += 0.001;
                    
                    renderer.render(scene, camera);
                }
                
                animate();
                
                // Handle window resize
                window.addEventListener('resize', function() {
                    camera.aspect = window.innerWidth / window.innerHeight;
                    camera.updateProjectionMatrix();
                    renderer.setSize(window.innerWidth, window.innerHeight);
                });
            } catch (e) {
                console.error('Three.js error:', e);
            }
            
            // Product card hover effect
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px)';
                    this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.2)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.15)';
                });
            });
        });
    </script>
   
<script>
    // Slider functionality
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.slide');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        const progressBar = document.querySelector('.progress-bar');
        
        let currentSlide = 0;
        let slideInterval;
        let progressInterval;
        let progressWidth = 0;
        const slideDuration = 6000; // 6 seconds per slide
        
        // Function to show a specific slide
        function showSlide(index) {
            // Remove active class from all slides
            slides.forEach(slide => slide.classList.remove('active'));
            
            // Add active class to current slide
            slides[index].classList.add('active');
            
            currentSlide = index;
            
            // Reset progress bar
            resetProgress();
        }
        
        // Function to go to next slide
        function nextSlide() {
            let nextIndex = (currentSlide + 1) % slides.length;
            showSlide(nextIndex);
        }
        
        // Function to go to previous slide
        function prevSlide() {
            let prevIndex = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(prevIndex);
        }
        
        // Progress bar functions
        function startProgress() {
            progressWidth = 0;
            progressBar.style.width = '0%';
            
            progressInterval = setInterval(function() {
                progressWidth += (100 / (slideDuration / 100));
                progressBar.style.width = progressWidth + '%';
                
                if (progressWidth >= 100) {
                    clearInterval(progressInterval);
                }
            }, 100);
        }
        
        function resetProgress() {
            clearInterval(progressInterval);
            progressBar.style.width = '0%';
            startProgress();
        }
        
        function stopProgress() {
            clearInterval(progressInterval);
        }
        
        // Start automatic slideshow
        function startSlideShow() {
            slideInterval = setInterval(nextSlide, slideDuration);
            startProgress();
        }
        
        // Stop automatic slideshow
        function stopSlideShow() {
            clearInterval(slideInterval);
            stopProgress();
        }
        
        // Event listeners for controls
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                stopSlideShow();
                prevSlide();
                startSlideShow();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                stopSlideShow();
                nextSlide();
                startSlideShow();
            });
        }
        
        // Touch swipe functionality
        let touchStartX = 0;
        let touchEndX = 0;
        
        const slider = document.querySelector('.hero-slider');
        
        slider.addEventListener('touchstart', function(event) {
            touchStartX = event.changedTouches[0].screenX;
        }, false);
        
        slider.addEventListener('touchend', function(event) {
            touchEndX = event.changedTouches[0].screenX;
            handleSwipe();
        }, false);
        
        function handleSwipe() {
            const swipeThreshold = 50; // Minimum swipe distance
            
            if (touchEndX < touchStartX - swipeThreshold) {
                // Swipe left - next slide
                stopSlideShow();
                nextSlide();
                startSlideShow();
            }
            
            if (touchEndX > touchStartX + swipeThreshold) {
                // Swipe right - previous slide
                stopSlideShow();
                prevSlide();
                startSlideShow();
            }
        }
        
        // Pause slideshow on hover (for desktop)
        slider.addEventListener('mouseenter', stopSlideShow);
        slider.addEventListener('mouseleave', startSlideShow);
        
        // Keyboard navigation
        document.addEventListener('keydown', function(event) {
            if (event.key === 'ArrowLeft') {
                stopSlideShow();
                prevSlide();
                startSlideShow();
            } else if (event.key === 'ArrowRight') {
                stopSlideShow();
                nextSlide();
                startSlideShow();
            }
        });
        
        // Start the slideshow
        startSlideShow();
    });
</script>
    <script src="assets/js/app.js"></script>
</body>
</html>