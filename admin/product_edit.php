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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('products.php');
}

$productId = (int)$_GET['id'];
$database = new Database();
$conn = $database->getConnection();

// Get product details
$productQuery = "SELECT * FROM products WHERE id = ?";
$productStmt = $conn->prepare($productQuery);
$productStmt->bind_param('i', $productId);
$productStmt->execute();
$product = $productStmt->get_result()->fetch_assoc();

if (!$product) {
    redirect('products.php');
}

// Get categories and attributes
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$attributes = $conn->query("SELECT id, name FROM attributes ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Get product variations
$variationsQuery = "SELECT * FROM product_variations WHERE product_id = ? ORDER BY is_default DESC";
$variationsStmt = $conn->prepare($variationsQuery);
$variationsStmt->bind_param('i', $productId);
$variationsStmt->execute();
$variations = $variationsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get product images
$imagesQuery = "SELECT * FROM product_media WHERE product_id = ? ORDER BY sort_order";
$imagesStmt = $conn->prepare($imagesQuery);
$imagesStmt->bind_param('i', $productId);
$imagesStmt->execute();
$images = $imagesStmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
        try {
            $conn->begin_transaction();
            
            // Update product
            $updateQuery = "UPDATE products SET sku = ?, name = ?, slug = ?, short_desc = ?, 
                           long_desc = ?, category_id = ?, brand = ?, is_active = ?, updated_at = NOW()
                           WHERE id = ?";
            
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('sssssisii', $sku, $name, $slug, $shortDesc, $longDesc, 
                                   $categoryId, $brand, $isActive, $productId);
            
            if ($updateStmt->execute()) {
                // Handle variations update
                if (isset($_POST['variations'])) {
                    foreach ($_POST['variations'] as $variationId => $variation) {
                        $variationSku = sanitizeInput($variation['sku']);
                        $price = (float)$variation['price'];
                        $salePrice = !empty($variation['sale_price']) ? (float)$variation['sale_price'] : null;
                        $stock = (int)$variation['stock'];
                        $weight = (float)$variation['weight'];
                        $isDefault = isset($variation['is_default']) ? 1 : 0;
                        
                        if ($variationId > 0) {
                            // Update existing variation
                            $updateVariationQuery = "UPDATE product_variations SET sku = ?, price = ?, 
                                                   sale_price = ?, stock = ?, weight = ?, is_default = ?
                                                   WHERE id = ?";
                            $updateVariationStmt = $conn->prepare($updateVariationQuery);
                            $updateVariationStmt->bind_param('sddidii', $variationSku, $price, $salePrice, 
                                                           $stock, $weight, $isDefault, $variationId);
                            $updateVariationStmt->execute();
                        } else {
                            // Insert new variation
                            $attributesJson = json_encode($variation['attributes'] ?? []);
                            $insertVariationQuery = "INSERT INTO product_variations 
                                                   (product_id, sku, price, sale_price, stock, weight, sku_attributes_json, is_default)
                                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                            $insertVariationStmt = $conn->prepare($insertVariationQuery);
                            $insertVariationStmt->bind_param('isddidsi', $productId, $variationSku, $price, 
                                                           $salePrice, $stock, $weight, $attributesJson, $isDefault);
                            $insertVariationStmt->execute();
                        }
                    }
                }
                
                // Handle image uploads
                if (!empty($_FILES['images']['name'][0])) {
                    $uploadDir = '../assets/uploads/products/';
                    
                    foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
                        if ($_FILES['images']['error'][$index] === UPLOAD_ERR_OK) {
                            $fileName = uniqid() . '_' . basename($_FILES['images']['name'][$index]);
                            $targetFile = $uploadDir . $fileName;
                            
                            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                            
                            if (in_array($imageFileType, $allowedTypes) && move_uploaded_file($tmpName, $targetFile)) {
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
                
                $conn->commit();
                $success = 'Product updated successfully!';
                
                // Refresh product data
                $productStmt->execute();
                $product = $productStmt->get_result()->fetch_assoc();
                
            } else {
                throw new Exception('Failed to update product: ' . $updateStmt->error);
            }
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Error updating product: ' . $e->getMessage();
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
    <title>Edit Product | LuxePerfume Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Edit Product: <?php echo htmlspecialchars($product['name']); ?></h1>
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
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="sku">SKU *</label>
                                    <input type="text" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="slug">Slug *</label>
                                    <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($product['slug']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="category_id">Category *</label>
                                    <select id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>" 
                                                <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="brand">Brand</label>
                                    <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>">
                                </div>
                                
                                <div class="form-group full-width">
                                    <label for="short_desc">Short Description *</label>
                                    <textarea id="short_desc" name="short_desc" rows="3" required><?php echo htmlspecialchars($product['short_desc']); ?></textarea>
                                </div>
                                
                                <div class="form-group full-width">
                                    <label for="long_desc">Long Description</label>
                                    <textarea id="long_desc" name="long_desc" rows="6"><?php echo htmlspecialchars($product['long_desc']); ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                                            <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                                        <span>Active Product</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Variations Tab -->
                        <div class="tab-pane" id="variations">
                            <div id="variations-container">
                                <?php foreach ($variations as $index => $variation): ?>
                                    <div class="variation-item">
                                        <h4>Variation <?php echo $index + 1; ?></h4>
                                        <input type="hidden" name="variations[<?php echo $variation['id']; ?>][id]" value="<?php echo $variation['id']; ?>">
                                        
                                        <div class="form-grid">
                                            <div class="form-group">
                                                <label>SKU *</label>
                                                <input type="text" name="variations[<?php echo $variation['id']; ?>][sku]" 
                                                       value="<?php echo htmlspecialchars($variation['sku']); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Price *</label>
                                                <input type="number" name="variations[<?php echo $variation['id']; ?>][price]" 
                                                       value="<?php echo $variation['price']; ?>" step="0.01" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Sale Price</label>
                                                <input type="number" name="variations[<?php echo $variation['id']; ?>][sale_price]" 
                                                       value="<?php echo $variation['sale_price']; ?>" step="0.01">
                                            </div>
                                            <div class="form-group">
                                                <label>Stock *</label>
                                                <input type="number" name="variations[<?php echo $variation['id']; ?>][stock]" 
                                                       value="<?php echo $variation['stock']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Weight (g)</label>
                                                <input type="number" name="variations[<?php echo $variation['id']; ?>][weight]" 
                                                       value="<?php echo $variation['weight']; ?>" step="0.01">
                                            </div>
                                            <div class="form-group">
                                                <label class="checkbox-label">
                                                    <input type="checkbox" name="variations[<?php echo $variation['id']; ?>][is_default]" value="1" 
                                                        <?php echo $variation['is_default'] ? 'checked' : ''; ?>>
                                                    <span>Default Variation</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" id="add-variation" class="btn btn-secondary">Add Variation</button>
                        </div>
                        
                        <!-- Images Tab -->
                        <div class="tab-pane" id="images">
                            <div class="existing-images">
                                <h4>Current Images</h4>
                                <div class="image-grid">
                                    <?php foreach ($images as $image): ?>
                                        <div class="image-item">
                                            <img src="../assets/uploads/products/<?php echo $image['file_path']; ?>" 
                                                 alt="<?php echo htmlspecialchars($image['alt_text']); ?>">
                                            <div class="image-actions">
                                                <a href="ajax/delete_image.php?id=<?php echo $image['id']; ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Delete this image?')">Delete</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div id="image-uploads">
                                <h4>Add New Images</h4>
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
                                <input type="text" id="meta_title" name="meta_title" 
                                       value="<?php echo htmlspecialchars($product['meta_title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <textarea id="meta_description" name="meta_description" rows="3"><?php echo htmlspecialchars($product['meta_description'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input type="text" id="meta_keywords" name="meta_keywords" 
                                       value="<?php echo htmlspecialchars($product['meta_keywords'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/admin-product.js"></script>
</body>
</html>