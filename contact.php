<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - ATK Perfumes</title>

  <!-- Google Fonts -->
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
      padding: 100px 0 0px;
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
      padding: 0rem 0;
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
/* Contact Info Cards - Equal height + centered content */
.contact-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 25px;
  margin: 50px auto;
  padding: 0 20px;
  max-width: 1400px;

  /* Make each grid row stretch so every card in a row gets equal height */
  grid-auto-rows: 1fr;
  align-items: stretch; /* ensure children stretch to the row height */
}

/* Card */
.contact-card {
  background: var(--white);
  border-radius: var(--radius);
  padding: 18px;                    /* comfortable padding */
  text-align: center;
  box-shadow: var(--shadow);
  transition: var(--transition);
  border-left: 6px solid var(--gold);

  /* Fill the grid cell height so all cards equal height */
  display: flex;
  flex-direction: column;
  align-items: center;              /* center horizontally */
  justify-content: center;          /* center vertically within card */
  gap: 12px;
  height: 100%;                     /* important for equal heights */
  min-height: 240px;                /* baseline height for visual consistency */
  overflow: hidden;                 /* avoid overflow if content is long */
}

/* Hover effect */
.contact-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
}

/* Icon */
.circle-icon {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  background: rgba(212, 175, 55, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Heading */
.contact-card h3 {
  font-family: 'Cinzel', serif;
  color: var(--gold);
  margin: 0;
  font-size: 1.2rem;
  line-height: 1.2;
  font-weight: 600;
}

/* Paragraph / details */
.contact-card p {
  color: var(--muted);
  margin: 0;
  font-size: 0.95rem;
  max-width: 340px;
  text-align: center;
  word-break: break-word;
}

/* Small helper text */
.contact-card small {
  color: var(--muted);
  font-size: 0.8rem;
  display: block;
  margin-top: 6px;
  text-align: center;
}

/* Business hours wrapper */
.contact-card .hours {
  display: flex;
  flex-direction: column;
  gap: 6px;
  align-items: center;
  justify-content: center;
}

/* Responsive: 2 columns */
@media (max-width: 992px) {
  .contact-grid {
    grid-template-columns: repeat(2, 1fr);
    max-width: 900px;
    padding: 0 16px;
    grid-auto-rows: 1fr;
  }

  .contact-card { min-height: 220px; }
}

/* Responsive: 1 column */
@media (max-width: 576px) {
  .contact-grid {
    grid-template-columns: 1fr;
    padding: 0 12px;
    gap: 18px;
    max-width: 720px;
    grid-auto-rows: 1fr;
  }

  .contact-card {
    padding: 16px;
    min-height: auto; /* allow content to determine height on very small screens */
    height: 100%;
  }

  .circle-icon { width: 64px; height: 64px; }
  .contact-card h3 { font-size: 1.1rem; }
  .contact-card p { max-width: 420px; font-size: 0.95rem; }
}

    /* Map - Fixed */
    .map-container {
      width: 100%;
      padding: calc(40px * 0.75) 0 calc(52px * 0.75);
      overflow: hidden;
    }

    .map {
      width: 100%;
      height: 380px;
      border: none;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
    }

    /* Contact Form */
    .contact-form {
      background: var(--white);
      max-width: 700px;
      margin: 0 auto 70px;
      padding: 40px 30px;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      border: 1px solid rgba(212, 175, 55, 0.2);
    }

    .contact-form h2 {
      font-family: 'Cinzel', serif;
      text-align: center;
      margin-bottom: 25px;
      color: var(--gold);
      font-size: 2rem;
    }

    .contact-form label {
      display: block;
      margin: 12px 0 6px;
      font-weight: 500;
      color: var(--dark-gray);
    }

    .contact-form input,
    .contact-form textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #e0e0e0;
      border-radius: var(--radius);
      margin-bottom: 15px;
      font-size: 1rem;
      transition: var(--transition);
      font-family: 'Montserrat', sans-serif;
    }

    .contact-form input:focus,
    .contact-form textarea:focus {
      border-color: var(--gold);
      outline: none;
      box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
    }

    .contact-form button {
      display: block;
      margin: 15px auto 0;
      padding: 14px 35px;
      background: var(--gradient-gold);
      color: var(--black);
      font-weight: bold;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      transition: var(--transition);
      font-family: 'Cinzel', serif;
      letter-spacing: 1px;
    }

    .contact-form button:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 30px rgba(166, 109, 48, 0.4);
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
      
      .contact-grid {
        grid-template-columns: repeat(2, 1fr);
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
      
      .nav-link {
        color: var(--black);
      }
      
      .mobile-toggle {
        display: block;
        z-index: 1002;
      }
      
      .page-title {
        font-size: 2.5rem;
      }
      
      .contact-grid {
        grid-template-columns: 1fr;
        padding: 0 20px;
      }
      
      .contact-form {
        padding: 30px 20px;
      }
      
      .map-container {
        padding: calc(40px * 0.75) 0 calc(52px * 0.75);
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
      
      .contact-grid {
        gap: 20px;
      }
      
      .contact-card {
        padding: 20px;
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
        <h1 class="page-title">Contact Us</h1>
        <p class="page-subtitle">We'd love to hear from you. Get in touch with ATK Perfumes for any inquiries or assistance.</p>
      </div>
    </div>
  </section>

  <!-- Info Cards -->
  <section class="content-section">
    <div class="contact-grid">
      <!-- Main Branch -->
      <div class="contact-card fade-in">
        <div class="circle-icon">
          <svg style="width: 35px; height: 35px; fill: #d4af37;" viewBox="0 0 24 24">
            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"></path>
          </svg>
        </div>
        <h3>Main Branch</h3>
        <p>79/C Mohammedi House, Mohammed Ali Road, Opposite Ruhani Restaurant, Masjid Bandar Signal, Near Bank of Baroda, Mumbai - 400003</p>
      </div>
      
      <!-- Phone Number -->
      <div class="contact-card fade-in">
        <div class="circle-icon">
          <svg style="width: 35px; height: 35px; fill: #d4af37;" viewBox="0 0 24 24">
            <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"></path>
          </svg>
        </div>
        <h3>Phone Number</h3>
        <p><a href="tel:+919920241094">+91 9876543210</a></p>
        <small>Call us for any inquiries</small>
      </div>
      
      <!-- Email Address -->
      <div class="contact-card fade-in">
        <div class="circle-icon">
          <svg style="width: 35px; height: 35px; fill: #d4af37;" viewBox="0 0 24 24">
            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path>
          </svg>
        </div>
        <h3>Email Address</h3>
        <p><a href="mailto:ATKPerfumes@gmail.com">ATKPerfumes@gmail.com</a></p>
        <small>We respond within 24 hours</small>
      </div>
      
      <!-- Business Hours -->
      <div class="contact-card fade-in">
        <div class="circle-icon">
          <svg style="width: 35px; height: 35px; fill: #d4af37;" viewBox="0 0 24 24">
            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"></path>
          </svg>
        </div>
        <h3>Business Hours</h3>
        <div class="hours">
          <div><strong>Mon-Fri:</strong> 10:00 AM - 7:00 PM</div>
          <div><strong>Saturday:</strong> 10:00 AM - 6:00 PM</div>
          <div><strong>Sunday:</strong> <span style="color:#e33;">Closed</span></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Map -->
  <div class="map-container">
    <iframe class="map" 
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.999244078168!2d2.2922926!3d48.8583736!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66fc7f8a4d6b1%3A0x40b82c3688c9460!2sEiffel%20Tower%2C%20Paris!5e0!3m2!1sen!2sfr!4v1695729040000!5m2!1sen!2sfr" 
      allowfullscreen="" loading="lazy">
    </iframe>
  </div>

  <!-- Contact Form -->
  <section class="content-section">
    <div class="container">
      <div class="contact-form fade-in">
        <h2>Get in Touch</h2>
        <form>
          <label for="name">Full Name</label>
          <input type="text" id="name" placeholder="Enter your name">

          <label for="email">Email</label>
          <input type="email" id="email" placeholder="Enter your email">

          <label for="phone">Phone</label>
          <input type="tel" id="phone" placeholder="Enter your phone number">

          <label for="message">Message</label>
          <textarea id="message" rows="5" placeholder="Enter your message"></textarea>

          <button type="submit">Send Message</button>
        </form>
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
        <p>&copy; 2025 ATK Perfumes. All rights reserved.</p>
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