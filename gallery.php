<style>
  .atk-wrapper {
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    gap: 60px;
    padding: 80px 5%;
    justify-content: center;
    align-items: center;
    box-sizing: border-box;
    background: var(--light-gray);
    font-family: 'Cinzel', serif;
  }

  .atk-left {
    flex: 1 1 400px;
    max-width: 600px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .atk-left h2 {
    font-size: 42px;
    font-weight: 700;
    margin-bottom: 40px;
    text-align: left;
    color: var(--black);
    position: relative;
    font-family: 'Cinzel', serif;
  }

  .atk-left h2:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -12px;
    width: 80px;
    height: 4px;
    background: var(--gradient-gold);
  }

  .atk-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
    margin-bottom: 40px;
  }

  .atk-feature {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 25px 20px;
    border-radius: var(--radius);
    background: var(--white);
    box-shadow: var(--shadow);
    transition: var(--transition);
  }

  .atk-feature:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
  }

  .atk-feature img {
    width: 70px;
    height: 70px;
    margin-bottom: 15px;
    object-fit: contain;
    border-radius: 50%;
  }

  .atk-feature h3 {
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 8px;
    color: var(--black);
    font-family: 'Cinzel', serif;
  }

  .atk-feature p {
    margin: 0;
    font-size: 15px;
    color: var(--muted);
    line-height: 1.5;
  }

  /* Shine Animation */
  @keyframes shine {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  .atk-btn {
    background: var(--gradient-gold);
   
    color: var(--black) !important;
    border: none;
    padding: 16px 36px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 16px;
    transition: var(--transition);
    text-align: center;
    cursor: pointer;
    display: inline-block;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    font-family: 'Cinzel', serif;
    letter-spacing: 1px;
  }

  .atk-btn:hover {
    animation-play-state: paused;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
  }

  .atk-right {
    display: flex;
    flex: 1 1 600px;
    gap: 16px;
    max-width: 800px;
    position: relative;
  }

  .atk-column {
    flex: 1;
    overflow: hidden;
    height: 750px;
    position: relative;
    border-radius: var(--radius);
  }

  .atk-scroll {
    display: flex;
    flex-direction: column;
    gap: 16px;
    animation: scrollVertical 30s linear infinite;
  }

  .atk-column:nth-child(2) .atk-scroll {
    animation-duration: 35s;
  }

  .atk-column:nth-child(3) .atk-scroll {
    animation-duration: 40s;
  }

  .atk-scroll img {
    width: 100%;
    height: 340px;
    object-fit: cover;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    transition: var(--transition);
  }

  .atk-scroll img:hover {
    transform: scale(1.02);
  }

  .atk-column:hover .atk-scroll {
    animation-play-state: paused;
  }

  .atk-column:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 60px;
    background: linear-gradient(to top, rgba(245, 245, 245, 0.9), transparent);
    pointer-events: none;
    z-index: 2;
  }

  @keyframes scrollVertical {
    0% { transform: translateY(0); }
    100% { transform: translateY(-50%); }
  }

  @media (max-width: 1200px) {
    .atk-wrapper {
      gap: 40px;
      padding: 60px 5%;
    }
    
    .atk-left h2 {
      font-size: 36px;
    }
    
    .atk-grid {
      gap: 20px;
    }
    
    .atk-feature {
      padding: 20px 15px;
    }
    
    .atk-feature img {
      width: 60px;
      height: 60px;
    }
    
    .atk-feature h3 {
      font-size: 20px;
    }
    
    .atk-column {
      height: 650px;
    }
    
    .atk-scroll img {
      height: 200px;
    }
  }

  @media (max-width: 1024px) {
    .atk-wrapper {
      flex-direction: column;
    }

    .atk-left {
      max-width: 100%;
      align-items: center;
      text-align: center;
    }

    .atk-left h2 {
      text-align: center;
    }
    
    .atk-left h2:after {
      left: 50%;
      transform: translateX(-50%);
    }

    .atk-grid {
      grid-template-columns: repeat(2, 1fr);
      max-width: 600px;
    }

    .atk-btn {
      margin: 0 auto;
    }

    .atk-right {
      max-width: 100%;
    }
  }

  @media (max-width: 768px) {
    .atk-wrapper {
      padding: 40px 5%;
    }
    
    .atk-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
    }
    
    .atk-feature {
      padding: 15px 10px;
    }
    
    .atk-feature img {
      width: 50px;
      height: 50px;
    }
    
    .atk-feature h3 {
      font-size: 18px;
    }
    
    .atk-feature p {
      font-size: 14px;
    }
    
    .atk-btn {
      padding: 14px 28px;
      font-size: 15px;
    }
    
    .atk-right {
      flex-direction: row;
    }
    
    .atk-column {
      height: 500px;
    }
    
    .atk-scroll img {
      height: 150px;
    }
  }

  @media (max-width: 480px) {
    .atk-left h2 {
      font-size: 32px;
    }
    
    .atk-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
    }
    
    .atk-feature {
      padding: 12px 8px;
    }
    
    .atk-feature img {
      width: 45px;
      height: 45px;
    }
    
    .atk-feature h3 {
      font-size: 16px;
    }
    
    .atk-feature p {
      font-size: 13px;
    }
    
    .atk-btn {
      padding: 12px 24px;
      font-size: 14px;
    }
    
    .atk-column {
      height: 450px;
    }
    
    .atk-scroll img {
      height: 130px;
    }
  }
</style>

<div class="atk-wrapper">
  <!-- Left Section -->
  <div class="atk-left">
    <h2>WHY ATK PERFUMES</h2>
    <div class="atk-grid">
      <div class="atk-feature">
        <img src="https://cdn.shopify.com/s/files/1/0961/8279/6602/files/moroccan.gif?v=1755940374" alt="Premium Fragrances" />
        <div>
          <h3>500+</h3>
          <p>Premium Fragrance Designs</p>
        </div>
      </div>
      <div class="atk-feature">
        <img src="https://cdn.shopify.com/s/files/1/0961/8279/6602/files/jewelry.gif?v=1755940374" alt="Elegant Bottles" />
        <div>
          <h3>100+</h3>
          <p>Elegant Bottle Designs</p>
        </div>
      </div>
      <div class="atk-feature">
        <img src="https://cdn.shopify.com/s/files/1/0961/8279/6602/files/clothes.gif?v=1755940374" alt="Scent Collections" />
        <div>
          <h3>100+</h3>
          <p>Scent Collections</p>
        </div>
      </div>
      <div class="atk-feature">
        <img src="https://cdn.shopify.com/s/files/1/0961/8279/6602/files/serum.gif?v=1755940375" alt="Personal Care" />
        <div>
          <h3>100+</h3>
          <p>Personal Care Products</p>
        </div>
      </div>
    </div>
    <button class="atk-btn" onclick="window.location.href='#products'">
      SHOP COLLECTION
    </button>
  </div>

  <!-- Right Section: Scrolling Columns -->
  <div class="atk-right">
    <div class="atk-column">
      <div class="atk-scroll">
        <img src="assets/images/1_bc36bd71-c5b2-463a-aeb3-3a27ca89fde7.webp" alt="Perfume Collection" />
        <img src="assets/images/images (1).jpg" alt="Perfume Collection" />
        <img src="assets/images/images (2).jpg" alt="Perfume Collection" />
        <img src="assets/images/images.jpg" alt="Perfume Collection" />
        <!-- Duplicate for seamless scroll -->
      <img src="assets/images/1_bc36bd71-c5b2-463a-aeb3-3a27ca89fde7.webp" alt="Perfume Collection" />
        <img src="assets/images/images (1).jpg" alt="Perfume Collection" />
        <img src="assets/images/images (2).jpg" alt="Perfume Collection" />
        <img src="assets/images/images.jpg" alt="Perfume Collection" />
      </div>
    </div>
    <div class="atk-column">
      <div class="atk-scroll">
        <img src="assets/images/1_bc36bd71-c5b2-463a-aeb3-3a27ca89fde7.webp" alt="Perfume Collection" />
        <img src="assets/images/images (1).jpg" alt="Perfume Collection" />
        <img src="assets/images/images (2).jpg" alt="Perfume Collection" />
        <img src="assets/images/images.jpg" alt="Perfume Collection" />
        <!-- Duplicate for seamless scroll -->
        <img src="assets/images/1_bc36bd71-c5b2-463a-aeb3-3a27ca89fde7.webp" alt="Perfume Collection" />
        <img src="assets/images/images (1).jpg" alt="Perfume Collection" />
        <img src="assets/images/images (2).jpg" alt="Perfume Collection" />
        <img src="assets/images/images.jpg" alt="Perfume Collection" />
      </div>
    </div>
    <div class="atk-column">
      <div class="atk-scroll">
 <img src="assets/images/1_bc36bd71-c5b2-463a-aeb3-3a27ca89fde7.webp" alt="Perfume Collection" />
        <img src="assets/images/images (1).jpg" alt="Perfume Collection" />
        <img src="assets/images/images (2).jpg" alt="Perfume Collection" />
        <img src="assets/images/images.jpg" alt="Perfume Collection" />
        <!-- Duplicate for seamless scroll -->
        <img src="assets/images/1_bc36bd71-c5b2-463a-aeb3-3a27ca89fde7.webp" alt="Perfume Collection" />
        <img src="assets/images/images (1).jpg" alt="Perfume Collection" />
        <img src="assets/images/images (2).jpg" alt="Perfume Collection" />
        <img src="assets/images/images.jpg" alt="Perfume Collection" />
      </div>
    </div>
  </div>
</div>