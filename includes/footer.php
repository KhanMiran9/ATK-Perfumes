<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <!-- First Column: Brand + Contact Info + Social -->
            <div class="footer-col footer-brand-section">
                <div class="footer-brand">
                    <a href="#" class="footer-logo">ATK<span>Perfumes</span></a>
                    <p class="footer-description">Crafting timeless fragrances with the world's finest ingredients.</p>
                </div>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>123 Luxury Avenue, Mumbai, India 400001</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>+91 98765 43210</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>info@atkperfumes.com</span>
                    </div>
                </div>
<<<<<<< HEAD
                
                <div class="footer-social-section">
                    <p class="social-title">Follow Us</p>
                    <div class="footer-social">
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-pinterest"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
=======
                <div class="footer-section">
                    <h4>Customer Service</h4>
                    <ul>
                        <li><a href="#">Shipping Policy</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Us</h4>
                    <p>Email: info@luxeperfume.com</p>
                    <p>Phone: +1 (555) 123-4567</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook">FB</a>
                        <a href="#" aria-label="Instagram">IG</a>
                        <a href="#" aria-label="Twitter">TW</a>
>>>>>>> 29e05669890ecc1d7a34c7417d2c8263bd0890dc
                    </div>
                </div>
            </div>
            
            <!-- Second Column: Collections -->
            <div class="footer-col">
                <h3 class="footer-heading">Collections</h3>
                <ul class="footer-links">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <li class="footer-link"><a href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="footer-link"><a href="#">Floral</a></li>
                        <li class="footer-link"><a href="#">Luxury</a></li>
                        <li class="footer-link"><a href="#">Men</a></li>
                        <li class="footer-link"><a href="#">Niche</a></li>
                        <li class="footer-link"><a href="#">Oriental</a></li>
                        <li class="footer-link"><a href="#">Unisex</a></li>
                        <li class="footer-link"><a href="#">Women</a></li>
                        <li class="footer-link"><a href="#">Woody</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- Third Column: Company -->
            <div class="footer-col">
                <h3 class="footer-heading">Company</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="#">Our Story</a></li>
                    <li class="footer-link"><a href="#">Sustainability</a></li>
                    <li class="footer-link"><a href="#">Press</a></li>
                    <li class="footer-link"><a href="#">Careers</a></li>
                </ul>
            </div>
            
            <!-- Fourth Column: Support -->
            <div class="footer-col">
                <h3 class="footer-heading">Support</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="#">Contact Us</a></li>
                    <li class="footer-link"><a href="#">Shipping & Returns</a></li>
                    <li class="footer-link"><a href="#">FAQ</a></li>
                    <li class="footer-link"><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
            
            <!-- Fifth Column: Recent Products -->
            <!-- <div class="footer-col">
                <h3 class="footer-heading">Recent Products</h3>
                <div class="recent-products">
                    <div class="loading-products">Loading products...</div>
                </div>
            </div> -->
        </div>
        
       <div class="footer-bottom">
    <p>&copy; 2023 ATK Perfumes. All rights reserved. Developed by <a href="https://digirich.in" class="digirich-link">Digirich</a></p>
</div>
    </div>
</footer>

<style>
    /* Footer */
    .footer {
        background-color: var(--black);
        color: var(--white);
        padding: 4rem 0 2rem;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr ;
        gap: 2rem;
        margin-bottom: 3rem;
        align-items: start;
    }

    /* First Column Styles */
    .footer-brand-section {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .footer-brand {
        text-align: left;
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
        line-height: 1.6;
        margin: 0;
        font-size: 0.95rem;
    }

    /* Contact Info Styles */
    .contact-info {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        color: var(--muted);
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .contact-item i {
        color: var(--gold);
        margin-top: 0.1rem;
        min-width: 16px;
        font-size: 0.9rem;
    }

    /* Social Section */
    .footer-social-section {
        margin-top: 0.5rem;
    }

    .social-title {
        color: var(--muted);
        margin-bottom: 0.8rem;
        font-size: 0.9rem;
    }

    .footer-social {
        display: flex;
        gap: 0.8rem;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.1);
        color: var(--white);
        transition: var(--transition);
        text-decoration: none;
        font-size: 0.9rem;
    }

    .social-link:hover {
        background: var(--gradient-gold);
        color: var(--black);
        transform: translateY(-2px);
    }

    /* Common Column Styles */
    .footer-col {
        display: flex;
        flex-direction: column;
    }

    .footer-heading {
        font-size: 1.1rem;
        margin-bottom: 1.2rem;
        position: relative;
        padding-bottom: 0.5rem;
        font-family: 'Cinzel', serif;
        font-weight: 600;
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
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .footer-link a {
        color: var(--muted);
        text-decoration: none;
        transition: var(--transition);
        font-size: 0.9rem;
        display: block;
    }

    .footer-link a:hover {
        color: var(--gold);
        padding-left: 5px;
    }

    /* Recent Products Styles */
    .recent-products {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .recent-product {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: var(--muted);
        transition: var(--transition);
        padding: 0.5rem;
        border-radius: 4px;
        background: rgba(255, 255, 255, 0.02);
    }

    .recent-product:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--white);
        transform: translateX(5px);
    }

    .product-image-container {
        position: relative;
        width: 50px;
        height: 50px;
        border-radius: 4px;
        overflow: hidden;
        flex-shrink: 0;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 0.3s ease;
    }

    .product-image.hover {
        position: absolute;
        top: 0;
        left: 0;
        opacity: 0;
    }

    .recent-product:hover .product-image.main {
        opacity: 0;
    }

    .recent-product:hover .product-image.hover {
        opacity: 1;
    }

    .product-info {
        flex: 1;
        min-width: 0;
    }

    .product-name {
        font-weight: 500;
        margin-bottom: 0.2rem;
        font-size: 0.85rem;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-price {
        color: var(--gold);
        font-weight: 600;
        font-size: 0.8rem;
    }

    .loading-products {
        color: var(--muted);
        font-style: italic;
        text-align: center;
        padding: 1rem 0;
        font-size: 0.9rem;
    }

    /* Footer Bottom */
   .footer-bottom {
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    color: var(--muted);
    font-size: 0.9rem;
}

.digirich-link {
    background: var(--gradient-gold);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    text-decoration: none;
    font-weight: 600;
    position: relative;
    padding: 0.1rem 0.2rem;
    border-radius: 4px;
    transition: all 0.3s ease;
}

/* .digirich-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--gradient-gold);
    border-radius: 4px;
    z-index: -1;
    opacity: 0.1;
    transition: opacity 0.3s ease;
} */

/* .digirich-link::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.4),
        transparent
    );
    transition: left 0.5s ease;
} */

.digirich-link:hover {
    transform: translateY(-1px);
    text-shadow: 0 2px 10px rgba(255, 215, 0, 0.3);
}

.digirich-link:hover::before {
    opacity: 0.2;
}

.digirich-link:hover::after {
    left: 100%;
}

/* Alternative shiny effect with animation */
@keyframes shiny {
    0% {
        background-position: -200% center;
    }
    100% {
        background-position: 200% center;
    }
}

.digirich-link.shiny {
    background: linear-gradient(
        90deg,
        #ffd700,
        #ffed4e,
        #ffd700,
        #ffed4e,
        #ffd700
    );
    background-size: 200% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: shiny 3s linear infinite;
    text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
}

    /* Responsive Design */
    @media (max-width: 1200px) {
        .footer-grid {
            grid-template-columns: 1.8fr 1fr 1fr 1fr ;
            gap: 1.5rem;
        }
    }

    @media (max-width: 992px) {
        .footer-grid {
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        .footer-brand-section {
            grid-column: 1 / -1;
            text-align: center;
            align-items: center;
        }
        
        .footer-brand {
            text-align: center;
        }
        
        .contact-item {
            justify-content: center;
            text-align: center;
        }
        
        .footer-social {
            justify-content: center;
        }
        
        .footer-col:nth-child(5) {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 768px) {
        .footer {
            padding: 3rem 0 1.5rem;
        }
        
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
            text-align: center;
        }
        
        .footer-col {
            text-align: center;
        }
        
        .footer-heading:after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        .recent-product {
            justify-content: center;
            max-width: 300px;
            margin: 0 auto;
        }
    }

    @media (max-width: 480px) {
        .footer {
            padding: 2rem 0 1rem;
        }
        
        .footer-logo {
            font-size: 1.75rem;
        }
        
        .footer-grid {
            gap: 1.5rem;
        }
        
        .recent-product {
            flex-direction: column;
            text-align: center;
        }
        
        .contact-item {
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
        }
    }
</style>

<script>
    // Function to load recent products
    function loadRecentProducts() {
        const recentProductsContainer = document.querySelector('.recent-products');
        
        // Show loading
        recentProductsContainer.innerHTML = '<div class="loading-products">Loading products...</div>';
        
        // Fetch recent products via AJAX
        fetch('api/get_recent_products.php?limit=4')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(products => {
                recentProductsContainer.innerHTML = '';
                
                if (products.length === 0) {
                    recentProductsContainer.innerHTML = '<div class="loading-products">No products found</div>';
                    return;
                }
                
                products.forEach(product => {
                    const productElement = document.createElement('a');
                    productElement.href = product.url;
                    productElement.className = 'recent-product';
                    
                    // Use placeholder if no image
                    const mainImage = product.image1 || 'assets/images/placeholder.jpg';
                    const hoverImage = product.image2 || mainImage;
                    
                    productElement.innerHTML = `
                        <div class="product-image-container">
                            <img src="${mainImage}" alt="${product.name}" class="product-image main">
                            <img src="${hoverImage}" alt="${product.name}" class="product-image hover">
                        </div>
                        <div class="product-info">
                            <div class="product-name">${product.name}</div>
                            <div class="product-price">₹${product.price}</div>
                        </div>
                    `;
                    
                    recentProductsContainer.appendChild(productElement);
                });
            })
            .catch(error => {
                console.error('Error loading recent products:', error);
                // Fallback to sample data
                const fallbackProducts = [
                    {
                        id: 1,
                        name: "Mystic Oud",
                        price: "2,499.00",
                        image1: "assets/images/mystic-oud-1.jpg",
                        image2: "assets/images/mystic-oud-2.jpg",
                        url: "product.php?id=1"
                    },
                    {
                        id: 2,
                        name: "Noir Essence",
                        price: "1,899.00",
                        image1: "assets/images/noir-essence-1.jpg",
                        image2: "assets/images/noir-essence-2.jpg",
                        url: "product.php?id=2"
                    },
                    {
                        id: 3,
                        name: "Rose Elixir",
                        price: "2,199.00",
                        image1: "assets/images/rose-elixir-1.jpg",
                        image2: "assets/images/rose-elixir-2.jpg",
                        url: "product.php?id=3"
                    }
                ];
                
                recentProductsContainer.innerHTML = '';
                fallbackProducts.forEach(product => {
                    const productElement = document.createElement('a');
                    productElement.href = product.url;
                    productElement.className = 'recent-product';
                    
                    productElement.innerHTML = `
                        <div class="product-image-container">
                            <img src="${product.image1}" alt="${product.name}" class="product-image main">
                            <img src="${product.image2}" alt="${product.name}" class="product-image hover">
                        </div>
                        <div class="product-info">
                            <div class="product-name">${product.name}</div>
                            <div class="product-price">₹${product.price}</div>
                        </div>
                    `;
                    
                    recentProductsContainer.appendChild(productElement);
                });
            });
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        loadRecentProducts();
        
        // Add error handling for images
        document.addEventListener('error', function(e) {
            if (e.target.classList.contains('product-image')) {
                e.target.src = 'assets/images/1_43.webp';
            }
        }, true);
    });
</script>