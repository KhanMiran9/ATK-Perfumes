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
            <i class="fas fa-leaf fa-2x" style="color: #2e8b57;"></i>
        </div>
        <h4 class="highlight-title">Natural Ingredients</h4>
        <p class="highlight-text">Sourced from sustainable partners.</p>
    </div>
    
    <div class="highlight" data-scroll="zoom-in" data-scroll-delay="400">
        <div class="highlight-icon">
            <i class="fas fa-hands fa-2x" style="color: #daa520;"></i>
        </div>
        <h4 class="highlight-title">Handcrafted Perfumes</h4>
        <p class="highlight-text">Each bottle is carefully crafted with precision</p>
    </div>
</div>
            </div>
            
            <div class="story-image" data-scroll="slide-in-right">
                <div class="image-wrapper">
                    <img src="assets/images/9_155f3ba4-9cd4-42a5-8660-d564920b1008.webp" alt="Perfume Making Process">
                    <div class="image-overlay"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Modern Brand Story Section - 2025 Design */
    .brand-story {
        background: linear-gradient(135deg, var(--light-gray) 0%, #f9f9f9 100%);
        position: relative;
        overflow: hidden;
        padding: 8rem 0;
    }

    .brand-story::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 20% 80%, rgba(212, 175, 55, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(192, 192, 192, 0.03) 0%, transparent 50%),
            url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="none"><defs><pattern id="grain" patternUnits="userSpaceOnUse" width="100" height="100"><rect width="100" height="100" fill="%23f5f5f5"/><path d="M0 0h100v100H0z" fill="%23f5f5f5"/><path d="M0 0h1v1H0z" fill="%23e0e0e0" opacity=".05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.6;
        z-index: 0;
    }

    .brand-story .container {
        position: relative;
        z-index: 1;
    }

    .story-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 6rem;
        align-items: center;
        position: relative;
    }

    .story-content {
        padding-right: 3rem;
    }

    .story-content .section-title {
        font-size: 3.5rem;
        margin-bottom: 2.5rem;
        position: relative;
        padding-bottom: 1.5rem;
        background: black;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .story-content .section-title::after {
        content: '';
        position: absolute;
        bottom: 15px;
        left: 60px;
        width: 80px;
        height: 4px;
        background: var(--gradient-gold);
        border-radius: 2px;
    }

    .story-text {
        font-size: 1.15rem;
        line-height: 1.8;
        margin-bottom: 2rem;
        color: var(--muted);
        position: relative;
        padding-left: 1.5rem;
        border-left: 3px solid transparent;
        /* background: linear-gradient(to right, transparent, rgba(212, 175, 55, 0.05)); */
        transition: all 0.5s ease;
    }

    .story-text:hover {
        border-left: 3px solid var(--gold);
        /* background: linear-gradient(to right, transparent, rgba(212, 175, 55, 0.1)); */
        transform: translateX(5px);
    }

    .story-highlights {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2.5rem;
        margin-top: 4rem;
    }

    .highlight {
        background: var(--white);
        padding: 1.5rem 1rem;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        z-index: 1;
        /* border: 1px solid rgba(255, 255, 255, 0.2); */
        /* backdrop-filter: blur(10px); */
    }

    .highlight::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        /* background: var(--gradient-gold); */
        opacity: 0.1;
        transition: left 0.7s ease;
        z-index: -1;
    }

    .highlight:hover::before {
        left: 0;
    }

    .highlight:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .highlight-icon {
        width: 90px;
        height: 90px;
        margin: 0 auto 2rem;
        background: var(--gradient-gold);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        transition: all 0.5s ease;
    }

    .highlight:hover .highlight-icon {
        transform: rotate(10deg) scale(1.1);
        box-shadow: 0 15px 40px rgba(212, 175, 55, 0.4);
    }

    .icon-gif {
        width: 50px;
        height: 50px;
        object-fit: contain;
        filter: brightness(0) invert(1);
    }

    .highlight-title {
        font-size: 1.4rem;
        margin-bottom: 1rem;
        font-family: 'Cinzel', serif;
        color: var(--black);
        position: relative;
    }

    .highlight-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 2px;
        background: var(--gradient-gold);
        transition: width 0.4s ease;
    }

    .highlight:hover .highlight-title::after {
        width: 60px;
    }

    .highlight-text {
        font-size: 1rem;
        color: var(--muted);
        line-height: 1.6;
    }

    .story-image {
        position: relative;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);
        transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .story-image:hover {
        transform: perspective(1000px) rotateY(0) rotateX(0) scale(1.02);
        box-shadow: 0 40px 80px rgba(0, 0, 0, 0.25);
    }

    .image-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
    }

    .story-image img {
        width: 100%;
        height: 600px;
        object-fit: cover;
        display: block;
        transition: transform 1.5s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, rgba(212, 175, 55, 0.1), rgba(192, 192, 192, 0.1));
        opacity: 0;
        transition: opacity 0.8s ease;
    }

    .story-image:hover img {
        transform: scale(1.08);
    }

    .story-image:hover .image-overlay {
        opacity: 1;
    }

    /* Floating Animation for Highlights */
    @keyframes float {
        0% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
        100% {
            transform: translateY(0px);
        }
    }

    .highlight {
        animation: float 6s ease-in-out infinite;
    }

    .highlight:nth-child(2) {
        animation-delay: 1s;
    }

    /* Scroll Animation Classes */
    [data-scroll] {
        opacity: 0;
        transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    [data-scroll="fade-up"] {
        transform: translateY(40px);
    }

    [data-scroll="fade-up"].is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    [data-scroll="slide-in-right"] {
        transform: translateX(80px) perspective(1000px) rotateY(-5deg) rotateX(5deg);
    }

    [data-scroll="slide-in-right"].is-visible {
        opacity: 1;
        transform: translateX(0) perspective(1000px) rotateY(-5deg) rotateX(5deg);
    }

    [data-scroll="zoom-in"] {
        transform: scale(0.8);
    }

    [data-scroll="zoom-in"].is-visible {
        opacity: 1;
        transform: scale(1);
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .story-grid {
            gap: 4rem;
        }
        
        .story-content {
            padding-right: 2rem;
        }
        
        .story-content .section-title {
            font-size: 3rem;
        }
    }

    @media (max-width: 992px) {
        .story-grid {
            grid-template-columns: 1fr;
            gap: 4rem;
        }
        
        .story-content {
            padding-right: 0;
            text-align: center;
        }
        
        .story-content .section-title::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        .story-text {
            padding-left: 0;
            border-left: none;
            border-top: 3px solid transparent;
            padding-top: 1rem;
            background: linear-gradient(to bottom, transparent, rgba(212, 175, 55, 0.05));
        }
        
        .story-text:hover {
            border-left: none;
            border-top: 3px solid var(--gold);
            background: linear-gradient(to bottom, transparent, rgba(212, 175, 55, 0.1));
            transform: translateY(-5px);
        }
        
        .story-highlights {
            grid-template-columns: repeat(2, 1fr);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .story-image {
            transform: none;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .story-image:hover {
            transform: scale(1.02);
        }
        
        [data-scroll="slide-in-right"] {
            transform: translateX(0);
        }
    }

    @media (max-width: 768px) {
        .brand-story {
            padding: 6rem 0;
        }
        
        .story-content .section-title {
            font-size: 2.5rem;
        }
        
        .story-highlights {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .highlight {
            padding: 2rem 1.5rem;
        }
        
        .highlight-icon {
            width: 80px;
            height: 80px;
        }
        
        .icon-gif {
            width: 40px;
            height: 40px;
        }
    }

    @media (max-width: 576px) {
        .brand-story {
            padding: 4rem 0;
        }
        
        .story-content .section-title {
            font-size: 2.2rem;
        }
        
        .story-text {
            font-size: 1rem;
        }
        
        .highlight {
            padding: 1.5rem 1rem;
        }
        
        .highlight-title {
            font-size: 1.2rem;
        }
        
        .highlight-text {
            font-size: 0.9rem;
        }
    }
</style>

<script>
    // Enhanced Scroll Animation Functionality
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
        
        // Add hover effects for story text
        const storyTexts = document.querySelectorAll('.story-text');
        storyTexts.forEach(text => {
            text.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
            });
            
            text.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
    });
</script>