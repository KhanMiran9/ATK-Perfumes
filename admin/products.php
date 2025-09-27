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

// Export function
function exportProductsToExcel($conn) {
    // Fetch all products with details
    $query = "SELECT p.*, c.name as category_name, u.name as creator_name,
                     pv.price, pv.sale_price, pv.stock, pv.sku_attributes_json
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              LEFT JOIN users u ON p.created_by_user_id = u.id
              LEFT JOIN product_variations pv ON p.id = pv.product_id AND pv.is_default = 1
              ORDER BY p.id";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set headers for Excel file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="products_export_' . date('Y-m-d') . '.xls"');
    
    // Create Excel content
    echo "ID\tSKU\tName\tCategory\tBrand\tPrice\tSale Price\tStock\tStatus\tCreated At\tDescription\n";
    
    foreach ($products as $product) {
        $status = $product['is_active'] ? 'Active' : 'Inactive';
        $price = $product['price'] ?? 0;
        $sale_price = $product['sale_price'] ?? '';
        $stock = $product['stock'] ?? 0;
        
        // Clean data for Excel
        $name = str_replace(["\t", "\n", "\r"], ' ', $product['name']);
        $description = str_replace(["\t", "\n", "\r"], ' ', strip_tags($product['short_desc']));
        
        echo "{$product['id']}\t{$product['sku']}\t{$name}\t{$product['category_name']}\t{$product['brand']}\t{$price}\t{$sale_price}\t{$stock}\t{$status}\t{$product['created_at']}\t{$description}\n";
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
    
    // Simple CSV parsing (for demonstration)
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        $header = fgetcsv($handle, 1000, "\t");
        $imported = 0;
        $errors = [];
        
        // Skip header row
        $rowNumber = 1;
        
        while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
            $rowNumber++;
            
            if (count($data) < 6) continue; // Skip invalid rows
            
            try {
                // Map Excel columns to database fields
                $productData = [
                    'sku' => $data[1] ?? '',
                    'name' => $data[2] ?? '',
                    'category_id' => getCategoryIdByName($conn, $data[3] ?? ''),
                    'brand' => $data[4] ?? '',
                    'price' => floatval($data[5] ?? 0),
                    'sale_price' => !empty($data[6]) ? floatval($data[6]) : null,
                    'stock' => intval($data[7] ?? 0),
                    'is_active' => ($data[8] ?? 'Active') === 'Active' ? 1 : 0,
                    'short_desc' => $data[10] ?? '',
                    'created_by_user_id' => $_SESSION['user_id'] ?? 1
                ];
                
                // Insert product
                $insertQuery = "INSERT INTO products (sku, name, category_id, brand, short_desc, is_active, created_by_user_id) 
                               VALUES (:sku, :name, :category_id, :brand, :short_desc, :is_active, :created_by_user_id)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->execute($productData);
                
                $productId = $conn->lastInsertId();
                
                // Insert default variation
                $variationQuery = "INSERT INTO product_variations (product_id, price, sale_price, stock, is_default) 
                                  VALUES (:product_id, :price, :sale_price, :stock, 1)";
                $variationStmt = $conn->prepare($variationQuery);
                $variationStmt->execute([
                    'product_id' => $productId,
                    'price' => $productData['price'],
                    'sale_price' => $productData['sale_price'],
                    'stock' => $productData['stock']
                ]);
                
                $imported++;
                
            } catch (Exception $e) {
                $errors[] = "Row $rowNumber: " . $e->getMessage();
            }
        }
        fclose($handle);
        
        return [
            'success' => true,
            'imported' => $imported,
            'errors' => $errors
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to read file'];
}

// Helper function to get category ID by name
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
    <style>
        .export-import-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }
        
        .export-import-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #495057;
        }
        
        .form-group input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
        }
        
        .import-notice {
            background: #e7f3ff;
            border: 1px solid #b3d7ff;
            border-radius: 4px;
            padding: 10px;
            margin-top: 10px;
            font-size: 14px;
        }
        
        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .stock-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .stock-badge.in-stock {
            background: #d4edda;
            color: #155724;
        }
        
        .stock-badge.low-stock {
            background: #fff3cd;
            color: #856404;
        }
        
        .stock-badge.out-of-stock {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-badge.status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-sm {
            padding: 4px 8px;
            font-size: 11px;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #6c757d;
            color: #6c757d;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .pagination {
            display: flex;
            gap: 5px;
            margin-top: 20px;
            justify-content: center;
        }
        
        .page-link {
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #007bff;
            border-radius: 4px;
        }
        
        .page-link.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .filters-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .filter-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-muted {
            color: #6c757d;
        }
        
        .inline-form {
            display: inline;
        }
        
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
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
            
            <!-- Export/Import Section -->
            <div class="export-import-section">
                <h3>Export/Import Products</h3>
                <div class="export-import-form">
                    <form method="POST" class="form-group">
                        <button type="submit" name="export_products" class="btn btn-success">Export to Excel</button>
                    </form>
                    
                    <form method="POST" enctype="multipart/form-data" class="form-group">
                        <label for="import_file">Import from Excel/CSV:</label>
                        <input type="file" name="import_file" accept=".xls,.xlsx,.csv" required>
                        <button type="submit" name="import_products" class="btn btn-primary" style="margin-top: 10px;">Import Products</button>
                    </form>
                </div>
                <div class="import-notice">
                    <strong>Import Format:</strong> Excel/CSV file with columns: ID, SKU, Name, Category, Brand, Price, Sale Price, Stock, Status, Created At, Description
                </div>
                
                <?php if (isset($importResult)): ?>
                    <div class="alert <?php echo $importResult['success'] ? 'alert-success' : 'alert-error'; ?>" style="margin-top: 15px;">
                        <?php if ($importResult['success']): ?>
                            Successfully imported <?php echo $importResult['imported']; ?> products.
                            <?php if (!empty($importResult['errors'])): ?>
                                <br>Errors: <?php echo implode(', ', $importResult['errors']); ?>
                            <?php endif; ?>
                        <?php else: ?>
                            Import failed: <?php echo $importResult['message']; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
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
                                    // Get default variation price and stock using PDO
                                    $variationQuery = "SELECT price, sale_price, stock 
                                                      FROM product_variations 
                                                      WHERE product_id = ? AND is_default = 1 
                                                      LIMIT 1";
                                    $variationStmt = $conn->prepare($variationQuery);
                                    $variationStmt->execute([$product['id']]);
                                    $variation = $variationStmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    $price = $variation ? ($variation['sale_price'] ?: $variation['price']) : 0;
                                    $stock = $variation ? $variation['stock'] : 0;
                                    
                                    // Get product image using PDO
                                    $imageQuery = "SELECT file_path FROM product_media WHERE product_id = ? ORDER BY sort_order LIMIT 1";
                                    $imageStmt = $conn->prepare($imageQuery);
                                    $imageStmt->execute([$product['id']]);
                                    $image = $imageStmt->fetch(PDO::FETCH_ASSOC);
                                    
                                    $imagePath = $image ? '../assets/uploads/' . $image['file_path'] : '../assets/images/pexels-photo-1961791.jpeg';
                                ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td>
                                            <img src="<?php echo $imagePath; ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                 class="product-thumb"
                                                 onerror="this.src='../assets/images/pexels-photo-1961791.jpeg'">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                            <br>
                                            <small class="text-muted">by <?php echo htmlspecialchars($product['creator_name']); ?></small>
                                        </td>
                                        <td><?php echo $product['sku']; ?></td>
                                        <td><?php echo $product['category_name']; ?></td>
                                        <td>₹<?php echo number_format($price, 2); ?></td>
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

    <script>
        // Confirm before delete
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('form[action*="delete_product"]');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>