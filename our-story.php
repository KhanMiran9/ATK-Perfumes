<!-- Brand Story -->
<section class="section brand-story" id="story">
    <div class="container">
        <div class="story-grid">
            <div class="story-content">
                <h2 class="section-title" data-scroll="fade-up">Our Story</h2>
                <p class="story-text" data-scroll="fade-up" data-scroll-delay="100">Founded in 2010, ATK Perfumes began as a small boutique perfumery with a passion for creating exceptional scents that tell a story. Our journey started in a quaint studio in Paris, where our master perfumers combined traditional techniques with innovative approaches.</p>
                <p class="story-text" data-scroll="fade-up" data-scroll-delay="200">Today, we've grown into a globally recognized brand, but our commitment to quality, craftsmanship, and authenticity remains unchanged. Each fragrance is still meticulously crafted by hand, using only the finest ingredients from around the world.</p>
                
                <div class="story-highlights">
                    <div class="highlight" data-scroll="zoom-in" data-scroll-delay="300">
                        <div class="highlight-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h4 class="highlight-title">Natural Ingredients</h4>
                        <p class="highlight-text">Sourced from sustainable partners worldwide</p>
                    </div>
                    
                    <div class="highlight" data-scroll="zoom-in" data-scroll-delay="400">
                        <div class="highlight-icon">
                            <i class="fas fa-hand-sparkles"></i>
                        </div>
                        <h4 class="highlight-title">Handcrafted</h4>
                        <p class="highlight-text">Each bottle is carefully crafted with precision</p>
                    </div>
                </div>
            </div>
            
            <div class="story-image" data-scroll="slide-in-right">
                <div class="image-wrapper">
                    <img src="https://images.unsplash.com/photo-1615634376658-7d0cd0e18982?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Perfume Making Process">
                    <div class="image-overlay"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
    /* Modern Brand Story Section */
.brand-story {
    background: linear-gradient(135deg, var(--light-gray) 0%, #f9f9f9 100%);
    position: relative;
    overflow: hidden;
}

.brand-story::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="none"><defs><pattern id="grain" patternUnits="userSpaceOnUse" width="100" height="100"><rect width="100" height="100" fill="%23f5f5f5"/><path d="M0 0h100v100H0z" fill="%23f5f5f5"/><path d="M0 0h1v1H0z" fill="%23e0e0e0" opacity=".05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
    z-index: 0;
}

.brand-story .container {
    position: relative;
    z-index: 1;
}

.story-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5rem;
    align-items: center;
    position: relative;
}

.story-content {
    padding-right: 2rem;
}

.story-content .section-title {
    font-size: 3rem;
    margin-bottom: 2rem;
    position: relative;
    padding-bottom: 1rem;
}

.story-content .section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: var(--gradient-gold);
    border-radius: 2px;
}

.story-text {
    font-size: 1.1rem;
    line-height: 1.8;
    margin-bottom: 1.5rem;
    color: var(--muted);
    position: relative;
}

.story-highlights {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-top: 3rem;
}

.highlight {
    background: var(--white);
    padding: 2rem 1.5rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.highlight::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: var(--gradient-gold);
    opacity: 0.05;
    transition: left 0.6s ease;
    z-index: -1;
}

.highlight:hover::before {
    left: 0;
}

.highlight:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.highlight-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 1.5rem;
    background: var(--gradient-gold);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: var(--white);
    position: relative;
    overflow: hidden;
}

.highlight-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.highlight:hover .highlight-icon::before {
    transform: translateX(100%);
}

.highlight-title {
    font-size: 1.2rem;
    margin-bottom: 0.75rem;
    font-family: 'Cinzel', serif;
    color: var(--black);
}

.highlight-text {
    font-size: 0.95rem;
    color: var(--muted);
    line-height: 1.5;
}

.story-image {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 16px;
}

.story-image img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 1.2s ease;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, rgba(212, 175, 55, 0.1), rgba(192, 192, 192, 0.1));
    opacity: 0;
    transition: opacity 0.6s ease;
}

.story-image:hover img {
    transform: scale(1.05);
}

.story-image:hover .image-overlay {
    opacity: 1;
}

/* Scroll Animation Classes */
[data-scroll] {
    opacity: 0;
    transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

[data-scroll="fade-up"] {
    transform: translateY(30px);
}

[data-scroll="fade-up"].is-visible {
    opacity: 1;
    transform: translateY(0);
}

[data-scroll="slide-in-right"] {
    transform: translateX(50px);
}

[data-scroll="slide-in-right"].is-visible {
    opacity: 1;
    transform: translateX(0);
}

[data-scroll="zoom-in"] {
    transform: scale(0.9);
}

[data-scroll="zoom-in"].is-visible {
    opacity: 1;
    transform: scale(1);
}

/* Responsive Design */
@media (max-width: 992px) {
    .story-grid {
        grid-template-columns: 1fr;
        gap: 3rem;
    }
    
    .story-content {
        padding-right: 0;
        text-align: center;
    }
    
    .story-content .section-title::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .story-highlights {
        grid-template-columns: 1fr;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }
}

@media (max-width: 768px) {
    .story-content .section-title {
        font-size: 2.5rem;
    }
    
    .highlight {
        padding: 1.5rem 1rem;
    }
}

@media (max-width: 576px) {
    .story-content .section-title {
        font-size: 2rem;
    }
    
    .story-text {
        font-size: 1rem;
    }
}
</style>
<script>
    // Scroll Animation Functionality
function initScrollAnimations() {
    const scrollElements = document.querySelectorAll('[data-scroll]');
    
    const elementInView = (el, dividend = 1) => {
        const elementTop = el.getBoundingClientRect().top;
        return (
            elementTop <= (window.innerHeight || document.documentElement.clientHeight) / dividend
        );
    };
    
    const elementOutofView = (el) => {
        const elementTop = el.getBoundingClientRect().top;
        return (
            elementTop > (window.innerHeight || document.documentElement.clientHeight)
        );
    };
    
    const displayScrollElement = (element) => {
        element.classList.add('is-visible');
    };
    
    const hideScrollElement = (element) => {
        element.classList.remove('is-visible');
    };
    
    const handleScrollAnimation = () => {
        scrollElements.forEach((el) => {
            if (elementInView(el, 1.2)) {
                // Apply delay if specified
                const delay = el.getAttribute('data-scroll-delay') || 0;
                setTimeout(() => {
                    displayScrollElement(el);
                }, parseInt(delay));
            } else if (elementOutofView(el)) {
                hideScrollElement(el);
            }
        });
    };
    
    // Throttle scroll events for performance
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                handleScrollAnimation();
                ticking = false;
            });
            ticking = true;
        }
    });
    
    // Initial check on page load
    window.addEventListener('load', handleScrollAnimation);
    handleScrollAnimation();
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initScrollAnimations();
});
</script>