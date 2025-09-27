<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$auth = new Auth();

// Check authentication and permissions
if (!$auth->isLoggedIn() || !$auth->hasPermission('manage_products')) {
    redirect('../login.php');
}

$database = new Database();
$conn = $database->getConnection();

// Handle export functionality
if (isset($_POST['export_products'])) {
    exportProductsToExcel($conn);
    exit;
}

// Handle import functionality
if (isset($_POST['import_products']) && isset($_FILES['import_file'])) {
    $importResult = importProductsFromExcel($conn, $_FILES['import_file']);
}

// Get products with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build search and filter query
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$product_type = isset($_GET['product_type']) ? $_GET['product_type'] : '';

$query = "SELECT SQL_CALC_FOUND_ROWS p.*, c.name as category_name, u.name as creator_name,
                 (SELECT COUNT(*) FROM product_variations pv WHERE pv.product_id = p.id) as variation_count
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          LEFT JOIN users u ON p.created_by_user_id = u.id
          WHERE 1=1";

$params = [];

if (!empty($search)) {
    $query .= " AND (p.name LIKE :search OR p.sku LIKE :search2 OR p.brand LIKE :search3)";
    $params[':search'] = "%$search%";
    $params[':search2'] = "%$search%";
    $params[':search3'] = "%$search%";
}

if (!empty($category) && $category !== 'all') {
    $query .= " AND p.category_id = :category";
    $params[':category'] = $category;
}

if (!empty($status) && $status !== 'all') {
    $query .= " AND p.is_active = :status";
    $params[':status'] = ($status === 'active' ? 1 : 0);
}

if (!empty($product_type) && $product_type !== 'all') {
    if ($product_type === 'simple') {
        $query .= " AND (SELECT COUNT(*) FROM product_variations pv WHERE pv.product_id = p.id) = 1";
    } elseif ($product_type === 'variable') {
        $query .= " AND (SELECT COUNT(*) FROM product_variations pv WHERE pv.product_id = p.id) > 1";
    }
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

// Export function
function exportProductsToExcel($conn) {
    // Fetch all products with complete details
    $query = "SELECT 
                p.id, p.sku, p.name, p.slug, p.short_desc, p.long_desc, 
                p.category_id, c.name as category_name, p.brand, p.is_active,
                p.created_at, p.updated_at,
                pv.id as variation_id, pv.sku as variation_sku, pv.price, 
                pv.sale_price, pv.stock, pv.weight, pv.sku_attributes_json,
                pv.is_default,
                (SELECT file_path FROM product_media pm WHERE pm.product_id = p.id ORDER BY pm.sort_order LIMIT 1) as main_image,
                GROUP_CONCAT(DISTINCT pt.tag) as tags,
                GROUP_CONCAT(DISTINCT pm2.file_path) as gallery_images
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              LEFT JOIN product_variations pv ON p.id = pv.product_id
              LEFT JOIN product_tags pt ON p.id = pt.product_id
              LEFT JOIN product_media pm2 ON p.id = pm2.product_id AND pm2.sort_order > 0
              GROUP BY p.id, pv.id
              ORDER BY p.id, pv.is_default DESC, pv.id";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group by product
    $groupedProducts = [];
    foreach ($productsData as $row) {
        $productId = $row['id'];
        if (!isset($groupedProducts[$productId])) {
            $groupedProducts[$productId] = [
                'product' => $row,
                'variations' => []
            ];
        }
        if ($row['variation_id']) {
            $groupedProducts[$productId]['variations'][] = $row;
        }
    }
    
    // Set headers for Excel file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="products_export_' . date('Y-m-d_H-i-s') . '.xls"');
    
    // Create Excel content
    echo "PRODUCT EXPORT - LUXEPERFUME\n";
    echo "Generated: " . date('Y-m-d H:i:s') . "\n\n";
    
    echo "PRODUCT DATA\t\t\t\t\t\t\t\t\tVARIATION DATA\t\t\t\t\t\t\tIMAGES & TAGS\n";
    echo "ID\tSKU\tName\tCategory\tBrand\tStatus\tShort Desc\tLong Desc\tCreated At\tVariation ID\tVariation SKU\tPrice\tSale Price\tStock\tWeight\tAttributes\tIs Default\tMain Image\tGallery Images\tTags\n";
    
    foreach ($groupedProducts as $productId => $data) {
        $product = $data['product'];
        $variations = $data['variations'];
        
        // If no variations, create one empty variation row
        if (empty($variations)) {
            $variations = [['variation_id' => '', 'variation_sku' => '', 'price' => '', 
                          'sale_price' => '', 'stock' => '', 'weight' => '', 
                          'sku_attributes_json' => '', 'is_default' => '']];
        }
        
        foreach ($variations as $index => $variation) {
            // Clean data for Excel
            $name = str_replace(["\t", "\n", "\r"], ' ', $product['name']);
            $shortDesc = str_replace(["\t", "\n", "\r"], ' ', strip_tags($product['short_desc']));
            $longDesc = str_replace(["\t", "\n", "\r"], ' ', strip_tags($product['long_desc']));
            
            // Parse attributes JSON
            $attributes = '';
            if (!empty($variation['sku_attributes_json'])) {
                $attrData = json_decode($variation['sku_attributes_json'], true);
                if ($attrData) {
                    $attrPairs = [];
                    foreach ($attrData as $key => $value) {
                        $attrPairs[] = "$key: $value";
                    }
                    $attributes = implode('; ', $attrPairs);
                }
            }
            
            // First variation row includes product data, subsequent rows are blank for product columns
            if ($index === 0) {
                echo "{$product['id']}\t{$product['sku']}\t{$name}\t{$product['category_name']}\t{$product['brand']}\t" .
                     ($product['is_active'] ? 'Active' : 'Inactive') . "\t{$shortDesc}\t{$longDesc}\t{$product['created_at']}\t";
            } else {
                echo "\t\t\t\t\t\t\t\t\t";
            }
            
            echo "{$variation['variation_id']}\t{$variation['variation_sku']}\t{$variation['price']}\t{$variation['sale_price']}\t" .
                 "{$variation['stock']}\t{$variation['weight']}\t{$attributes}\t" .
                 ($variation['is_default'] ? 'Yes' : 'No') . "\t";
            
            // Only show images and tags in first row
            if ($index === 0) {
                echo "{$product['main_image']}\t{$product['gallery_images']}\t{$product['tags']}";
            } else {
                echo "\t\t";
            }
            
            echo "\n";
        }
        
        // Add empty row between products
        echo "\n";
    }
    exit;
}

// Import function
function importProductsFromExcel($conn, $file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload failed'];
    }
    
    $filePath = $file['tmp_name'];
    $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    if ($fileType !== 'xls' && $fileType !== 'xlsx' && $fileType !== 'csv') {
        return ['success' => false, 'message' => 'Invalid file format. Please upload Excel or CSV file.'];
    }
    
    try {
        // Read the file
        $lines = file($filePath);
        $imported = 0;
        $errors = [];
        $currentProduct = null;
        
        // Skip header lines
        for ($i = 0; $i < 4; $i++) {
            array_shift($lines);
        }
        
        foreach ($lines as $lineNumber => $line) {
            $lineNumber += 5; // Adjust for header lines
            $data = str_getcsv($line, "\t");
            
            if (count($data) < 5 || empty(trim($line))) {
                continue; // Skip empty lines
            }
            
            // Check if this is a new product row (has product ID)
            if (!empty($data[0]) && is_numeric($data[0])) {
                // Process the previous product if exists
                if ($currentProduct) {
                    $result = processImportedProduct($conn, $currentProduct);
                    if ($result['success']) {
                        $imported++;
                    } else {
                        $errors[] = "Product {$currentProduct['sku']}: " . $result['message'];
                    }
                }
                
                // Start new product
                $currentProduct = [
                    'id' => $data[0],
                    'sku' => $data[1],
                    'name' => $data[2],
                    'category_name' => $data[3],
                    'brand' => $data[4],
                    'status' => trim($data[5]) === 'Active' ? 1 : 0,
                    'short_desc' => $data[6],
                    'long_desc' => $data[7],
                    'created_at' => $data[8],
                    'main_image' => $data[17] ?? '',
                    'gallery_images' => $data[18] ?? '',
                    'tags' => $data[19] ?? '',
                    'variations' => []
                ];
            }
            
            // Add variation data if we have a current product
            if ($currentProduct && !empty($data[9])) {
                $variation = [
                    'variation_id' => $data[9],
                    'variation_sku' => $data[10],
                    'price' => $data[11],
                    'sale_price' => $data[12] ?: null,
                    'stock' => $data[13],
                    'weight' => $data[14],
                    'attributes' => $data[15],
                    'is_default' => trim($data[16]) === 'Yes'
                ];
                $currentProduct['variations'][] = $variation;
            }
        }
        
        // Process the last product
        if ($currentProduct) {
            $result = processImportedProduct($conn, $currentProduct);
            if ($result['success']) {
                $imported++;
            } else {
                $errors[] = "Product {$currentProduct['sku']}: " . $result['message'];
            }
        }
        
        return [
            'success' => true,
            'imported' => $imported,
            'errors' => $errors
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Failed to process file: ' . $e->getMessage()];
    }
}

function processImportedProduct($conn, $productData) {
    try {
        $conn->beginTransaction();
        
        // Get or create category
        $categoryId = getCategoryIdByName($conn, $productData['category_name']);
        if (!$categoryId) {
            // Create new category
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $productData['category_name'])));
            $stmt = $conn->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
            $stmt->execute([$productData['category_name'], $slug]);
            $categoryId = $conn->lastInsertId();
        }
        
        // Check if product already exists
        $checkStmt = $conn->prepare("SELECT id FROM products WHERE sku = ?");
        $checkStmt->execute([$productData['sku']]);
        
        if ($checkStmt->rowCount() > 0) {
            // Update existing product
            $existingProduct = $checkStmt->fetch(PDO::FETCH_ASSOC);
            $productId = $existingProduct['id'];
            
            $updateStmt = $conn->prepare("UPDATE products SET name = ?, category_id = ?, brand = ?, 
                                        short_desc = ?, long_desc = ?, is_active = ? WHERE id = ?");
            $updateStmt->execute([
                $productData['name'], $categoryId, $productData['brand'],
                $productData['short_desc'], $productData['long_desc'], 
                $productData['status'], $productId
            ]);
            
            // Delete existing variations
            $conn->prepare("DELETE FROM product_variations WHERE product_id = ?")->execute([$productId]);
            
        } else {
            // Insert new product
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $productData['name'])));
            $insertStmt = $conn->prepare("INSERT INTO products (sku, name, slug, category_id, brand, 
                                        short_desc, long_desc, is_active, created_by_user_id) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insertStmt->execute([
                $productData['sku'], $productData['name'], $slug, $categoryId, $productData['brand'],
                $productData['short_desc'], $productData['long_desc'], $productData['status'],
                $_SESSION['user_id'] ?? 1
            ]);
            $productId = $conn->lastInsertId();
        }
        
        // Handle variations
        foreach ($productData['variations'] as $variation) {
            // Parse attributes
            $attributes = [];
            if (!empty($variation['attributes'])) {
                $attrPairs = explode(';', $variation['attributes']);
                foreach ($attrPairs as $pair) {
                    $parts = explode(':', $pair, 2);
                    if (count($parts) === 2) {
                        $key = trim($parts[0]);
                        $value = trim($parts[1]);
                        $attributes[$key] = $value;
                    }
                }
            }
            
            $variationStmt = $conn->prepare("INSERT INTO product_variations 
                                            (product_id, sku, price, sale_price, stock, weight, 
                                             sku_attributes_json, is_default) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $variationStmt->execute([
                $productId, $variation['variation_sku'], $variation['price'], 
                $variation['sale_price'], $variation['stock'], $variation['weight'],
                json_encode($attributes), $variation['is_default'] ? 1 : 0
            ]);
        }
        
        // Handle tags
        if (!empty($productData['tags'])) {
            $tags = array_filter(array_map('trim', explode(',', $productData['tags'])));
            $tagStmt = $conn->prepare("INSERT IGNORE INTO product_tags (product_id, tag) VALUES (?, ?)");
            foreach ($tags as $tag) {
                $tagStmt->execute([$productId, $tag]);
            }
        }
        
        $conn->commit();
        return ['success' => true];
        
    } catch (Exception $e) {
        $conn->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getCategoryIdByName($conn, $categoryName) {
    if (empty($categoryName)) return null;
    
    $stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->execute([$categoryName]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $category ? $category['id'] : null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | LuxePerfume Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1><i class="fas fa-cubes"></i> Manage Products</h1>
                <div class="header-actions">
                    <a href="product_add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                    <a href="attributes.php" class="btn btn-secondary">
                        <i class="fas fa-tags"></i> Manage Attributes
                    </a>
                </div>
            </div>
            
            <!-- Export/Import Section -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-file-export"></i> Export/Import Products</h2>
                </div>
                <div class="card-body">
                    <div class="export-import-section">
                        <div class="export-import-form">
                            <form method="POST" class="form-group">
                                <button type="submit" name="export_products" class="btn btn-success">
                                    <i class="fas fa-download"></i> Export to Excel
                                </button>
                                <small>Exports all products with variations, images, and tags</small>
                            </form>
                            
                            <form method="POST" enctype="multipart/form-data" class="form-group">
                                <label for="import_file"><i class="fas fa-upload"></i> Import from Excel:</label>
                                <input type="file" name="import_file" accept=".xls,.xlsx,.csv" required 
                                       class="form-control">
                                <button type="submit" name="import_products" class="btn btn-primary" style="margin-top: 10px;">
                                    <i class="fas fa-upload"></i> Import Products
                                </button>
                            </form>
                        </div>
                        <div class="import-notice">
                            <strong><i class="fas fa-info-circle"></i> Import Format:</strong> 
                            Use exported Excel file format. New products will be created, existing products will be updated.
                        </div>
                        
                        <?php if (isset($importResult)): ?>
                            <div class="alert <?php echo $importResult['success'] ? 'alert-success' : 'alert-error'; ?>" style="margin-top: 15px;">
                                <?php if ($importResult['success']): ?>
                                    <i class="fas fa-check-circle"></i> 
                                    Successfully imported <?php echo $importResult['imported']; ?> products.
                                    <?php if (!empty($importResult['errors'])): ?>
                                        <br><strong>Errors:</strong> 
                                        <?php echo implode(', ', array_slice($importResult['errors'], 0, 5)); ?>
                                        <?php if (count($importResult['errors']) > 5): ?>
                                            ... and <?php echo count($importResult['errors']) - 5; ?> more errors.
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <i class="fas fa-exclamation-circle"></i> 
                                    Import failed: <?php echo $importResult['message']; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Filters and Search -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-filter"></i> Filters</h2>
                </div>
                <div class="card-body">
                    <form method="GET" class="filter-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="search">Search Products</label>
                                <input type="text" name="search" id="search" 
                                       placeholder="Search by name, SKU, or brand..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select name="category" id="category">
                                    <option value="all">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" 
                                                <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status">
                                    <option value="all">All Status</option>
                                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="product_type">Product Type</label>
                                <select name="product_type" id="product_type">
                                    <option value="all">All Types</option>
                                    <option value="simple" <?php echo $product_type === 'simple' ? 'selected' : ''; ?>>Simple Products</option>
                                    <option value="variable" <?php echo $product_type === 'variable' ? 'selected' : ''; ?>>Variable Products</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <a href="products.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Products Table -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-list"></i> Products (<?php echo $totalResult; ?>)</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Product Info</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Variations</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($products) > 0): ?>
                                    <?php foreach ($products as $product): 
                                        // Get product image
                                        $imageQuery = "SELECT file_path FROM product_media WHERE product_id = ? ORDER BY sort_order LIMIT 1";
                                        $imageStmt = $conn->prepare($imageQuery);
                                        $imageStmt->execute([$product['id']]);
                                        $image = $imageStmt->fetch(PDO::FETCH_ASSOC);
                                        
                                        $imagePath = $image ? '../assets/uploads/' . $image['file_path'] : '../assets/images/1_43.webp';
                                        
                                        // Get price range and total stock
                                        $priceQuery = "SELECT MIN(price) as min_price, MAX(price) as max_price, 
                                                      SUM(stock) as total_stock, COUNT(*) as var_count
                                                      FROM product_variations WHERE product_id = ?";
                                        $priceStmt = $conn->prepare($priceQuery);
                                        $priceStmt->execute([$product['id']]);
                                        $priceData = $priceStmt->fetch(PDO::FETCH_ASSOC);
                                        
                                        $minPrice = $priceData['min_price'] ?? 0;
                                        $maxPrice = $priceData['max_price'] ?? 0;
                                        $totalStock = $priceData['total_stock'] ?? 0;
                                        $varCount = $priceData['var_count'] ?? 0;
                                        
                                        // Determine price display
                                        if ($varCount > 1) {
                                            $priceDisplay = "₹" . number_format($minPrice, 2) . " - ₹" . number_format($maxPrice, 2);
                                        } else {
                                            $priceDisplay = "₹" . number_format($minPrice, 2);
                                        }
                                    ?>
                                        <tr>
                                            <td><?php echo $product['id']; ?></td>
                                            <td>
                                                <img src="<?php echo $imagePath; ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                     class="product-thumb"
                                                     onerror="this.src='../assets/images/1_43.webp'">
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($product['brand']); ?></small>
                                                <br>
                                                <small class="text-muted">by <?php echo htmlspecialchars($product['creator_name']); ?></small>
                                            </td>
                                            <td>
                                                <code><?php echo $product['sku']; ?></code>
                                                <?php if ($varCount > 1): ?>
                                                    <br><small class="text-gold">+<?php echo $varCount; ?> variations</small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $product['category_name']; ?></td>
                                            <td>
                                                <strong><?php echo $priceDisplay; ?></strong>
                                                <?php if ($varCount > 1): ?>
                                                    <br><small>(<?php echo $varCount; ?> options)</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="stock-badge <?php echo $totalStock > 10 ? 'in-stock' : ($totalStock > 0 ? 'low-stock' : 'out-of-stock'); ?>">
                                                    <?php echo $totalStock; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($varCount > 1): ?>
                                                    <span class="badge badge-primary">Variable</span>
                                                    <br><small><?php echo $varCount; ?> variations</small>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Simple</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $product['is_active'] ? 'active' : 'inactive'; ?>">
                                                    <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($product['created_at'])); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="product_edit.php?id=<?php echo $product['id']; ?>" 
                                                       class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="../product.php?slug=<?php echo $product['slug']; ?>" 
                                                       target="_blank" class="btn btn-secondary btn-sm" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form method="POST" action="ajax/delete_product.php" 
                                                          class="inline-form" 
                                                          onsubmit="return confirmDeleteProduct()">
                                                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="11" class="text-center">
                                            <div style="padding: 40px; color: var(--muted);">
                                                <i class="fas fa-box-open" style="font-size: 48px; margin-bottom: 16px;"></i>
                                                <h3>No products found</h3>
                                                <p>Try adjusting your filters or add a new product.</p>
                                                <a href="product_add.php" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Add Your First Product
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                                   class="page-link">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                   class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                                   class="page-link">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDeleteProduct() {
            return confirm('Are you sure you want to delete this product? This will also delete all variations, images, and associated data. This action cannot be undone.');
        }
        
        // Auto-submit form when filters change
        document.getElementById('category').addEventListener('change', function() {
            this.form.submit();
        });
        
        document.getElementById('status').addEventListener('change', function() {
            this.form.submit();
        });
        
        document.getElementById('product_type').addEventListener('change', function() {
            this.form.submit();
        });
    </script>

    <style>
        .export-import-section {
            background: rgba(212, 175, 55, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: var(--radius-md);
            padding: 1.5rem;
        }
        
        .export-import-form {
            display: flex;
            gap: 2rem;
            align-items: flex-end;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        
        .export-import-form .form-group {
            flex: 1;
            min-width: 200px;
            margin-bottom: 0;
        }
        
        .import-notice {
            background: rgba(0, 123, 255, 0.1);
            border: 1px solid rgba(0, 123, 255, 0.2);
            border-radius: var(--radius-sm);
            padding: 1rem;
            font-size: 0.9rem;
        }
        
        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            border: 2px solid var(--gold);
        }
        
        .stock-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            min-width: 40px;
            text-align: center;
        }
        
        .stock-badge.in-stock {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .stock-badge.low-stock {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .stock-badge.out-of-stock {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.25rem;
            flex-wrap: wrap;
        }
        
        .action-buttons .btn-sm {
            padding: 0.375rem 0.5rem;
            font-size: 0.75rem;
            min-width: auto;
        }
        
        .filter-form .form-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-actions {
            text-align: right;
            padding-top: 1rem;
            border-top: 1px solid rgba(0,0,0,0.1);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        
        .page-link {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            text-decoration: none;
            color: var(--black);
            border-radius: var(--radius-sm);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .page-link:hover {
            background: var(--gold);
            color: var(--black);
            border-color: var(--gold);
        }
        
        .page-link.active {
            background: var(--gradient-gold);
            color: var(--black);
            border-color: var(--gold);
            font-weight: 600;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-muted {
            color: var(--muted);
        }
        
        .text-gold {
            color: var(--gold);
        }
        
        .inline-form {
            display: inline;
        }
        
        code {
            background: #f8f9fa;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-family: monospace;
            font-size: 0.85rem;
        }
        
        @media (max-width: 768px) {
            .export-import-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .filter-form .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>