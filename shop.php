<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$database = new Database();
$conn = $database->getConnection();

// Get filters from query string
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$minPrice = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$maxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query with filters
$query = "SELECT SQL_CALC_FOUND_ROWS p.id, p.name, p.slug, p.short_desc, 
                 pv.price, pv.sale_price, pm.file_path as image,
                 c.name as category_name
          FROM products p
          LEFT JOIN product_variations pv ON p.id = pv.product_id AND pv.is_default = 1
          LEFT JOIN product_media pm ON p.id = pm.product_id AND pm.sort_order = 0
          LEFT JOIN categories c ON p.category_id = c.id
          WHERE p.is_active = 1";

$params = [];

if (!empty($category)) {
    $query .= " AND c.slug = :category";
    $params[':category'] = $category;
}

if (!empty($search)) {
    $query .= " AND (p.name LIKE :search OR p.short_desc LIKE :search OR p.long_desc LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($minPrice) && is_numeric($minPrice)) {
    $query .= " AND pv.price >= :min_price";
    $params[':min_price'] = $minPrice;
}

if (!empty($maxPrice) && is_numeric($maxPrice)) {
    $query .= " AND pv.price <= :max_price";
    $params[':max_price'] = $maxPrice;
}

$query .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

// Prepare and execute query
$stmt = $conn->prepare($query);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$totalResult = $conn->query("SELECT FOUND_ROWS()")->fetch(PDO::FETCH_COLUMN);
$totalPages = ceil($totalResult / $limit);

// Get categories for filter sidebar
$categoryQuery = "SELECT id, name, slug FROM categories WHERE parent_id IS NULL";
$categoryStmt = $conn->prepare($categoryQuery);
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop | LuxePerfume</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="shop-header">
        <div class="container">
            <h1>Our Fragrance Collection</h1>
            <p>Discover our exquisite range of luxury perfumes</p>
        </div>
    </section>

    <section class="shop-content">
        <div class="container">
            <div class="shop-layout">
                <!-- Filters Sidebar -->
                <aside class="shop-filters">
                    <h3>Filters</h3>
                    
                    <div class="filter-group">
                        <h4>Categories</h4>
                        <ul class="filter-list">
                            <?php foreach ($categories as $cat): ?>
                            <li>
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="category" value="<?php echo $cat['slug']; ?>" 
                                        <?php echo $category === $cat['slug'] ? 'checked' : ''; ?>>
                                    <span><?php echo $cat['name']; ?></span>
                                </label>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="filter-group">
                        <h4>Price Range</h4>
                        <div class="price-range">
                            <input type="number" placeholder="Min" name="min_price" value="<?php echo $minPrice; ?>">
                            <span>-</span>
                            <input type="number" placeholder="Max" name="max_price" value="<?php echo $maxPrice; ?>">
                        </div>
                    </div>

                    <button class="btn btn-primary apply-filters">Apply Filters</button>
                    <button class="btn btn-secondary clear-filters">Clear All</button>
                </aside>

                <!-- Products Grid -->
                <main class="shop-main">
                    <div class="shop-toolbar">
                        <div class="results-count">
                            Showing <?php echo count($products); ?> of <?php echo $totalResult; ?> products
                        </div>
                        <div class="sort-options">
                            <select id="sortSelect">
                                <option value="newest">Newest First</option>
                                <option value="price_asc">Price: Low to High</option>
                                <option value="price_desc">Price: High to Low</option>
                                <option value="name_asc">Name: A to Z</option>
                                <option value="name_desc">Name: Z to A</option>
                            </select>
                        </div>
                    </div>

                    <div class="products-grid">
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="<?php echo $product['image'] ? 'assets/uploads/products/' . $product['image'] : 'assets/images/placeholder.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="product-overlay">
                                        <button class="quick-view" data-product-id="<?php echo $product['id']; ?>">Quick View</button>
                                        <button class="add-wishlist" data-product-id="<?php echo $product['id']; ?>">♡</button>
                                    </div>
                                </div>
                                <div class="product-info">
                                    <span class="product-category"><?php echo $product['category_name']; ?></span>
                                    <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <div class="product-price">
                                        <?php if ($product['sale_price']): ?>
                                            <span class="sale-price">$<?php echo number_format($product['sale_price'], 2); ?></span>
                                            <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                                        <?php else: ?>
                                            <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-actions">
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary">View Details</a>
                                        <button class="btn btn-primary add-to-cart" 
                                                data-product-id="<?php echo $product['id']; ?>"
                                                data-variation-id="<?php echo $product['id']; ?>">Add to Cart</button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-products">
                                <h3>No products found</h3>
                                <p>Try adjusting your filters or search terms</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="page-link">Previous</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                               class="page-link <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="page-link">Next</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/shop.js"></script>
</body>
</html>