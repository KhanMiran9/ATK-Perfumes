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

// Get categories and attributes for form
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$attributes = $conn->query("SELECT id, name FROM attributes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $sku = sanitizeInput($_POST['sku']);
    $slug = sanitizeInput($_POST['slug']);
    $shortDesc = sanitizeInput($_POST['short_desc']);
    $longDesc = sanitizeInput($_POST['long_desc']);
    $categoryId = (int)$_POST['category_id'];
    $brand = sanitizeInput($_POST['brand']);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (validateCsrfToken($csrf_token)) {
        // Validate required fields
        if (empty($name) || empty($sku) || empty($slug)) {
            $error = 'Please fill in all required fields';
        } else {
            try {
                $conn->begin_transaction();
                
                // Insert product
                $productQuery = "INSERT INTO products (sku, name, slug, short_desc, long_desc, 
                                    category_id, brand, is_active, created_by_user_id)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $productStmt = $conn->prepare($productQuery);
                $productStmt->bind_param('sssssisii', $sku, $name, $slug, $shortDesc, $longDesc, 
                                        $categoryId, $brand, $isActive, $_SESSION['user_id']);
                
                if ($productStmt->execute()) {
                    $productId = $conn->insert_id;
                    
                    // Handle product variations
                    if (isset($_POST['variations'])) {
                        foreach ($_POST['variations'] as $variation) {
                            $variationSku = sanitizeInput($variation['sku']);
                            $price = (float)$variation['price'];
                            $salePrice = !empty($variation['sale_price']) ? (float)$variation['sale_price'] : null;
                            $stock = (int)$variation['stock'];
                            $weight = (float)$variation['weight'];
                            $isDefault = isset($variation['is_default']) ? 1 : 0;
                            $attributesJson = json_encode($variation['attributes']);
                            
                            $variationQuery = "INSERT INTO product_variations 
                                                (product_id, sku, price, sale_price, stock, weight, sku_attributes_json, is_default)
                                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                            
                            $variationStmt = $conn->prepare($variationQuery);
                            $variationStmt->bind_param('isddidsi', $productId, $variationSku, $price, $salePrice, 
                                                      $stock, $weight, $attributesJson, $isDefault);
                            $variationStmt->execute();
                        }
                    }
                    
                    // Handle image uploads
                    if (!empty($_FILES['images']['name'][0])) {
                        $uploadDir = '../assets/uploads/products/';
                        
                        foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
                            if ($_FILES['images']['error'][$index] === UPLOAD_ERR_OK) {
                                $fileName = uniqid() . '_' . basename($_FILES['images']['name'][$index]);
                                $targetFile = $uploadDir . $fileName;
                                
                                // Validate and move uploaded file
                                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                                
                                if (in_array($imageFileType, $allowedTypes)) {
                                    if (move_uploaded_file($tmpName, $targetFile)) {
                                        $altText = sanitizeInput($_POST['image_alt'][$index] ?? '');
                                        $sortOrder = (int)($_POST['image_order'][$index] ?? 0);
                                        
                                        $imageQuery = "INSERT INTO product_media (product_id, file_path, alt_text, sort_order)
                                                      VALUES (?, ?, ?, ?)";
                                        $imageStmt = $conn->prepare($imageQuery);
                                        $imageStmt->bind_param('issi', $productId, $fileName, $altText, $sortOrder);
                                        $imageStmt->execute();
                                    }
                                }
                            }
                        }
                    }
                    
                    $conn->commit();
                    $success = 'Product added successfully!';
                    header('Refresh: 2; URL=products.php');
                } else {
                    throw new Exception('Failed to add product: ' . $productStmt->error);
                }
                
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'Error adding product: ' . $e->getMessage();
            }
        }
    } else {
        $error = 'Invalid CSRF token';
    }
}

$csrf_token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | LuxePerfume Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Add New Product</h1>
                <a href="products.php" class="btn btn-secondary">Back to Products</a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="product-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-tabs">
                    <div class="tab-headers">
                        <button type="button" class="tab-header active" data-tab="basic">Basic Info</button>
                        <button type="button" class="tab-header" data-tab="variations">Variations</button>
                        <button type="button" class="tab-header" data-tab="images">Images</button>
                        <button type="button" class="tab-header" data-tab="seo">SEO</button>
                    </div>
                    
                    <div class="tab-content">
                        <!-- Basic Info Tab -->
                        <div class="tab-pane active" id="basic">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="name">Product Name *</label>
                                    <input type="text" id="name" name="name" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="sku">SKU *</label>
                                    <input type="text" id="sku" name="sku" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="slug">Slug *</label>
                                    <input type="text" id="slug" name="slug" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="category_id">Category *</label>
                                    <select id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="brand">Brand</label>
                                    <input type="text" id="brand" name="brand">
                                </div>
                                
                                <div class="form-group full-width">
                                    <label for="short_desc">Short Description *</label>
                                    <textarea id="short_desc" name="short_desc" rows="3" required></textarea>
                                </div>
                                
                                <div class="form-group full-width">
                                    <label for="long_desc">Long Description</label>
                                    <textarea id="long_desc" name="long_desc" rows="6"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                                        <span>Active Product</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Variations Tab -->
                        <div class="tab-pane" id="variations">
                            <div id="variations-container">
                                <div class="variation-item">
                                    <h4>Default Variation</h4>
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label>SKU *</label>
                                            <input type="text" name="variations[0][sku]" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Price *</label>
                                            <input type="number" name="variations[0][price]" step="0.01" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Sale Price</label>
                                            <input type="number" name="variations[0][sale_price]" step="0.01">
                                        </div>
                                        <div class="form-group">
                                            <label>Stock *</label>
                                            <input type="number" name="variations[0][stock]" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Weight (g)</label>
                                            <input type="number" name="variations[0][weight]" step="0.01">
                                        </div>
                                        <div class="form-group">
                                            <label class="checkbox-label">
                                                <input type="checkbox" name="variations[0][is_default]" value="1" checked>
                                                <span>Default Variation</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Attribute selection would go here -->
                                </div>
                            </div>
                            
                            <button type="button" id="add-variation" class="btn btn-secondary">Add Variation</button>
                        </div>
                        
                        <!-- Images Tab -->
                        <div class="tab-pane" id="images">
                            <div id="image-uploads">
                                <div class="image-upload-item">
                                    <div class="form-group">
                                        <label>Image</label>
                                        <input type="file" name="images[]" accept="image/*">
                                    </div>
                                    <div class="form-group">
                                        <label>Alt Text</label>
                                        <input type="text" name="image_alt[]">
                                    </div>
                                    <div class="form-group">
                                        <label>Sort Order</label>
                                        <input type="number" name="image_order[]" value="0">
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-image" class="btn btn-secondary">Add Another Image</button>
                        </div>
                        
                        <!-- SEO Tab -->
                        <div class="tab-pane" id="seo">
                            <div class="form-group">
                                <label for="meta_title">Meta Title</label>
                                <input type="text" id="meta_title" name="meta_title">
                            </div>
                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <textarea id="meta_description" name="meta_description" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input type="text" id="meta_keywords" name="meta_keywords">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Product</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/admin-product.js"></script>
</body>
</html>