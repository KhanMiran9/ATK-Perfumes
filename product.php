<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: shop.php');
    exit();
}

$productId = (int)$_GET['id'];
$database = new Database();
$conn = $database->getConnection();

// Get product details
$query = "SELECT p.*, c.name as category_name, c.slug as category_slug
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE p.id = :id AND p.is_active = 1";
          
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $productId, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: shop.php');
    exit();
}

// Get product variations
$variationQuery = "SELECT * FROM product_variations WHERE product_id = :product_id ORDER BY is_default DESC";
$variationStmt = $conn->prepare($variationQuery);
$variationStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
$variationStmt->execute();
$variations = $variationStmt->fetchAll(PDO::FETCH_ASSOC);

// Get product media
$mediaQuery = "SELECT * FROM product_media WHERE product_id = :product_id ORDER BY sort_order";
$mediaStmt = $conn->prepare($mediaQuery);
$mediaStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
$mediaStmt->execute();
$media = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);

// Get product reviews
$reviewQuery = "SELECT r.*, u.name as user_name 
                FROM reviews r 
                LEFT JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = :product_id AND r.approved = 1 
                ORDER BY r.created_at DESC";
$reviewStmt = $conn->prepare($reviewQuery);
$reviewStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
$reviewStmt->execute();
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate average rating
$ratingQuery = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                FROM reviews 
                WHERE product_id = :product_id AND approved = 1";
$ratingStmt = $conn->prepare($ratingQuery);
$ratingStmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
$ratingStmt->execute();
$ratingData = $ratingStmt->fetch(PDO::FETCH_ASSOC);

$avgRating = $ratingData['avg_rating'] ? round($ratingData['avg_rating'], 1) : 0;
$totalReviews = $ratingData['total_reviews'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | LuxePerfume</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="product-detail">
        <div class="container">
            <nav class="breadcrumb">
                <a href="index.php">Home</a> >
                <a href="shop.php">Shop</a> >
                <a href="shop.php?category=<?php echo $product['category_slug']; ?>"><?php echo $product['category_name']; ?></a> >
                <span><?php echo htmlspecialchars($product['name']); ?></span>
            </nav>

            <div class="product-detail-content">
                <!-- Product Images -->
                <div class="product-gallery">
                    <div class="main-image">
                        <?php if (!empty($media)): ?>
                            <img src="assets/uploads/products/<?php echo $media[0]['file_path']; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" id="mainProductImage">
                        <?php else: ?>
                            <img src="assets/images/placeholder.jpg" alt="No image available">
                        <?php endif; ?>
                    </div>
                    <div class="thumbnail-images">
                        <?php foreach ($media as $image): ?>
                            <img src="assets/uploads/products/<?php echo $image['file_path']; ?>" 
                                 alt="<?php echo $image['alt_text'] ?: htmlspecialchars($product['name']); ?>"
                                 class="thumbnail" 
                                 onclick="changeMainImage(this.src)">
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <div class="product-meta">
                        <span class="product-sku">SKU: <?php echo $product['sku']; ?></span>
                        <span class="product-category">Category: <?php echo $product['category_name']; ?></span>
                        <span class="product-brand">Brand: <?php echo $product['brand']; ?></span>
                    </div>

                    <div class="product-rating">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?php echo $i <= round($avgRating) ? 'filled' : ''; ?>">★</span>
                            <?php endfor; ?>
                            <span>(<?php echo $totalReviews; ?> reviews)</span>
                        </div>
                    </div>

                    <div class="product-price">
                        <?php 
                        $defaultVariation = array_filter($variations, function($v) { return $v['is_default']; });
                        $defaultVariation = reset($defaultVariation);
                        ?>
                        <?php if ($defaultVariation && $defaultVariation['sale_price']): ?>
                            <span class="sale-price">$<?php echo number_format($defaultVariation['sale_price'], 2); ?></span>
                            <span class="original-price">$<?php echo number_format($defaultVariation['price'], 2); ?></span>
                        <?php elseif ($defaultVariation): ?>
                            <span class="current-price">$<?php echo number_format($defaultVariation['price'], 2); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="product-description">
                        <p><?php echo htmlspecialchars($product['short_desc']); ?></p>
                    </div>

                    <!-- Variation Selector -->
                    <?php if (count($variations) > 1): ?>
                    <div class="variation-selector">
                        <h3>Available Options</h3>
                        <div class="variation-options">
                            <?php foreach ($variations as $variation): 
                                $attributes = json_decode($variation['sku_attributes_json'], true);
                            ?>
                                <div class="variation-option" data-variation-id="<?php echo $variation['id']; ?>"
                                     data-price="<?php echo $variation['sale_price'] ?: $variation['price']; ?>"
                                     data-stock="<?php echo $variation['stock']; ?>">
                                    <input type="radio" name="variation" id="variation-<?php echo $variation['id']; ?>" 
                                           value="<?php echo $variation['id']; ?>" 
                                           <?php echo $variation['is_default'] ? 'checked' : ''; ?>>
                                    <label for="variation-<?php echo $variation['id']; ?>">
                                        <?php echo implode(' / ', $attributes); ?>
                                        <?php if ($variation['sale_price']): ?>
                                            - $<?php echo number_format($variation['sale_price'], 2); ?>
                                        <?php else: ?>
                                            - $<?php echo number_format($variation['price'], 2); ?>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Add to Cart -->
                    <div class="add-to-cart-section">
                        <div class="quantity-selector">
                            <button class="quantity-btn" onclick="updateQuantity(-1)">-</button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $defaultVariation ? $defaultVariation['stock'] : 0; ?>">
                            <button class="quantity-btn" onclick="updateQuantity(1)">+</button>
                        </div>
                        
                        <button class="btn btn-primary add-to-cart-btn" 
                                data-product-id="<?php echo $product['id']; ?>"
                                data-variation-id="<?php echo $defaultVariation ? $defaultVariation['id'] : ''; ?>">
                            Add to Cart
                        </button>
                        
                        <button class="btn btn-secondary add-to-wishlist-btn" 
                                data-product-id="<?php echo $product['id']; ?>">
                            Add to Wishlist
                        </button>
                    </div>

                    <div class="stock-info">
                        <?php if ($defaultVariation && $defaultVariation['stock'] > 0): ?>
                            <span class="in-stock">In Stock (<?php echo $defaultVariation['stock']; ?> available)</span>
                        <?php else: ?>
                            <span class="out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Product Tabs -->
            <div class="product-tabs">
                <div class="tab-headers">
                    <button class="tab-header active" data-tab="description">Description</button>
                    <button class="tab-header" data-tab="details">Details</button>
                    <button class="tab-header" data-tab="reviews">Reviews (<?php echo $totalReviews; ?>)</button>
                </div>
                
                <div class="tab-content">
                    <div class="tab-pane active" id="description">
                        <div class="product-long-desc">
                            <?php echo nl2br(htmlspecialchars($product['long_desc'])); ?>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="details">
                        <div class="product-details">
                            <h3>Product Details</h3>
                            <ul>
                                <li><strong>SKU:</strong> <?php echo $product['sku']; ?></li>
                                <li><strong>Brand:</strong> <?php echo $product['brand']; ?></li>
                                <li><strong>Category:</strong> <?php echo $product['category_name']; ?></li>
                                <li><strong>Weight:</strong> <?php echo $defaultVariation ? $defaultVariation['weight'] . 'g' : 'N/A'; ?></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="reviews">
                        <div class="product-reviews">
                            <h3>Customer Reviews</h3>
                            
                            <?php if ($totalReviews > 0): ?>
                                <div class="reviews-list">
                                    <?php foreach ($reviews as $review): ?>
                                        <div class="review-item">
                                            <div class="review-header">
                                                <div class="reviewer-name"><?php echo htmlspecialchars($review['user_name']); ?></div>
                                                <div class="review-rating">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <span class="star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>">★</span>
                                                    <?php endfor; ?>
                                                </div>
                                                <div class="review-date"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></div>
                                            </div>
                                            <?php if ($review['title']): ?>
                                                <h4 class="review-title"><?php echo htmlspecialchars($review['title']); ?></h4>
                                            <?php endif; ?>
                                            <p class="review-body"><?php echo nl2br(htmlspecialchars($review['body'])); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p>No reviews yet. Be the first to review this product!</p>
                            <?php endif; ?>
                            
                            <!-- Review Form -->
                            <div class="review-form">
                                <h4>Write a Review</h4>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form id="reviewForm">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <div class="form-group">
                                            <label>Rating</label>
                                            <div class="rating-input">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span class="star-input" data-rating="<?php echo $i; ?>">★</span>
                                                <?php endfor; ?>
                                                <input type="hidden" name="rating" id="ratingValue" value="5">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="reviewTitle">Title</label>
                                            <input type="text" id="reviewTitle" name="title" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="reviewBody">Review</label>
                                            <textarea id="reviewBody" name="body" rows="5" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit Review</button>
                                    </form>
                                <?php else: ?>
                                    <p>Please <a href="login.php">login</a> to write a review.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/product.js"></script>
</body>
</html>