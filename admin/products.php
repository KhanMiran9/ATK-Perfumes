<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$auth = new Auth();

// Check if user is logged in and has permission
if (!$auth->isLoggedIn() || !$auth->hasPermission('manage_products')) {
    redirect('../login.php');
}

$database = new Database();
$conn = $database->getConnection();

// Get products with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build search and filter query
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT SQL_CALC_FOUND_ROWS p.*, c.name as category_name, u.name as creator_name
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          LEFT JOIN users u ON p.created_by_user_id = u.id
          WHERE 1=1";

$params = [];

if (!empty($search)) {
    $query .= " AND (p.name LIKE :search OR p.sku LIKE :search2)";
    $params[':search'] = "%$search%";
    $params[':search2'] = "%$search%";
}

if (!empty($category) && $category !== 'all') {
    $query .= " AND p.category_id = :category";
    $params[':category'] = $category;
}

if (!empty($status) && $status !== 'all') {
    $query .= " AND p.is_active = :status";
    $params[':status'] = ($status === 'active' ? 1 : 0);
}

$query .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
$params[':limit'] = $limit;
$params[':offset'] = $offset;

// Prepare and execute query
$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    if ($key === ':limit' || $key === ':offset') {
        $stmt->bindValue($key, $value, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($key, $value);
    }
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count
$totalResult = $conn->query("SELECT FOUND_ROWS()")->fetchColumn();
$totalPages = ceil($totalResult / $limit);

// Get categories for filter
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | LuxePerfume Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Manage Products</h1>
                <div class="header-actions">
                    <a href="product_add.php" class="btn btn-primary">Add New Product</a>
                </div>
            </div>
            
            <!-- Filters and Search -->
            <div class="filters-card">
                <form method="GET" class="filter-form">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <select name="category">
                            <option value="all">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <select name="status">
                            <option value="all">All Status</option>
                            <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-secondary">Apply Filters</button>
                    <a href="products.php" class="btn btn-outline">Clear</a>
                </form>
            </div>
            
            <!-- Products Table -->
            <div class="card">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($products) > 0): ?>
                                <?php foreach ($products as $product): 
                                    // Get default variation price and stock
                                    $variationQuery = "SELECT price, sale_price, stock 
                                                      FROM product_variations 
                                                      WHERE product_id = ? AND is_default = 1";
                                    $variationStmt = $conn->prepare($variationQuery);
                                    $variationStmt->bind_param('i', $product['id']);
                                    $variationStmt->execute();
                                    $variation = $variationStmt->get_result()->fetch_assoc();
                                    
                                    $price = $variation['sale_price'] ?: $variation['price'];
                                    $stock = $variation['stock'];
                                ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td>
                                            <?php 
                                            $imageQuery = "SELECT file_path FROM product_media WHERE product_id = ? ORDER BY sort_order LIMIT 1";
                                            $imageStmt = $conn->prepare($imageQuery);
                                            $imageStmt->bind_param('i', $product['id']);
                                            $imageStmt->execute();
                                            $image = $imageStmt->get_result()->fetch_assoc();
                                            ?>
                                            <img src="<?php echo $image ? '../assets/uploads/products/' . $image['file_path'] : '../assets/images/placeholder.jpg'; ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                 class="product-thumb">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                            <br>
                                            <small class="text-muted">by <?php echo htmlspecialchars($product['creator_name']); ?></small>
                                        </td>
                                        <td><?php echo $product['sku']; ?></td>
                                        <td><?php echo $product['category_name']; ?></td>
                                        <td>$<?php echo number_format($price, 2); ?></td>
                                        <td>
                                            <span class="stock-badge <?php echo $stock > 10 ? 'in-stock' : ($stock > 0 ? 'low-stock' : 'out-of-stock'); ?>">
                                                <?php echo $stock; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $product['is_active'] ? 'active' : 'inactive'; ?>">
                                                <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($product['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <a href="../product.php?id=<?php echo $product['id']; ?>" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                                <form method="POST" action="ajax/delete_product.php" class="inline-form" onsubmit="return confirm('Are you sure?')">
                                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center">No products found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
            </div>
        </div>
    </div>
</body>
</html>