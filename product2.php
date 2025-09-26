<?php

require_once 'includes/config.php';
require_once 'includes/db.php';

$database = new Database();
$pdo = $database->getConnection();

/* ------------------------- Fetch active categories ------------------------- */
try {
    $stmt = $pdo->prepare("SELECT id, name, slug, image FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
    $stmt->execute();
    $categories = $stmt->fetchAll();
    
    if (!$categories) $categories = [];
} catch (Exception $e) {
    $categories = [];
}

/* ------------------------- Fetch active products with limits ------------------------- */
$categoryProductsMap = [];

try {
    // Fetch limited products for each category (4 products per category)
    foreach ($categories as $category) {
        $stmt = $pdo->prepare("
            SELECT p.*, c.name AS category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 AND p.category_id = ? 
            ORDER BY p.id ASC 
            LIMIT 4
        ");
        $stmt->execute([$category['id']]);
        $products = $stmt->fetchAll();

        if (!$products) {
            $categoryProductsMap[$category['id']] = [];
            continue;
        }

        $productIds = array_column($products, 'id');
        
        // Prepare maps for each product
        $mediaMap = [];
        $tagsMap = [];
        $variationsMap = [];
        $variationImagesMap = [];

        if (!empty($productIds)) {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            
            // product_media
            $sql = "SELECT product_id, file_path, alt_text FROM product_media WHERE product_id IN ($placeholders) ORDER BY product_id, sort_order ASC";
            $stmtMedia = $pdo->prepare($sql);
            $stmtMedia->execute($productIds);
            $rows = $stmtMedia->fetchAll();
            foreach ($rows as $r) {
                $mediaMap[$r['product_id']][] = $r['file_path'];
            }

            // product_tags
            $sql = "SELECT product_id, tag FROM product_tags WHERE product_id IN ($placeholders)";
            $stmtTags = $pdo->prepare($sql);
            $stmtTags->execute($productIds);
            $rows = $stmtTags->fetchAll();
            foreach ($rows as $r) {
                $tagsMap[$r['product_id']][] = $r['tag'];
            }

            // product_variations
            $sql = "SELECT * FROM product_variations WHERE product_id IN ($placeholders) ORDER BY product_id, is_default DESC, id ASC";
            $stmtVars = $pdo->prepare($sql);
            $stmtVars->execute($productIds);
            $rows = $stmtVars->fetchAll();
            
            $variationIds = [];
            foreach ($rows as $r) {
                $variationsMap[$r['product_id']][] = $r;
                $variationIds[] = $r['id'];
            }

            // variation_images
            if (!empty($variationIds)) {
                $placeholdersVar = implode(',', array_fill(0, count($variationIds), '?'));
                $sql = "SELECT variation_id, file_path FROM variation_images WHERE variation_id IN ($placeholdersVar)";
                $stmtVarImages = $pdo->prepare($sql);
                $stmtVarImages->execute($variationIds);
                $rows = $stmtVarImages->fetchAll();
                foreach ($rows as $r) {
                    $variationImagesMap[$r['variation_id']][] = $r['file_path'];
                }
            }
        }

        // Wishlist items for current user
        $wishlistSet = [];
        if (isset($_SESSION['user_id'])) {
            $sql = "SELECT product_id FROM wishlist WHERE user_id = ?";
            $stmtWishlist = $pdo->prepare($sql);
            $stmtWishlist->execute([$_SESSION['user_id']]);
            $rows = $stmtWishlist->fetchAll();
            foreach ($rows as $r) $wishlistSet[] = (int)$r['product_id'];
        }

        // Build enriched products array for this category
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
                'category_id' => $p['category_id'],
                'brand' => $p['brand'] ?: 'Brand',
                'media' => array_values($media),
                'tags' => array_values($tagsMap[$pid] ?? []),
                'variations' => $vars,
                'default_variation' => $defaultVar,
                'is_in_wishlist' => in_array($pid, $wishlistSet, true),
            ];
        }

        $categoryProductsMap[$category['id']] = $enriched;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo "Error fetching products: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<style>
/* ---------- Category Tabs Styles ---------- */
.category-tabs-section {
    padding: 60px 0;
    background-image: url(assets/images/Vector_1.png);
}

.category-tabs-container {
    width: 95%;
    max-width: 1400px;
    margin: 0 auto;
}

.category-tabs-header {
    text-align: center;
    margin-bottom: 40px;
}

.category-tabs-title {
    font-family: 'Cinzel', serif;
    font-size: clamp(26px, 4vw, 36px);
    color: var(--black);
    margin: 0 0 12px 0;
    letter-spacing: 0.8px;
}

.category-tabs-subtitle {
    color: var(--muted);
    font-size: 16px;
    max-width: 600px;
    margin: 0 auto;
}

.category-tabs-wrapper {
    margin-bottom: 40px;
}

.category-tabs-container-scroll {
    position: relative;
    width: 100%;
}

.category-tabs-scroll {
    display: flex;
    justify-content: center;
    gap: 12px;
    padding: 10px 0;
    overflow-x: auto;
    scroll-behavior: smooth;
    scrollbar-width: none;
    -ms-overflow-style: none;
    scroll-snap-type: x mandatory;
}

.category-tabs-scroll::-webkit-scrollbar {
    display: none;
}

.category-tab {
    padding: 16px 28px;
    border: 2px solid transparent;
    background: white;
    border-radius: 12px;
    cursor: pointer;
    font-family: 'Cinzel', serif;
    font-weight: 600;
    font-size: 15px;
    color: var(--black);
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    flex-shrink: 0;
    scroll-snap-align: start;
    white-space: nowrap;
}

.category-tab::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.15), transparent);
    transition: left 0.6s ease;
}

.category-tab:hover::before {
    left: 100%;
}

.category-tab:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: var(--gold);
}

.category-tab.active {
    background: linear-gradient(135deg, var(--gold), #e0b057);
    color: var(--black);
    border-color: var(--gold);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
    transform: translateY(-2px);
}

.category-tab.active::before {
    display: none;
}

/* Scroll buttons for category tabs */
.category-scroll-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: 2px solid var(--gold);
    color: var(--gold);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    transition: all 0.3s ease;
    z-index: 10;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.category-scroll-btn:hover {
    background: var(--gold);
    color: white;
    transform: translateY(-50%) scale(1.1);
}

.category-scroll-btn.prev {
    left: -20px;
}

.category-scroll-btn.next {
    right: -20px;
}

.category-scroll-btn.hidden {
    display: none;
}

/* Products grid */
.category-products-container {
    min-height: 400px;
}

.category-products-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    padding: 20px 0;
}

.category-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--muted);
    grid-column: 1 / -1;
}

.category-empty-state i {
    font-size: 48px;
    color: var(--gold);
    margin-bottom: 16px;
}

.category-empty-state h3 {
    font-family: 'Cinzel', serif;
    margin: 0 0 8px 0;
    color: var(--black);
}

.category-loading {
    text-align: center;
    padding: 40px 0;
    color: var(--muted);
    grid-column: 1 / -1;
}

.category-loading i {
    color: var(--gold);
    margin-right: 8px;
}

/* View More button */
.category-view-more {
    text-align: center;
    margin-top: 40px;
    grid-column: 1 / -1;
}

.view-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    background: linear-gradient(135deg, var(--gold), #e0b057);
    color: var(--black);
    text-decoration: none;
    border-radius: 10px;
    font-family: 'Cinzel', serif;
    font-weight: 700;
    font-size: 16px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.view-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
    border-color: var(--gold);
}

/* Product card styles */
.product-card {
    background: var(--white);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    transition: transform .25s ease, box-shadow .25s ease;
    height: auto;
    min-height: 500px;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 40px rgba(0,0,0,0.16);
}

.product-image-wrap {
    border-radius: 10px;
    position: relative;
    height: 280px;
    overflow: hidden;
    background: linear-gradient(180deg, rgba(0,0,0,0.02), rgba(0,0,0,0.02));
    user-select: none;
}

.product-image, .product-image-2 {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .5s ease, opacity .35s ease;
    backface-visibility: hidden;
}

.product-image-2 {
    position: absolute;
    inset: 0;
    opacity: 0;
    transform: scale(1.03);
}

.product-image-wrap:hover .product-image {
    transform: scale(1.04) translateY(-6px);
}

.product-image-wrap:hover .product-image-2 {
    opacity: 1;
    transform: none;
}

.product-brand {
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

.product-wishlist {
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
}

.product-wishlist.active {
    color: var(--gold);
    transform: scale(1.05);
}

.product-info {
    padding: 14px 16px 18px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex-grow: 1;
    justify-content: space-between;
    min-height: 220px;
}

.product-category {
    font-size: 0.78rem;
    color: var(--muted);
    text-transform: uppercase;
    line-height: 1.2;
}

.product-name {
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

.product-tag-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
    margin-top: auto;
}

.product-tag {
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 0.78rem;
    background: rgba(0,0,0,0.04);
    color: var(--muted);
    font-weight: 700;
}

.product-price-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-top: 6px;
}

.product-price {
    font-family: 'Cinzel', serif;
    font-size: 1.15rem;
    color: var(--gold);
    font-weight: 700;
}

.product-original {
    font-size: 0.92rem;
    color: var(--muted);
    text-decoration: line-through;
}

.product-variations {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 12px;
}

.product-variation {
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
}

.product-variation::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.1), transparent);
    transition: left 0.5s ease;
}

.product-variation:hover::before {
    left: 100%;
}

.product-variation:hover {
    border-color: var(--gold);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
}

.product-variation.active {
    background: linear-gradient(135deg, var(--gold), #e0b057);
    color: var(--black);
    border-color: var(--gold);
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
    transform: translateY(-2px);
}

.product-actions {
    display: flex;
    gap: 10px;
    margin-top: auto;
}

.product-add-btn {
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
}

.product-add-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
}

.product-sale {
    font-weight: 800;
    color: white;
    background: #2fa572;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 0.85rem;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .category-products-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .category-tabs-section {
        padding: 40px 0;
    }
    
    .category-tab {
        padding: 12px 20px;
        font-size: 14px;
    }
    
    .category-products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    .category-scroll-btn {
        display: none;
    }
    
    .product-card {
        min-height: 480px;
    }
    
    .product-image-wrap {
        height: 240px;
    }
}

@media (max-width: 480px) {
.product-brand{
        padding: 5px 10px;
            font-size: 0.60rem;
}
.product-wishlist{
    position: absolute;
    right: 4px;
    top: 4px;
    width: 30px;
    height: 30px;
}
.product-category{
        font-size: 0.60rem;
}
.product-name{
        font-size: 0.8rem;
}
.product-sale {
   
   
    padding: 1px 5px;
   
    font-size: 0.65rem;
}
.product-price{
    font-size: 0.8rem;
    margin-bottom: 0.1rem;
}
.product-variation{
        padding: 1px 5px;
        font-size: 0.75rem;
}
.product-add-btn{
    padding: 10px 4px;
    gap: 5px;
}
.product-info{
    padding: 10px 12px 12px 12px;
}
    .category-tabs-wrapper {
        margin-bottom: 30px;
    }
    
    .category-products-grid {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    
    .product-card {
        min-height: 430px;
    }
    
    .product-image-wrap {
        height: 220px;
    }
    
    .view-more-btn {
        padding: 12px 24px;
        font-size: 14px;
    }
}

@media (max-width: 360px) {
    .category-tab {
        padding: 10px 16px;
        font-size: 13px;
    }
    
    .product-info {
        padding: 12px 14px 16px 14px;
    }
    
    .product-variation {
        padding: 8px 12px;
        font-size: 0.8rem;
    }
}
</style>

<!-- Shop by Categories Section -->
<section class="category-tabs-section">
    <div class="category-tabs-container">
        <div class="category-tabs-header">
            <h2 class="category-tabs-title">Shop by Category</h2>
            <p class="category-tabs-subtitle">Explore our curated collection of fragrances organized by category</p>
        </div>
        
        <div class="category-tabs-wrapper">
            <div class="category-tabs-container-scroll">
                <button class="category-scroll-btn prev hidden" aria-label="Scroll left">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div class="category-tabs-scroll" id="categoryTabs">
                    <!-- Category tabs will be rendered here -->
                </div>
                
                <button class="category-scroll-btn next hidden" aria-label="Scroll right">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        <div class="category-products-container">
            <div id="categoryProducts" class="category-products-grid">
                <!-- Category products will be loaded here -->
            </div>
            <div id="categoryLoading" class="category-loading" style="display:none;">
                <i class="fas fa-spinner fa-spin"></i> Loading products...
            </div>
            <div id="categoryEmpty" class="category-empty-state" style="display:none;">
                <i class="fas fa-box-open"></i>
                <h3>No products found</h3>
                <p>There are no products available in this category at the moment.</p>
            </div>
        </div>
    </div>
</section>

<!-- Embedded product and category data -->
<script>
window.__CATEGORY_PRODUCTS__ = <?= json_encode($categoryProductsMap, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
window.__CATEGORIES__ = <?= json_encode($categories, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
window.__CURRENT_USER__ = <?= isset($_SESSION['user_id']) ? json_encode((int)$_SESSION['user_id']) : 'null'; ?>;
</script>

<script>
(function(){
    const categoryProductsMap = window.__CATEGORY_PRODUCTS__ || {};
    const categories = window.__CATEGORIES__ || [];
    const categoryTabs = document.getElementById('categoryTabs');
    const categoryProducts = document.getElementById('categoryProducts');
    const categoryLoading = document.getElementById('categoryLoading');
    const categoryEmpty = document.getElementById('categoryEmpty');
    const scrollPrevBtn = document.querySelector('.category-scroll-btn.prev');
    const scrollNextBtn = document.querySelector('.category-scroll-btn.next');
    const API_ADD_TO_CART = 'api/add_to_cart.php';
    const API_TOGGLE_WISHLIST = 'api/toggle_wishlist.php';
    const placeholder = 'assets/images/placeholder.png';

    // Current state
    let currentCategoryId = null;

    /* Utility: escape for attribute insertion */
    function esc(s) {
        return String(s === null || s === undefined ? '' : s)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;');
    }

    /* Create product card DOM */
    function createCard(p) {
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
                
                varHtml += `<div class="product-variation ${i===0 ? 'active' : ''}" 
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
            p.tags.forEach(t => tagsHtml += `<div class="product-tag">${esc(t)}</div>`);
        }

        // Build card markup
        const card = document.createElement('div');
        card.className = 'product-card';
        card.dataset.productId = p.id;
        card.innerHTML = `
            <div class="product-image-wrap">
                <img class="product-image" src="${esc(img1)}" alt="${esc(p.name)}" loading="lazy">
                <img class="product-image-2" src="${esc(img2)}" alt="${esc(p.name)}" loading="lazy">
                <div class="product-brand">${esc(p.brand)}</div>
                <button class="product-wishlist ${p.is_in_wishlist ? 'active' : ''}" 
                        data-product-id="${esc(p.id)}" 
                        aria-label="Wishlist">
                    <i class="${p.is_in_wishlist ? 'fas' : 'far'} fa-heart"></i>
                </button>
            </div>
            <div class="product-info">
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:8px;">
                        <div style="flex:1;">
                            <div class="product-category">${esc(p.category_name)}</div>
                            <div class="product-name">${esc(p.name)}</div>
                        </div>
                        ${hasSale ? `<div class="product-sale">-${pctOff}%</div>` : ''}
                    </div>
                    
                    <div class="product-price-row">
                        <div>
                            <div class="product-price">${hasSale ? '₹' + sale.toFixed(2) : '₹' + (price || 0).toFixed(2)}</div>
                            ${hasSale ? `<div class="product-original">₹${price.toFixed(2)}</div>` : ''}
                        </div>
                    </div>
                    
                    ${varHtml ? `<div class="product-variations">${varHtml}</div>` : ''}
                </div>
                
                <div class="product-actions">
                    <button class="product-add-btn" 
                            data-product-id="${esc(p.id)}" 
                            data-variation-id="${esc(p.default_variation ? p.default_variation.id : '')}">
                        <i class="fas fa-cart-plus"></i> ADD TO CART
                    </button>
                </div>
            </div>
        `;

        return card;
    }

    /* Render category tabs */
    function renderCategoryTabs() {
        if (!categories || categories.length === 0) {
            categoryTabs.innerHTML = '<div style="padding:20px;color:var(--muted);text-align:center">No categories available.</div>';
            return;
        }

        categoryTabs.innerHTML = '';

        // Add category tabs
        categories.forEach(cat => {
            const tab = document.createElement('button');
            tab.className = 'category-tab';
            tab.textContent = cat.name;
            tab.dataset.categoryId = cat.id;
            tab.dataset.categorySlug = cat.slug;
            tab.addEventListener('click', () => showCategoryProducts(cat.id));
            categoryTabs.appendChild(tab);
        });

        // Show scroll buttons if tabs overflow
        updateScrollButtons();
        
        // Show first category by default
        if (categories.length > 0) {
            const firstTab = categoryTabs.querySelector('.category-tab');
            firstTab.classList.add('active');
            showCategoryProducts(categories[0].id);
        }
    }

    /* Show products for a specific category */
    function showCategoryProducts(categoryId) {
        currentCategoryId = categoryId;
        
        // Update active tab
        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelector(`.category-tab[data-category-id="${categoryId}"]`).classList.add('active');
        
        // Show loading
        categoryLoading.style.display = 'block';
        categoryProducts.style.display = 'none';
        categoryEmpty.style.display = 'none';
        
        // Load products after a short delay for smooth transition
        setTimeout(() => {
            const products = categoryProductsMap[categoryId] || [];
            renderCategoryProducts(products, categoryId);
        }, 200);
    }

    /* Render category products */
    function renderCategoryProducts(products, categoryId) {
        categoryLoading.style.display = 'none';
        
        if (!products || products.length === 0) {
            categoryEmpty.style.display = 'block';
            categoryProducts.style.display = 'none';
            return;
        }
        
        categoryEmpty.style.display = 'none';
        categoryProducts.style.display = 'grid';
        categoryProducts.innerHTML = '';
        
        const frag = document.createDocumentFragment();
        products.forEach(p => {
            frag.appendChild(createCard(p));
        });
        
        // Add View More button
        const viewMoreDiv = document.createElement('div');
        viewMoreDiv.className = 'category-view-more';
        const currentCategory = categories.find(cat => cat.id == categoryId);
        const categorySlug = currentCategory ? currentCategory.slug : 'products';
        viewMoreDiv.innerHTML = `
            <a href="category.php?slug=${esc(categorySlug)}" class="view-more-btn">
                View All ${currentCategory ? currentCategory.name : 'Category'} Products
                <i class="fas fa-arrow-right"></i>
            </a>
        `;
        frag.appendChild(viewMoreDiv);
        
        categoryProducts.appendChild(frag);
        
        attachInteractions();
    }

    /* Update scroll buttons visibility */
    function updateScrollButtons() {
        const tabsScroll = document.querySelector('.category-tabs-scroll');
        const hasOverflow = tabsScroll.scrollWidth > tabsScroll.clientWidth;
        
        if (hasOverflow) {
            scrollPrevBtn.classList.remove('hidden');
            scrollNextBtn.classList.remove('hidden');
            checkScrollPosition();
        } else {
            scrollPrevBtn.classList.add('hidden');
            scrollNextBtn.classList.add('hidden');
        }
    }

    /* Check scroll position for button states */
    function checkScrollPosition() {
        const tabsScroll = document.querySelector('.category-tabs-scroll');
        const scrollLeft = tabsScroll.scrollLeft;
        const maxScroll = tabsScroll.scrollWidth - tabsScroll.clientWidth;
        
        scrollPrevBtn.classList.toggle('hidden', scrollLeft <= 0);
        scrollNextBtn.classList.toggle('hidden', scrollLeft >= maxScroll - 5); // 5px tolerance
    }

    /* Scroll tabs */
    function scrollTabs(direction) {
        const tabsScroll = document.querySelector('.category-tabs-scroll');
        const scrollAmount = 200;
        
        if (direction === 'prev') {
            tabsScroll.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else {
            tabsScroll.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
        
        // Update button states after scroll
        setTimeout(checkScrollPosition, 300);
    }

    /* Attach interactions to product cards */
    function attachInteractions() {
        // Variation selection
        categoryProducts.addEventListener('click', function(e) {
            const variation = e.target.closest('.product-variation');
            if (!variation) return;

            e.preventDefault();
            e.stopPropagation();

            const card = variation.closest('.product-card');
            const allVariations = card.querySelectorAll('.product-variation');
            
            allVariations.forEach(v => v.classList.remove('active'));
            variation.classList.add('active');

            // Update price
            const priceEl = card.querySelector('.product-price');
            const origEl = card.querySelector('.product-original');
            const price = parseFloat(variation.getAttribute('data-price') || 0);
            const sale = variation.getAttribute('data-sale') ? parseFloat(variation.getAttribute('data-sale')) : null;

            if (sale !== null && !isNaN(sale) && sale < price) {
                priceEl.textContent = '₹' + sale.toFixed(2);
                if (origEl) {
                    origEl.textContent = '₹' + price.toFixed(2);
                } else {
                    const newOrig = document.createElement('div');
                    newOrig.className = 'product-original';
                    newOrig.textContent = '₹' + price.toFixed(2);
                    priceEl.insertAdjacentElement('afterend', newOrig);
                }
            } else {
                priceEl.textContent = '₹' + price.toFixed(2);
                if (origEl) origEl.remove();
            }

            // Update add button variation
            const btn = card.querySelector('.product-add-btn');
            if (btn) btn.dataset.variationId = variation.dataset.variationId || '';

            // Update images if available
            const productId = card.dataset.productId;
            const allProducts = Object.values(categoryProductsMap).flat();
            const prod = allProducts.find(pp => String(pp.id) === String(productId));
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
                
                const imgEl2 = card.querySelector('.product-image-2');
                if (imgEl2 && newImg) {
                    imgEl2.src = newImg;
                }
            }
        });

        // Wishlist
        categoryProducts.addEventListener('click', async function(e) {
            const wishlistBtn = e.target.closest('.product-wishlist');
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
        categoryProducts.addEventListener('click', async function(e) {
            const addBtn = e.target.closest('.product-add-btn');
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

    // Initialize
    renderCategoryTabs();
    
    // Event listeners for scroll buttons
    scrollPrevBtn.addEventListener('click', () => scrollTabs('prev'));
    scrollNextBtn.addEventListener('click', () => scrollTabs('next'));
    
    // Update scroll buttons on resize and scroll
    window.addEventListener('resize', updateScrollButtons);
    categoryTabs.addEventListener('scroll', checkScrollPosition);
    
    // Check scroll buttons on load
    window.addEventListener('load', updateScrollButtons);
})();
</script>