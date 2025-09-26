<!-- Google Reviews Section -->
<section class="section" id="reviews">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Customer Reviews</h2>
            <p class="section-subtitle">What our customers are saying about ATK Perfumes</p>
        </div>
        
        <div class="google-reviews-container">
            <div class="reviews-header">
                <div class="rating-overview">
                    <div class="average-rating">
                        <span class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </span>
                        <span class="rating-number">4.8</span>
                        <span class="rating-text">out of 5</span>
                    </div>
                    <div class="total-reviews">Based on 247 reviews</div>
                </div>
                <div class="review-stats">
                    <div class="stat-item">
                        <span class="stat-label">5 star</span>
                        <div class="stat-bar">
                            <div class="stat-fill" style="width: 78%"></div>
                        </div>
                        <span class="stat-percent">78%</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">4 star</span>
                        <div class="stat-bar">
                            <div class="stat-fill" style="width: 15%"></div>
                        </div>
                        <span class="stat-percent">15%</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">3 star</span>
                        <div class="stat-bar">
                            <div class="stat-fill" style="width: 5%"></div>
                        </div>
                        <span class="stat-percent">5%</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">2 star</span>
                        <div class="stat-bar">
                            <div class="stat-fill" style="width: 1%"></div>
                        </div>
                        <span class="stat-percent">1%</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">1 star</span>
                        <div class="stat-bar">
                            <div class="stat-fill" style="width: 1%"></div>
                        </div>
                        <span class="stat-percent">1%</span>
                    </div>
                </div>
            </div>
            
            <div class="reviews-slider-container">
                <div class="reviews-slider" id="reviewsSlider">
                    <!-- Review cards will be populated by JavaScript -->
                </div>
                
                <div class="slider-controls">
                    <button class="slider-btn prev-btn" aria-label="Previous reviews">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="slider-btn next-btn" aria-label="Next reviews">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <div class="slider-dots" id="sliderDots"></div>
            </div>
            
          
        </div>
    </div>
</section>

<style>
    /* Google Reviews Section Styles */
    .google-reviews-container {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 2.5rem;
        margin-top: 2rem;
    }

    .reviews-header {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        margin-bottom: 3rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }

    .average-rating {
        text-align: center;
        margin-bottom: 1rem;
    }

    .rating-stars {
        display: block;
        margin-bottom: 0.5rem;
    }

    .rating-stars i {
        color: var(--gold);
        font-size: 1.8rem;
        margin: 0 2px;
    }

    .rating-number {
        font-size: 3rem;
        font-weight: 700;
        color: var(--black);
        display: block;
        line-height: 1;
        margin-bottom: 0.25rem;
        font-family: 'Cinzel', serif;
    }

    .rating-text {
        color: var(--muted);
        font-size: 1rem;
    }

    .total-reviews {
        color: var(--muted);
        font-size: 0.9rem;
    }

    .review-stats {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .stat-label {
        width: 50px;
        font-size: 0.9rem;
        color: var(--muted);
    }

    .stat-bar {
        flex: 1;
        height: 8px;
        background: rgba(0,0,0,0.1);
        border-radius: 4px;
        overflow: hidden;
    }

    .stat-fill {
        height: 100%;
        background: var(--gradient-gold);
        border-radius: 4px;
        transition: width 0.8s ease;
    }

    .stat-percent {
        width: 35px;
        font-size: 0.9rem;
        color: var(--muted);
        text-align: right;
    }

    /* Reviews Slider */
    .reviews-slider-container {
        position: relative;
        margin-bottom: 2rem;
    }

    .reviews-slider {
        display: flex;
        gap: 1.5rem;
        overflow-x: hidden;
        scroll-behavior: smooth;
        padding: 1rem 0;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .reviews-slider::-webkit-scrollbar {
        display: none;
    }

    .review-card {
        flex: 0 0 calc(33.333% - 1rem);
        background: #f8f5f0;
        border-radius: var(--radius);
        padding: 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        min-height: 280px;
        display: flex;
        flex-direction: column;
    }

    .review-card:hover {
        transform: translateY(-5px);
      
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .reviewer-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .reviewer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        background: var(--gradient-gold);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--black);
        font-weight: 600;
        font-size: 1.1rem;
    }

    .reviewer-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .reviewer-details h4 {
        margin: 0 0 0.25rem 0;
        font-family: 'Cinzel', serif;
        font-size: 1rem;
    }

    .review-date {
        color: var(--muted);
        font-size: 0.8rem;
    }

    .review-rating {
        display: flex;
        gap: 2px;
    }

    .review-rating i {
        color: var(--gold);
        font-size: 0.9rem;
    }

    .review-content {
        flex: 1;
        margin-bottom: 1rem;
    }

    .review-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-family: 'Cinzel', serif;
        color: var(--black);
    }

    .review-text {
        color: var(--muted);
        line-height: 1.5;
        font-size: 0.9rem;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .review-product {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(0,0,0,0.1);
        font-size: 0.8rem;
        color: var(--muted);
    }

    .review-product i {
        color: var(--gold);
    }

    /* Slider Controls */
    .slider-controls {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        display: flex;
        justify-content: space-between;
        padding: 0 1rem;
        pointer-events: none;
        z-index: 5;
    }

    .slider-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--white);
        border: 1px solid rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
        pointer-events: all;
        color: var(--black);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .slider-btn:hover {
        background: var(--gradient-gold);
        color: var(--black);
        transform: scale(1.1);
    }

    .slider-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .slider-btn:disabled:hover {
        background: var(--white);
        color: var(--black);
    }

    /* Slider Dots */
    .slider-dots {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
    }

    .slider-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(0,0,0,0.2);
        cursor: pointer;
        transition: var(--transition);
    }

    .slider-dot.active {
        background: var(--gold);
        transform: scale(1.2);
    }

    /* Reviews Footer */
    .reviews-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(0,0,0,0.1);
    }

    .view-all-reviews {
        color: var(--gold);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
    }

    .view-all-reviews:hover {
        text-decoration: underline;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .reviews-header {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .review-card {
            flex: 0 0 calc(50% - 1rem);
        }
    }

    @media (max-width: 768px) {
        .google-reviews-container {
            padding: 1.5rem;
        }
        
        .review-card {
            flex: 0 0 calc(100% - 1rem);
        }
        
        .slider-controls {
            display: none;
        }
        
        .reviews-footer {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .stat-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .stat-bar {
            width: 100%;
        }
        
        .stat-percent {
            text-align: left;
        }
    }
</style>

<script>
    // Reviews Data
    const reviewsData = [
        {
            id: 1,
            reviewer: "Sarah Mitchell",
            avatar: "SM",
            rating: 5,
            date: "2 weeks ago",
            title: "Absolutely Divine Fragrance",
            text: "The Mystic Oud is everything I hoped for and more. The scent lasts all day and I've received so many compliments. The packaging was luxurious and delivery was quick.",
            product: "Mystic Oud"
        },
        {
            id: 2,
            reviewer: "James Wilson",
            avatar: "JW",
            rating: 5,
            date: "1 month ago",
            title: "Best Perfume I've Ever Owned",
            text: "As a collector of fine fragrances, I can confidently say ATK Perfumes stands above the rest. The Noir Essence has incredible depth and complexity.",
            product: "Noir Essence"
        },
        {
            id: 3,
            reviewer: "Emma Rodriguez",
            avatar: "ER",
            rating: 4,
            date: "3 weeks ago",
            title: "Beautiful Floral Scent",
            text: "Rose Elixir is a perfect balance of floral and modern notes. It's not too overpowering but still makes a statement. Will definitely purchase again.",
            product: "Rose Elixir"
        },
        {
            id: 4,
            reviewer: "Michael Chen",
            avatar: "MC",
            rating: 5,
            date: "2 months ago",
            title: "Exceeded All Expectations",
            text: "I was hesitant about the price, but after receiving Citrus Bloom, I understand why it's worth every penny. The quality is exceptional.",
            product: "Citrus Bloom"
        },
        {
            id: 5,
            reviewer: "Olivia Thompson",
            avatar: "OT",
            rating: 5,
            date: "3 days ago",
            title: "Perfect Everyday Fragrance",
            text: "This has become my signature scent. It's versatile enough for both day and evening wear. The customer service was also excellent.",
            product: "Citrus Bloom"
        },
        {
            id: 6,
            reviewer: "Daniel Park",
            avatar: "DP",
            rating: 4,
            date: "1 week ago",
            title: "Impressive Longevity",
            text: "The scent lasts much longer than other premium perfumes I've tried. The only reason I'm not giving 5 stars is I wish there was a larger bottle option.",
            product: "Mystic Oud"
        }
    ];

    // Initialize Reviews Slider
    document.addEventListener('DOMContentLoaded', function() {
        const reviewsSlider = document.getElementById('reviewsSlider');
        const sliderDots = document.getElementById('sliderDots');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        
        let currentSlide = 0;
        const slidesToShow = window.innerWidth < 768 ? 1 : window.innerWidth < 992 ? 2 : 3;
        const totalSlides = Math.ceil(reviewsData.length / slidesToShow);
        
        // Create review cards
        function createReviewCards() {
            reviewsSlider.innerHTML = '';
            
            reviewsData.forEach(review => {
                const reviewCard = document.createElement('div');
                reviewCard.className = 'review-card fade-in';
                
                // Generate star rating
                let starsHtml = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= review.rating) {
                        starsHtml += '<i class="fas fa-star"></i>';
                    } else if (i - 0.5 === review.rating) {
                        starsHtml += '<i class="fas fa-star-half-alt"></i>';
                    } else {
                        starsHtml += '<i class="far fa-star"></i>';
                    }
                }
                
                reviewCard.innerHTML = `
                    <div class="review-header">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">${review.avatar}</div>
                            <div class="reviewer-details">
                                <h4>${review.reviewer}</h4>
                                <div class="review-date">${review.date}</div>
                            </div>
                        </div>
                        <div class="review-rating">
                            ${starsHtml}
                        </div>
                    </div>
                    <div class="review-content">
                        <h5 class="review-title">${review.title}</h5>
                        <p class="review-text">${review.text}</p>
                    </div>
                    <div class="review-product">
                        <i class="fas fa-tag"></i>
                        <span>Purchased: ${review.product}</span>
                    </div>
                `;
                
                reviewsSlider.appendChild(reviewCard);
            });
        }
        
        // Create slider dots
        function createSliderDots() {
            sliderDots.innerHTML = '';
            
            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('div');
                dot.className = `slider-dot ${i === currentSlide ? 'active' : ''}`;
                dot.addEventListener('click', () => goToSlide(i));
                sliderDots.appendChild(dot);
            }
        }
        
        // Go to specific slide
        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            const slideWidth = reviewsSlider.querySelector('.review-card').offsetWidth + 24; // 24px gap
            reviewsSlider.scrollTo({
                left: slideIndex * slideWidth * slidesToShow,
                behavior: 'smooth'
            });
            
            updateSliderDots();
            updateNavButtons();
        }
        
        // Next slide
        function nextSlide() {
            if (currentSlide < totalSlides - 1) {
                goToSlide(currentSlide + 1);
            } else {
                goToSlide(0);
            }
        }
        
        // Previous slide
        function prevSlide() {
            if (currentSlide > 0) {
                goToSlide(currentSlide - 1);
            } else {
                goToSlide(totalSlides - 1);
            }
        }
        
        // Update slider dots
        function updateSliderDots() {
            const dots = sliderDots.querySelectorAll('.slider-dot');
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }
        
        // Update navigation buttons
        function updateNavButtons() {
            if (prevBtn && nextBtn) {
                prevBtn.disabled = currentSlide === 0;
                nextBtn.disabled = currentSlide === totalSlides - 1;
            }
        }
        
        // Initialize
        createReviewCards();
        createSliderDots();
        updateNavButtons();
        
        // Event listeners
        if (prevBtn) {
            prevBtn.addEventListener('click', prevSlide);
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', nextSlide);
        }
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const newSlidesToShow = window.innerWidth < 768 ? 1 : window.innerWidth < 992 ? 2 : 3;
            if (newSlidesToShow !== slidesToShow) {
                // Reinitialize on breakpoint change
                location.reload();
            }
        });
        
        // Auto-advance slides (optional)
        let autoSlideInterval = setInterval(nextSlide, 5000);
        
        // Pause auto-slide on hover
        reviewsSlider.addEventListener('mouseenter', () => {
            clearInterval(autoSlideInterval);
        });
        
        reviewsSlider.addEventListener('mouseleave', () => {
            autoSlideInterval = setInterval(nextSlide, 5000);
        });
    });
</script>