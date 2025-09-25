// Shop Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const applyFiltersBtn = document.querySelector('.apply-filters');
    const clearFiltersBtn = document.querySelector('.clear-filters');
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const formData = new FormData();
            
            // Get all filter values
            const categoryCheckboxes = document.querySelectorAll('input[name="category"]:checked');
            const minPrice = document.querySelector('input[name="min_price"]').value;
            const maxPrice = document.querySelector('input[name="max_price"]').value;
            
            // Update URL parameters
            if (categoryCheckboxes.length > 0) {
                const categories = Array.from(categoryCheckboxes).map(cb => cb.value);
                urlParams.set('category', categories.join(','));
            } else {
                urlParams.delete('category');
            }
            
            if (minPrice) urlParams.set('min_price', minPrice);
            else urlParams.delete('min_price');
            
            if (maxPrice) urlParams.set('max_price', maxPrice);
            else urlParams.delete('max_price');
            
            // Redirect with new filters
            window.location.href = 'shop.php?' + urlParams.toString();
        });
    }
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            window.location.href = 'shop.php';
        });
    }
    
    // Sort functionality
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort', this.value);
            window.location.href = 'shop.php?' + urlParams.toString();
        });
        
        // Set current sort value
        const urlParams = new URLSearchParams(window.location.search);
        const currentSort = urlParams.get('sort');
        if (currentSort) {
            sortSelect.value = currentSort;
        }
    }
    
    // Quick view modal
    const quickViewButtons = document.querySelectorAll('.quick-view');
    const quickViewModal = document.getElementById('quickViewModal');
    
    if (quickViewButtons.length && quickViewModal) {
        quickViewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                showQuickView(productId);
            });
        });
        
        // Close modal
        const closeModal = quickViewModal.querySelector('.close-modal');
        closeModal.addEventListener('click', function() {
            quickViewModal.style.display = 'none';
        });
        
        window.addEventListener('click', function(event) {
            if (event.target === quickViewModal) {
                quickViewModal.style.display = 'none';
            }
        });
    }
    
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const variationId = this.getAttribute('data-variation-id') || productId;
            
            addToCart(productId, variationId);
        });
    });
    
    // Wishlist buttons
    const wishlistButtons = document.querySelectorAll('.add-wishlist');
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            addToWishlist(productId);
        });
    });
    
    // Price range slider (if implemented)
    const priceRangeInputs = document.querySelectorAll('.price-range input');
    priceRangeInputs.forEach(input => {
        input.addEventListener('change', function() {
            const minPrice = document.querySelector('input[name="min_price"]').value;
            const maxPrice = document.querySelector('input[name="max_price"]').value;
            
            if (minPrice && maxPrice && parseFloat(minPrice) > parseFloat(maxPrice)) {
                alert('Minimum price cannot be greater than maximum price.');
                this.value = '';
            }
        });
    });
});

// Quick view function
function showQuickView(productId) {
    fetch(`ajax/get_product.php?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.product;
                const modal = document.getElementById('quickViewModal');
                
                modal.querySelector('.product-name').textContent = product.name;
                modal.querySelector('.product-price').textContent = `$${product.price}`;
                if (product.sale_price) {
                    modal.querySelector('.product-sale-price').textContent = `$${product.sale_price}`;
                    modal.querySelector('.product-sale-price').style.display = 'inline';
                    modal.querySelector('.product-price').style.textDecoration = 'line-through';
                }
                modal.querySelector('.product-desc').textContent = product.short_desc;
                modal.querySelector('.product-image').src = product.image;
                modal.querySelector('.view-details-btn').href = `product.php?id=${productId}`;
                modal.querySelector('.add-to-cart-btn').setAttribute('data-product-id', productId);
                
                modal.style.display = 'block';
            } else {
                alert('Error loading product details.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading product details.');
        });
}

// Add to cart function
function addToCart(productId, variationId, quantity = 1) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('variation_id', variationId);
    formData.append('quantity', quantity);
    
    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
            showToast('Product added to cart!');
        } else {
            alert(data.error || 'Error adding to cart.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding to cart.');
    });
}

// Add to wishlist function
function addToWishlist(productId) {
    fetch('ajax/add_to_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Product added to wishlist!');
        } else {
            if (data.redirect) {
                window.location.href = 'login.php';
            } else {
                alert(data.error || 'Error adding to wishlist.');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding to wishlist.');
    });
}

// Update cart count
function updateCartCount() {
    fetch('ajax/get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.count;
                    cartCount.style.display = data.count > 0 ? 'flex' : 'none';
                }
            }
        });
}

// Toast notification
function showToast(message, type = 'success') {
    // Create toast element if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
        `;
        document.body.appendChild(toastContainer);
    }
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        padding: 12px 20px;
        margin-bottom: 10px;
        background: ${type === 'success' ? 'var(--success-color)' : 'var(--danger-color)'};
        color: white;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    `;
    
    toastContainer.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            toastContainer.removeChild(toast);
        }, 300);
    }, 3000);
}