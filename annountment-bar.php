<style>
  /* Import Font Awesome */
  @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
  
  /* 🔝 TOP SLIDER */
  .announcement-slider-wrapper {
    background: linear-gradient(90deg, #7F6051, #b58c65, #f3dab3, #b58c65, #7F6051);
    background-size: 400% 100%;
    animation: shine 20s linear infinite;
    color: #fff8f0;
    font-size: 14px;
    font-weight: 500;
    height: 36px;
    overflow: hidden;
    position: relative;
    display: flex;
    align-items: center;
  }

  .announcement-slider-track {
    display: flex;
    transition: transform 0.8s ease-in-out;
    will-change: transform;
  }

  .announcement-slide {
    min-width: 100vw;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 20px;
    box-sizing: border-box;
    white-space: nowrap;
    gap: 10px;
  }

  .announcement-icon {
    font-size: 14px;
    display: inline-flex;
    color: #fcecd2;
    flex-shrink: 0;
  }

  .announcement-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .promo-badge {
    background: rgba(252, 236, 210, 0.15);
    border: 1px solid rgba(252, 236, 210, 0.3);
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 11px;
    margin-left: 12px;
    color: #fcecd2;
    flex-shrink: 0;
  }

  @keyframes shine {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  /* 🔻 BOTTOM MARQUEE */
  .custom-marquee-wrapper {
    overflow: hidden;
    white-space: nowrap;
    background: linear-gradient(90deg, #7F6051, #b58c65, #f3dab3, #b58c65, #7F6051);
    background-size: 400% 100%;
    animation: shine 20s linear infinite;
    padding: 8px 0;
    position: relative;
  }

  .custom-marquee-track {
    display: inline-flex;
    animation: scroll-left 30s linear infinite;
  }

  .custom-marquee-wrapper:hover .custom-marquee-track {
    animation-play-state: paused;
  }

  .custom-marquee-item {
    display: inline-flex;
    align-items: center;
    font-weight: 600;
    font-size: 13px;
    color: #fff8f0;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    padding-right: 50px;
    gap: 12px;
  }

  .marquee-icon {
    font-size: 12px;
    display: inline-flex;
    color: #fcecd2;
  }

  .shop-now-btn {
    color: #fcecd2;
    text-decoration: underline;
    font-weight: 500;
    margin-left: 5px;
    transition: color 0.3s ease;
  }

  .shop-now-btn:hover {
    color: #E6CBA8;
  }

  @keyframes scroll-left {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-50%); }
  }

  /* Mobile responsiveness */
  @media screen and (max-width: 768px) {
    .announcement-slider-wrapper {
      height: 32px;
    }
    
    .announcement-slide {
      padding: 0 15px;
      gap: 8px;
    }
    
    .announcement-icon {
      font-size: 12px;
    }
    
    .announcement-text {
      font-size: 12px;
      max-width: 65vw;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    .promo-badge {
      font-size: 10px;
      padding: 2px 6px;
      margin-left: 8px;
    }
  }

  @media screen and (max-width: 480px) {
    .announcement-slider-wrapper {
      height: 30px;
      font-size: 11px;
    }
    
    .announcement-slide {
      padding: 0 12px;
      gap: 6px;
    }
    
    .announcement-text {
      font-size: 11px;
      max-width: 60vw;
    }
    
    .promo-badge {
      font-size: 9px;
      padding: 1px 5px;
      margin-left: 6px;
    }
    
    /* Shorten text for very small screens */
    .mobile-short-text {
      display: inline;
    }
    
    .full-text {
      display: none;
    }
  }

  @media screen and (max-width: 360px) {
    .announcement-text {
      font-size: 10px;
      max-width: 55vw;
    }
    
    .announcement-icon {
      font-size: 11px;
    }
    
    .promo-badge {
      font-size: 8px;
    }
  }
</style>

<!-- 🔝 TOP SLIDER -->
<div class="announcement-slider-wrapper" role="status" aria-live="polite">
  <div class="announcement-slider-track" id="announcement-slider">
    <div class="announcement-slide">
      <i class="fas fa-tag announcement-icon"></i>
      <span class="announcement-text">
        <span class="full-text">100% Original</span>
        <span class="mobile-short-text">100% Original</span>
      </span>
      <div class="promo-badge">Trusted</div>
    </div>
    <div class="announcement-slide">
      <i class="fas fa-truck announcement-icon"></i>
      <span class="announcement-text">Shipping Across India</span>
      <div class="promo-badge">all over</div>
    </div>
    <div class="announcement-slide">
      <i class="fas fa-percent announcement-icon"></i>
      <span class="announcement-text">
        <span class="full-text">Premium Quality</span>
        <span class="mobile-short-text">Premium Quality</span>
      </span>
      <div class="promo-badge">Luxury</div>
    </div>
    <div class="announcement-slide">
      <i class="fas fa-percent announcement-icon"></i>
      <span class="announcement-text">
        <span class="full-text">Carefully Packed With Love</span>
        <span class="mobile-short-text">Carefully Packed With Love</span>
      </span>
      <div class="promo-badge">Precious</div>
    </div>
  </div>
</div>

<!-- 🔻 BOTTOM MARQUEE -->
<div class="custom-marquee-wrapper">
  <div class="custom-marquee-track">
    <div class="custom-marquee-item">
      <i class="fas fa-store marquee-icon"></i>
      HKR Hijabs &amp; More — Fine hijabs &amp; luxury imitation jewelry
    </div>
    <div class="custom-marquee-item">
      <i class="fas fa-globe marquee-icon"></i>
      World shipping available internationally
    </div>
    <div class="custom-marquee-item">
      <i class="fas fa-map-marker-alt marquee-icon"></i>
      Visit our store at 79/C Mohammedi House, Mohammed Ali Road, Masjid Bunder, Mumbai
    </div>
    <div class="custom-marquee-item">
      <i class="fab fa-whatsapp marquee-icon"></i>
      WhatsApp: +91-99202-41094
    </div>
    <div class="custom-marquee-item">
      <i class="fas fa-gift marquee-icon"></i>
      Christmas collection arriving soon
    </div>
    <!-- Duplicate for seamless loop -->
    <div class="custom-marquee-item">
      <i class="fas fa-store marquee-icon"></i>
      HKR Hijabs &amp; More — Fine hijabs &amp; luxury imitation jewelry
    </div>
    <div class="custom-marquee-item">
      <i class="fas fa-globe marquee-icon"></i>
      World shipping available internationally
    </div>
    <div class="custom-marquee-item">
      <i class="fas fa-map-marker-alt marquee-icon"></i>
      Visit our store at 79/C Mohammedi House, Mohammed Ali Road, Masjid Bunder, Mumbai
    </div>
    <div class="custom-marquee-item">
      <i class="fab fa-whatsapp marquee-icon"></i>
      WhatsApp: +91-99202-41094
    </div>
    <div class="custom-marquee-item">
      <i class="fas fa-gift marquee-icon"></i>
      Christmas collection arriving soon
    </div>
  </div>
</div>

<!-- ✅ JS FOR TOP SLIDER -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const slider = document.getElementById("announcement-slider");
    const slides = Array.from(slider.children);
    let index = 0;

    slides.forEach(slide => slider.appendChild(slide.cloneNode(true)));

    const slideCount = slides.length;
    const interval = 6000;

    const nextSlide = () => {
      index++;
      slider.style.transition = "transform 0.8s ease-in-out";
      slider.style.transform = `translateX(-${index * 100}vw)`;

      if (index >= slideCount) {
        setTimeout(() => {
          slider.style.transition = "none";
          slider.style.transform = "translateX(0)";
          index = 0;
        }, 800);
      }
    };

    setInterval(nextSlide, interval);
  });
</script>