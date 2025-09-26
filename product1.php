<?php
/**
 * product1.php - Fixed version
 * - Uses existing includes/config.php and db.php
 * - Fixes button alignment issues
 * - Fixes variation selection on desktop
 * - Prevents text/image selection during swipe
 * - Makes variation buttons more luxurious
 */

// Use existing config and database connection
require_once 'includes/config.php';
require_once 'includes/db.php';

$database = new Database();
$pdo = $database->getConnection();

/* ------------------------- Fetch active products ------------------------- */
try {
    // 1) Products
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.id ASC");
    $stmt->execute();
    $products = $stmt->fetchAll();

    // Guard if no products
    if (!$products) $products = [];

    // Collect product IDs
    $productIds = array_column($products, 'id');
    
    // Prepare maps
    $mediaMap = [];
    $tagsMap = [];
    $variationsMap = [];
    $variationImagesMap = [];

    if (!empty($productIds)) {
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        
        // 2) product_media
        $sql = "SELECT product_id, file_path, alt_text FROM product_media WHERE product_id IN ($placeholders) ORDER BY product_id, sort_order ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($productIds);
        $rows = $stmt->fetchAll();
        foreach ($rows as $r) {
            $mediaMap[$r['product_id']][] = $r['file_path'];
        }

        // 3) product_tags
        $sql = "SELECT product_id, tag FROM product_tags WHERE product_id IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($productIds);
        $rows = $stmt->fetchAll();
        foreach ($rows as $r) {
            $tagsMap[$r['product_id']][] = $r['tag'];
        }

        // 4) product_variations
        $sql = "SELECT * FROM product_variations WHERE product_id IN ($placeholders) ORDER BY product_id, is_default DESC, id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($productIds);
        $rows = $stmt->fetchAll();
        
        $variationIds = [];
        foreach ($rows as $r) {
            $variationsMap[$r['product_id']][] = $r;
            $variationIds[] = $r['id'];
        }

        // 5) variation_images
        if (!empty($variationIds)) {
            $placeholdersVar = implode(',', array_fill(0, count($variationIds), '?'));
            $sql = "SELECT variation_id, file_path FROM variation_images WHERE variation_id IN ($placeholdersVar)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($variationIds);
            $rows = $stmt->fetchAll();
            foreach ($rows as $r) {
                $variationImagesMap[$r['variation_id']][] = $r['file_path'];
            }
        }
    }

    // 6) Wishlist items for current user
    $wishlistSet = [];
    if (isset($_SESSION['user_id'])) {
        $sql = "SELECT product_id FROM wishlist WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id']]);
        $rows = $stmt->fetchAll();
        foreach ($rows as $r) $wishlistSet[] = (int)$r['product_id'];
    }

    // 7) Build enriched products array
    $enriched = [];
    foreach ($products as $p) {
        $pid = (int)$p['id'];
        
        // Media with fallback
        $media = isset($mediaMap[$pid]) ? $mediaMap[$pid] : [];
        if (empty($media)) {
            $media[] = 'assets/images/placeholder.png';
        }

        // Variations
        $vars = isset($variationsMap[$pid]) ? $variationsMap[$pid] : [];
        foreach ($vars as &$v) {
            $v['sku_attributes'] = json_decode($v['sku_attributes_json'] ?? '{}', true) ?: [];
            $v['variation_images'] = $variationImagesMap[$v['id']] ?? [];
        }
        unset($v);

        // Default variation
        $defaultVar = null;
        if (!empty($vars)) {
            foreach ($vars as $vv) {
                if (!empty($vv['is_default'])) {
                    $defaultVar = $vv;
                    break;
                }
            }
            if ($defaultVar === null) $defaultVar = $vars[0];
        } else {
            $defaultVar = [
                'id' => null,
                'price' => 0,
                'sale_price' => null,
                'sku' => '',
                'sku_attributes' => [],
                'stock' => 0,
                'variation_images' => []
            ];
        }

        $enriched[] = [
            'id' => $pid,
            'sku' => $p['sku'],
            'name' => $p['name'],
            'slug' => $p['slug'],
            'short_desc' => $p['short_desc'],
            'long_desc' => $p['long_desc'],
            'category_name' => $p['category_name'],
            'brand' => $p['brand'] ?: 'Brand',
            'media' => array_values($media),
            'tags' => array_values($tagsMap[$pid] ?? []),
            'variations' => $vars,
            'default_variation' => $defaultVar,
            'is_in_wishlist' => in_array($pid, $wishlistSet, true),
        ];
    }

} catch (Exception $e) {
    http_response_code(500);
    echo "Error fetching products: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<style>
/* ---------- Product Slider Styles (Conflict-Free) ---------- */
.product-slider-section {
    padding: 44px 0;
    background-image: url('assets/images/Maroon_BG.webp');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.product-slider-container {
    width: 95%;
    margin: 0 auto;
    padding: 18px 0;
}

.product-slider-header {
    text-align: center;
    margin-bottom: 28px;
}

.product-slider-title {
    font-family: 'Cinzel', serif;
    font-size: clamp(22px, 3vw, 32px);
    margin: 0;
    color: white;
    letter-spacing: 0.6px;
}

.product-slider-sub {
    color: var(--muted);
    margin-top: 8px;
    font-size: 14px;
}

/* Slider container with improved drag handling */
.products-slider-container {
    position: relative;
    width: 100%;
    overflow: hidden;
}

.products-slider {
    display: flex;
    gap: 1rem;
    padding-bottom: 8px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scroll-snap-type: x mandatory;
    scroll-behavior: smooth;
    padding-left: 6px;
    padding-right: 6px;
    scrollbar-width: none;
    -ms-overflow-style: none;
    cursor: grab;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.products-slider.dragging {
    cursor: grabbing;
    scroll-snap-type: none;
}

.products-slider::-webkit-scrollbar {
    display: none;
}

/* Product card with consistent height */
.product-slider-card {
    flex: 0 0 calc(25% - (var(--gap) * .75));
    background: var(--white);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--shadow);
    scroll-snap-align: start;
    display: flex;
    flex-direction: column;
    min-width: 260px;
    transition: transform .25s ease, box-shadow .25s ease;
    height: auto;
    min-height: 580px; /* Consistent minimum height */
}

.product-slider-card:hover {
   
    box-shadow: 0 18px 40px rgba(0,0,0,0.16);
}

/* Image area */
.product-slider-image-wrap {
    border-radius: 10px;
    position: relative;
    height: 320px; /* Reduced height for better balance */
    overflow: hidden;
    background: linear-gradient(180deg, rgba(0,0,0,0.02), rgba(0,0,0,0.02));
    user-select: none;
    -webkit-user-select: none;
}

.product-slider-image, .product-slider-image-2 {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .5s ease, opacity .35s ease;
    backface-visibility: hidden;
    pointer-events: none;
    user-select: none;
}

.product-slider-image-2 {
    position: absolute;
    inset: 0;
    opacity: 0;
    transform: scale(1.03);
}

.product-slider-image-wrap:hover .product-slider-image {
    transform: scale(1.04) translateY(-6px);
}

.product-slider-image-wrap:hover .product-slider-image-2 {
    opacity: 1;
    transform: none;
}

/* Brand badge */
.product-slider-brand {
    position: absolute;
    left: 0px;
    top: 0px;
    padding: 10px 16px;
    font-family: 'Cinzel', serif;
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--black);
    background: var(--gradient-gold);
    clip-path: polygon(0 0, 100% 0, 82% 100%, 0% 100%);
    box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    z-index: 2;
}

/* Wishlist button */
.product-slider-wishlist {
    position: absolute;
    right: 14px;
    top: 12px;
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: rgba(255,255,255,0.96);
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    z-index: 2;
    color: #bdbdbd;
    transition: transform .18s ease, color .18s ease;
    user-select: none;
}

.product-slider-wishlist.active {
    color: var(--gold);
    transform: scale(1.05);
}

/* Info area with consistent layout */
.product-slider-info {
    padding: 14px 16px 18px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-grow: 1;
    justify-content: space-between;
    min-height: 240px; /* Ensure consistent info area height */
}

.product-slider-category {
    font-size: 0.78rem;
    color: var(--muted);
    text-transform: uppercase;
    line-height: 1.2;
}

.product-slider-name {
    font-family: 'Cinzel', serif;
    font-size: 1.02rem;
    margin: 0;
    color: var(--black);
    line-height: 1.3;
  
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-slider-tag-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
    margin-top: auto;
}

.product-slider-tag {
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 0.78rem;
    background: rgba(0,0,0,0.04);
    color: var(--muted);
    font-weight: 700;
}

/* Price & variation */
.product-slider-price-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-top: 6px;
}

.product-slider-price {
    font-family: 'Cinzel', serif;
    font-size: 1.15rem;
    color: var(--gold);
    font-weight: 700;
}

.product-slider-original {
    font-size: 0.92rem;
    color: var(--muted);
    text-decoration: line-through;
}

/* Luxury variation buttons */
.product-slider-variations {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 12px;
}

.product-slider-variation {
    padding: 10px 16px;
    border-radius: 8px;
    border: 2px solid #e8e8e8;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    background: linear-gradient(135deg, #f8f8f8 0%, #ffffff 100%);
    color: #555;
    position: relative;
    overflow: hidden;
    min-width: 60px;
    text-align: center;
    user-select: none;
}

.product-slider-variation::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.1), transparent);
    transition: left 0.5s ease;
}

.product-slider-variation:hover::before {
    left: 100%;
}

.product-slider-variation:hover {
    border-color: var(--gold);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
}

.product-slider-variation.active {
    background: linear-gradient(135deg, var(--gold), #e0b057);
    color: var(--black);
    border-color: var(--gold);
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
    transform: translateY(-2px);
}

/* Actions area - always at bottom */
.product-slider-actions {
    display: flex;
    gap: 10px;
    margin-top: auto;
    /* padding-top: 12px; */
}

.product-slider-add-btn {
    width: 100%;
    padding: 14px 16px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-weight: 800;
    background: var(--gradient-gold);
    color: var(--black);
    display: flex;
    gap: 10px;
    align-items: center;
    justify-content: center;
    transition: transform .12s ease, box-shadow .3s ease;
    font-family: 'Cinzel', serif;
    letter-spacing: 0.5px;
    user-select: none;
}

.product-slider-add-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
}

.product-slider-add-btn:active {
    transform: translateY(0);
}

/* Sale badge */
.product-slider-sale {
    font-weight: 800;
    color: white;
    background: #2fa572;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 0.85rem;
}

/* Loading */
.product-slider-loading {
    text-align: center;
    padding: 40px 0;
    color: var(--muted);
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .product-slider-card {
        flex: 0 0 calc(50% - 16px);
    }
    .product-slider-image-wrap {
        height: 300px;
    }
}

@media (max-width: 768px) {
    .product-slider-card {
        flex: 0 0 calc(90% - 12px);
        min-height: 540px;
    }
    .product-slider-image-wrap {
        height: 400px;
    }
    .product-slider-container {
        width: 90%;
    }
    .product-slider-variation {
        padding: 8px 12px;
        font-size: 0.8rem;
    }
}

@media (max-width: 420px) {
    .product-slider-image-wrap {
        height: 300px;
    }
    .product-slider-brand {
        padding: 8px 12px;
        font-size: 0.85rem;
    }
    .product-slider-name {
        font-size: 1rem;
    }
    .product-slider-card {
        min-height: 520px;
    }
}
</style>

<section class="product-slider-section">
    <div class="product-slider-container">
        <div class="product-slider-header">
            <h2 class="product-slider-title">Featured Products</h2>
            <p class="product-slider-sub">Big visuals • Simple actions • Swipe to browse</p>
        </div>
        
        <div class="products-slider-container">
            <div id="productsSlider" class="products-slider" aria-live="polite">
                <!-- Cards rendered by JS -->
            </div>
        </div>
        
        <div id="loading" class="product-slider-loading" style="display:none;">
            <i class="fas fa-spinner fa-spin" style="color:var(--gold); margin-right:8px"></i> Loading...
        </div>
    </div>
</section>

<!-- Embedded product data -->
<script>
window.__PRODUCTS__ = <?= json_encode($enriched, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
window.__CURRENT_USER__ = <?= isset($_SESSION['user_id']) ? json_encode((int)$_SESSION['user_id']) : 'null'; ?>;
</script>

<script>
(function(){
    const products = window.__PRODUCTS__ || [];
    const slider = document.getElementById('productsSlider');
    const loadingEl = document.getElementById('loading');
    const API_ADD_TO_CART = 'api/add_to_cart.php';
    const API_TOGGLE_WISHLIST = 'api/toggle_wishlist.php';
    const placeholder = 'assets/images/placeholder.png';

    if (!products || products.length === 0) {
        slider.innerHTML = '<div style="padding:30px;color:var(--muted);width:100%;text-align:center"><i class="fas fa-box-open" style="font-size:34px;color:var(--gold)"></i><div>No products found.</div></div>';
        return;
    }

    /* Utility: escape for attribute insertion */
    function esc(s) {
        return String(s === null || s === undefined ? '' : s)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;');
    }

    /* Create product card DOM */
    function createCard(p, index) {
        const img1 = p.media && p.media.length ? p.media[0] : placeholder;
        const img2 = p.media && p.media.length > 1 ? p.media[1] : 
                    (p.variations && p.variations[0] && p.variations[0].variation_images && p.variations[0].variation_images[0] ? 
                     p.variations[0].variation_images[0] : img1);

        const defaultVar = p.default_variation || {};
        const price = parseFloat(defaultVar.price || 0);
        const sale = (defaultVar.sale_price !== null && defaultVar.sale_price !== undefined) ? parseFloat(defaultVar.sale_price) : null;
        const hasSale = sale !== null && !isNaN(sale) && sale < price;
        const pctOff = hasSale ? Math.round((1 - (sale/price)) * 100) : 0;

        // Build variation options
        let varHtml = '';
        if (p.variations && p.variations.length > 0) {
            p.variations.forEach((v, i) => {
                let label = '';
                try {
                    if (v.sku_attributes && typeof v.sku_attributes === 'object') {
                        label = v.sku_attributes.volume || v.sku_attributes.size || v.sku || 'Option ' + (i+1);
                    }
                } catch(e) {
                    label = v.sku || 'Option ' + (i+1);
                }
                if (!label) label = 'Option ' + (i+1);
                
                varHtml += `<div class="product-slider-variation ${i===0 ? 'active' : ''}" 
                              data-variation-id="${esc(v.id)}" 
                              data-price="${esc(v.price)}" 
                              data-sale="${esc(v.sale_price)}">
                            ${esc(label)}
                          </div>`;
            });
        }

        // Tags
        let tagsHtml = '';
        if (p.tags && p.tags.length) {
            p.tags.forEach(t => tagsHtml += `<div class="product-slider-tag">${esc(t)}</div>`);
        }

        // Build card markup
        const card = document.createElement('div');
        card.className = 'product-slider-card';
        card.dataset.productId = p.id;
        card.innerHTML = `
            <div class="product-slider-image-wrap">
                <img class="product-slider-image" src="${esc(img1)}" alt="${esc(p.name)}" loading="lazy">
                <img class="product-slider-image-2" src="${esc(img2)}" alt="${esc(p.name)}" loading="lazy">
                <div class="product-slider-brand">${esc(p.brand)}</div>
                <button class="product-slider-wishlist ${p.is_in_wishlist ? 'active' : ''}" 
                        data-product-id="${esc(p.id)}" 
                        aria-label="Wishlist">
                    <i class="${p.is_in_wishlist ? 'fas' : 'far'} fa-heart"></i>
                </button>
            </div>
            <div class="product-slider-info">
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:8px;">
                        <div style="flex:1;">
                            <div class="product-slider-category">${esc(p.category_name)}</div>
                            <div class="product-slider-name">${esc(p.name)}</div>
                        </div>
                        ${hasSale ? `<div class="product-slider-sale">-${pctOff}%</div>` : ''}
                    </div>
                    
                    <div class="product-slider-price-row">
                        <div>
                            <div class="product-slider-price">${hasSale ? '₹' + sale.toFixed(2) : '₹' + (price || 0).toFixed(2)}</div>
                            ${hasSale ? `<div class="product-slider-original">₹${price.toFixed(2)}</div>` : ''}
                        </div>
                    </div>
                    
                    ${varHtml ? `<div class="product-slider-variations">${varHtml}</div>` : ''}
                    
                   
                </div>
                
                <div class="product-slider-actions">
                    <button class="product-slider-add-btn" 
                            data-product-id="${esc(p.id)}" 
                            data-variation-id="${esc(p.default_variation ? p.default_variation.id : '')}">
                        <i class="fas fa-cart-plus"></i> ADD TO CART
                    </button>
                </div>
            </div>
        `;

        return card;
    }

    /* Render all cards */
    function renderAll() {
        slider.innerHTML = '';
        const frag = document.createDocumentFragment();
        products.forEach((p,i) => {
            frag.appendChild(createCard(p,i));
        });
        slider.appendChild(frag);
        attachInteractions();
        enableDragScroll(slider);
    }

    /* Improved drag scroll with better text selection prevention */
    function enableDragScroll(el) {
        let isDown = false;
        let startX;
        let scrollLeft;

        el.addEventListener('mousedown', (e) => {
            if (e.target.closest('.product-slider-variation') || 
                e.target.closest('.product-slider-wishlist') ||
                e.target.closest('.product-slider-add-btn')) {
                return;
            }
            
            isDown = true;
            el.classList.add('dragging');
            startX = e.pageX - el.offsetLeft;
            scrollLeft = el.scrollLeft;
            el.style.cursor = 'grabbing';
        });

        el.addEventListener('mouseleave', () => {
            isDown = false;
            el.classList.remove('dragging');
            el.style.cursor = '';
        });

        el.addEventListener('mouseup', () => {
            isDown = false;
            el.classList.remove('dragging');
            el.style.cursor = '';
        });

        el.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - el.offsetLeft;
            const walk = (x - startX) * 1.5;
            el.scrollLeft = scrollLeft - walk;
        });

        // Touch events for mobile
        let touchStartX = 0;
        let scrollLeftStart = 0;

        el.addEventListener('touchstart', (e) => {
            if (e.target.closest('.product-slider-variation') || 
                e.target.closest('.product-slider-wishlist') ||
                e.target.closest('.product-slider-add-btn')) {
                return;
            }
            
            touchStartX = e.touches[0].pageX;
            scrollLeftStart = el.scrollLeft;
            el.classList.add('dragging');
        }, { passive: true });

        el.addEventListener('touchmove', (e) => {
            if (!touchStartX) return;
            const touchX = e.touches[0].pageX;
            const walk = (touchX - touchStartX) * 1.5;
            el.scrollLeft = scrollLeftStart - walk;
        }, { passive: true });

        el.addEventListener('touchend', () => {
            touchStartX = 0;
            scrollLeftStart = 0;
            el.classList.remove('dragging');
        }, { passive: true });
    }

    /* Attach interactions */
    function attachInteractions() {
        // Variation selection
        slider.addEventListener('click', function(e) {
            const variation = e.target.closest('.product-slider-variation');
            if (!variation) return;

            e.preventDefault();
            e.stopPropagation();

            const card = variation.closest('.product-slider-card');
            const allVariations = card.querySelectorAll('.product-slider-variation');
            
            allVariations.forEach(v => v.classList.remove('active'));
            variation.classList.add('active');

            // Update price
            const priceEl = card.querySelector('.product-slider-price');
            const origEl = card.querySelector('.product-slider-original');
            const price = parseFloat(variation.getAttribute('data-price') || 0);
            const sale = variation.getAttribute('data-sale') ? parseFloat(variation.getAttribute('data-sale')) : null;

            if (sale !== null && !isNaN(sale) && sale < price) {
                priceEl.textContent = '₹' + sale.toFixed(2);
                if (origEl) {
                    origEl.textContent = '₹' + price.toFixed(2);
                } else {
                    const newOrig = document.createElement('div');
                    newOrig.className = 'product-slider-original';
                    newOrig.textContent = '₹' + price.toFixed(2);
                    priceEl.insertAdjacentElement('afterend', newOrig);
                }
            } else {
                priceEl.textContent = '₹' + price.toFixed(2);
                if (origEl) origEl.remove();
            }

            // Update add button variation
            const btn = card.querySelector('.product-slider-add-btn');
            if (btn) btn.dataset.variationId = variation.dataset.variationId || '';

            // Update images if available
            const productId = card.dataset.productId;
            const prod = products.find(pp => String(pp.id) === String(productId));
            if (prod) {
                const vid = parseInt(variation.dataset.variationId);
                let newImg = null;
                
                if (!isNaN(vid)) {
                    const variationData = (prod.variations || []).find(v => Number(v.id) === Number(vid));
                    if (variationData && variationData.variation_images && variationData.variation_images.length) {
                        newImg = variationData.variation_images[0];
                    }
                }

                if (!newImg) newImg = (prod.media && prod.media[1]) ? prod.media[1] : prod.media[0];
                
                const imgEl2 = card.querySelector('.product-slider-image-2');
                if (imgEl2 && newImg) {
                    imgEl2.src = newImg;
                }
            }
        });

        // Wishlist
        slider.addEventListener('click', async function(e) {
            const wishlistBtn = e.target.closest('.product-slider-wishlist');
            if (!wishlistBtn) return;

            e.preventDefault();
            e.stopPropagation();

            const pid = wishlistBtn.dataset.productId;
            const isNowActive = !wishlistBtn.classList.contains('active');
            const icon = wishlistBtn.querySelector('i');

            // Optimistic UI update
            if (isNowActive) {
                wishlistBtn.classList.add('active');
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                wishlistBtn.classList.remove('active');
                icon.classList.remove('fas');
                icon.classList.add('far');
            }

            try {
                const res = await fetch(API_TOGGLE_WISHLIST, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        product_id: pid, 
                        action: isNowActive ? 'add' : 'remove'
                    })
                });
                
                const j = await res.json();
                if (!res.ok || j.error) throw new Error(j.error || 'Wishlist failed');
                
            } catch (err) {
                console.error(err);
                // Revert UI on error
                if (isNowActive) {
                    wishlistBtn.classList.remove('active');
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                } else {
                    wishlistBtn.classList.add('active');
                    icon.classList.add('fas');
                    icon.classList.remove('far');
                }
                alert('Could not update wishlist. Please login or try again.');
            }
        });

        // Add to cart
        slider.addEventListener('click', async function(e) {
            const addBtn = e.target.closest('.product-slider-add-btn');
            if (!addBtn) return;

            e.preventDefault();
            e.stopPropagation();

            const pid = addBtn.dataset.productId;
            const vid = addBtn.dataset.variationId || null;
            const original = addBtn.innerHTML;

            try {
                addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                addBtn.disabled = true;

                const res = await fetch(API_ADD_TO_CART, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        product_id: pid,
                        variation_id: vid,
                        quantity: 1
                    })
                });

                const j = await res.json();
                if (!res.ok || j.error) throw new Error(j.error || 'Add to cart failed');

                addBtn.innerHTML = '<i class="fas fa-check"></i> Added';
                
                setTimeout(() => {
                    addBtn.innerHTML = original;
                    addBtn.disabled = false;
                }, 1400);

            } catch (err) {
                console.error(err);
                addBtn.innerHTML = original;
                addBtn.disabled = false;
                alert('Could not add to cart. Please login or try again.');
            }
        });
    }

    // Prevent text selection during drag
    document.addEventListener('selectstart', function(e) {
        if (e.target.closest('.products-slider')) {
            e.preventDefault();
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (['ArrowLeft','ArrowRight'].includes(e.key) && document.activeElement.closest('.products-slider')) {
            e.preventDefault();
            const cardWidth = slider.querySelector('.product-slider-card') ? 
                slider.querySelector('.product-slider-card').offsetWidth + parseInt(getComputedStyle(slider).gap || 24) : 300;
            slider.scrollBy({
                left: e.key === 'ArrowRight' ? cardWidth : -cardWidth,
                behavior: 'smooth'
            });
        }
    });

    // Initialize
    renderAll();
})();
</script>