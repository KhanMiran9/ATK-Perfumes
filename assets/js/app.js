// Main application JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart count
    updateCartCount();
    
    // Initialize search functionality
    initSearch();
    
    // Initialize newsletter form
    initNewsletter();
    
    // Initialize mobile menu
    initMobileMenu();
});

// Mobile menu functionality
function initMobileMenu() {
    const mobileToggle = document.querySelector('.mobile-toggle');
    const nav = document.querySelector('.nav');
    
    if (mobileToggle && nav) {
        mobileToggle.addEventListener('click', function() {
            nav.classList.toggle('active');
            mobileToggle.innerHTML = nav.classList.contains('active') ? 
                '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
        
        // Close mobile menu when clicking on nav links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                nav.classList.remove('active');
                mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
            });
        });
    }
}

// Header scroll effect
function initHeaderScroll() {
    const header = document.querySelector('.header');
    
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }
}

// Cart functionality
let cart = JSON.parse(localStorage.getItem('cart')) || [];

function updateCartCount() {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
    }
}

function addToCart(productId, variationId = null, quantity = 1) {
    // Check if item already exists in cart
    const existingItem = cart.find(item => 
        item.productId === productId && item.variationId === variationId
    );
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            productId,
            variationId,
            quantity,
            addedAt: new Date().toISOString()
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    
    // Show success message
    showToast('Product added to cart');
}

function removeFromCart(productId, variationId = null) {
    cart = cart.filter(item => 
        !(item.productId === productId && item.variationId === variationId)
    );
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
}

function updateCartQuantity(productId, variationId = null, quantity) {
    const item = cart.find(item => 
        item.productId === productId && item.variationId === variationId
    );
    
    if (item) {
        item.quantity = quantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
    }
}

// Search functionality
function initSearch() {
    const searchBtn = document.querySelector('.header-action .fa-search');
    if (searchBtn) {
        searchBtn.closest('.header-action').addEventListener('click', function(e) {
            e.preventDefault();
            const searchTerm = prompt('Enter search term:');
            if (searchTerm) {
                window.location.href = `shop.php?search=${encodeURIComponent(searchTerm)}`;
            }
        });
    }
}

// Newsletter subscription
function initNewsletter() {
    const newsletterForm = document.querySelector('.subscribe-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value;
            
            if (!email) {
                showToast('Please enter your email address', 'error');
                return;
            }
            
            try {
                // Simulate API call - replace with actual endpoint
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                // Simulate success
                showToast('Successfully subscribed to our newsletter!');
                this.reset();
                
            } catch (error) {
                console.error('Error subscribing to newsletter:', error);
                showToast('Subscription failed. Please try again.', 'error');
            }
        });
    }
}

// Toast notifications
function showToast(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.toast').forEach(toast => toast.remove());
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 12px 20px;
        background: ${type === 'success' ? '#4caf50' : '#f44336'};
        color: white;
        border-radius: 4px;
        z-index: 10000;
        opacity: 0;
        transform: translateX(100px);
        transition: all 0.3s ease;
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100px)';
        setTimeout(() => {
            if (toast.parentNode) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Utility functions
function formatPrice(price) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Smooth scrolling for anchor links
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initHeaderScroll();
    initSmoothScroll();
    updateCartCount();
    initSearch();
    initNewsletter();
});

// Export functions for use in other scripts
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateCartQuantity = updateCartQuantity;
window.showToast = showToast;