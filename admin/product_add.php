<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/csrf.php';

// Check authentication and permissions
$auth = new Auth();
if (!$auth->isLoggedIn() || !$auth->hasPermission('manage_products')) {
    header('Location: login.php');
    exit;
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

if ($_POST) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!CSRF::validateToken($csrf_token)) {
        $error = "Invalid CSRF token";
    } else {
        try {
            $db->beginTransaction();

            // Basic product information
            $sku = $_POST['sku'] ?? '';
            $name = $_POST['name'] ?? '';
            $slug = $_POST['slug'] ?? '';
            $short_desc = $_POST['short_desc'] ?? '';
            $long_desc = $_POST['long_desc'] ?? '';
            $category_id = $_POST['category_id'] ?? '';
            $brand = $_POST['brand'] ?? '';
            $tags = $_POST['tags'] ?? '';
            $product_type = $_POST['product_type'] ?? 'variable';

            // Convert tags string to array
            $tags_array = [];
            if (!empty($tags)) {
                $tags_array = array_map('trim', explode(',', $tags));
                $tags_array = array_filter($tags_array); // Remove empty values
            }
            
            // Generate slug if not provided
            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            }
            
            // Check if SKU or slug already exists
            $check_query = "SELECT id FROM products WHERE sku = :sku OR slug = :slug";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(':sku', $sku);
            $check_stmt->bindParam(':slug', $slug);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                throw new Exception("SKU or slug already exists");
            }
            
            // Insert product
            $product_query = "INSERT INTO products 
                            (sku, name, slug, short_desc, long_desc, category_id, brand, created_by_user_id) 
                            VALUES (:sku, :name, :slug, :short_desc, :long_desc, :category_id, :brand, :user_id)";
            
            $product_stmt = $db->prepare($product_query);
            $product_stmt->bindParam(':sku', $sku);
            $product_stmt->bindParam(':name', $name);
            $product_stmt->bindParam(':slug', $slug);
            $product_stmt->bindParam(':short_desc', $short_desc);
            $product_stmt->bindParam(':long_desc', $long_desc);
            $product_stmt->bindParam(':category_id', $category_id);
            $product_stmt->bindParam(':brand', $brand);
            $product_stmt->bindParam(':user_id', $_SESSION['user_id']);
            
            if (!$product_stmt->execute()) {
                throw new Exception("Failed to insert product");
            }
            
            $product_id = $db->lastInsertId();
            
            // Handle tags
            if (!empty($tags_array)) {
                $tag_query = "INSERT INTO product_tags (product_id, tag) VALUES (:product_id, :tag)";
                $tag_stmt = $db->prepare($tag_query);
                
                foreach ($tags_array as $tag) {
                    if (!empty($tag)) {
                        $tag_stmt->bindValue(':product_id', $product_id);
                        $tag_stmt->bindValue(':tag', $tag);
                        $tag_stmt->execute();
                    }
                }
            }
            
            // Handle variations based on product type
            if ($product_type === 'variable') {
                // Existing variations code for variable products
                $variations = $_POST['variations'] ?? [];
                if (!empty($variations) && is_array($variations)) {
                    $variation_query = "INSERT INTO product_variations 
                                      (product_id, sku, price, sale_price, stock, weight, sku_attributes_json, is_default) 
                                      VALUES (:product_id, :sku, :price, :sale_price, :stock, :weight, :attributes, :is_default)";
                    
                    foreach ($variations as $index => $variation_data) {
                        if (!empty($variation_data['sku']) && !empty($variation_data['price'])) {
                            $variation_stmt = $db->prepare($variation_query);
                            
                            $is_default = ($index == 0) ? 1 : 0;
                            $attributes_json = json_encode($variation_data['attributes'] ?? []);
                            
                            // Use bindValue instead of bindParam to avoid reference issues
                            $variation_stmt->bindValue(':product_id', $product_id);
                            $variation_stmt->bindValue(':sku', $variation_data['sku']);
                            $variation_stmt->bindValue(':price', $variation_data['price']);
                            $variation_stmt->bindValue(':sale_price', $variation_data['sale_price'] ?? null);
                            $variation_stmt->bindValue(':stock', $variation_data['stock']);
                            $variation_stmt->bindValue(':weight', $variation_data['weight'] ?? 0);
                            $variation_stmt->bindValue(':attributes', $attributes_json);
                            $variation_stmt->bindValue(':is_default', $is_default);
                            
                            if ($variation_stmt->execute()) {
                                $variation_id = $db->lastInsertId();
                                
                                // Handle variation images
                                if (!empty($_FILES['variation_images']['name'][$index])) {
                                    $upload_dir = UPLOAD_PATH . 'products/' . $product_id . '/variations/' . $variation_id . '/';
                                    if (!is_dir($upload_dir)) {
                                        mkdir($upload_dir, 0755, true);
                                    }
                                    
                                    $variation_image_file = [
                                        'name' => $_FILES['variation_images']['name'][$index],
                                        'type' => $_FILES['variation_images']['type'][$index],
                                        'tmp_name' => $_FILES['variation_images']['tmp_name'][$index],
                                        'error' => $_FILES['variation_images']['error'][$index],
                                        'size' => $_FILES['variation_images']['size'][$index]
                                    ];
                                    
                                    $variation_image = handleFileUpload($variation_image_file, $upload_dir);
                                    if ($variation_image) {
                                        $image_query = "INSERT INTO variation_images (variation_id, file_path) VALUES (:variation_id, :file_path)";
                                        $image_stmt = $db->prepare($image_query);
                                        $image_stmt->bindValue(':variation_id', $variation_id);
                                        $image_stmt->bindValue(':file_path', $variation_image);
                                        $image_stmt->execute();
                                    }
                                }
                            } else {
                                throw new Exception("Failed to insert variation");
                            }
                        }
                    }
                }
            } else {
                // Simple product - create one variation with simple product data
                $simple_price = $_POST['simple_price'] ?? 0;
                $simple_sale_price = $_POST['simple_sale_price'] ?? null;
                $simple_stock = $_POST['simple_stock'] ?? 0;
                $simple_weight = $_POST['simple_weight'] ?? 0;
                
                $variation_query = "INSERT INTO product_variations 
                                  (product_id, sku, price, sale_price, stock, weight, sku_attributes_json, is_default) 
                                  VALUES (:product_id, :sku, :price, :sale_price, :stock, :weight, :attributes, 1)";
                
                $variation_stmt = $db->prepare($variation_query);
                $attributes_json = json_encode([]); // Empty attributes for simple products
                
                $variation_stmt->bindValue(':product_id', $product_id);
                $variation_stmt->bindValue(':sku', $sku . '-SIMPLE');
                $variation_stmt->bindValue(':price', $simple_price);
                $variation_stmt->bindValue(':sale_price', $simple_sale_price);
                $variation_stmt->bindValue(':stock', $simple_stock);
                $variation_stmt->bindValue(':weight', $simple_weight);
                $variation_stmt->bindValue(':attributes', $attributes_json);
                
                if (!$variation_stmt->execute()) {
                    throw new Exception("Failed to create simple product variation");
                }
            }
            
            // Handle main product image (same for both product types)
            if (!empty($_FILES['main_image']['name'])) {
                $upload_dir = UPLOAD_PATH . 'products/' . $product_id . '/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $main_image = handleFileUpload($_FILES['main_image'], $upload_dir);
                if ($main_image) {
                    $media_query = "INSERT INTO product_media (product_id, file_path, alt_text, sort_order) 
                                   VALUES (:product_id, :file_path, :alt_text, 0)";
                    $media_stmt = $db->prepare($media_query);
                    $media_stmt->bindValue(':product_id', $product_id);
                    $media_stmt->bindValue(':file_path', $main_image);
                    $media_stmt->bindValue(':alt_text', $name);
                    $media_stmt->execute();
                }
            }
            
            // Handle gallery images (same for both product types)
            if (!empty($_FILES['gallery_images']['name'][0])) {
                $upload_dir = UPLOAD_PATH . 'products/' . $product_id . '/gallery/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $gallery_query = "INSERT INTO product_media (product_id, file_path, alt_text, sort_order) 
                                 VALUES (:product_id, :file_path, :alt_text, :sort_order)";
                $gallery_stmt = $db->prepare($gallery_query);
                $sort_order = 1;
                
                foreach ($_FILES['gallery_images']['name'] as $index => $filename) {
                    if (!empty($filename)) {
                        $file = [
                            'name' => $_FILES['gallery_images']['name'][$index],
                            'type' => $_FILES['gallery_images']['type'][$index],
                            'tmp_name' => $_FILES['gallery_images']['tmp_name'][$index],
                            'error' => $_FILES['gallery_images']['error'][$index],
                            'size' => $_FILES['gallery_images']['size'][$index]
                        ];
                        
                        $gallery_image = handleFileUpload($file, $upload_dir);
                        if ($gallery_image) {
                            $gallery_stmt->bindValue(':product_id', $product_id);
                            $gallery_stmt->bindValue(':file_path', $gallery_image);
                            $gallery_stmt->bindValue(':alt_text', $name . ' - Image ' . $sort_order);
                            $gallery_stmt->bindValue(':sort_order', $sort_order);
                            $gallery_stmt->execute();
                            $sort_order++;
                        }
                    }
                }
            }
            
            $db->commit();
            $success = "Product added successfully! <a href='product_edit.php?id=" . $product_id . "'>Edit product</a>";
            
        } catch (Exception $e) {
            $db->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    }
}

// File upload handler function
function handleFileUpload($file, $upload_dir) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Return path relative to website root (for frontend access)
        $relative_path = str_replace(UPLOAD_PATH, 'assets/uploads/', $filepath);
        return $relative_path;
    }
    
    return false;
}

// Get categories for dropdown
$categories_query = "SELECT id, name, parent_id FROM categories ORDER BY name";
$categories_stmt = $db->prepare($categories_query);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get attributes for variations
$attributes_query = "SELECT a.id, a.name, a.type, av.id as value_id, av.value, av.slug 
                    FROM attributes a 
                    LEFT JOIN attribute_values av ON a.id = av.attribute_id 
                    ORDER BY a.name, av.value";
$attributes_stmt = $db->prepare($attributes_query);
$attributes_stmt->execute();
$attributes = [];
while ($row = $attributes_stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!isset($attributes[$row['name']])) {
        $attributes[$row['name']] = [
            'id' => $row['id'],
            'type' => $row['type'],
            'values' => []
        ];
    }
    if ($row['value_id']) {
        $attributes[$row['name']]['values'][] = [
            'id' => $row['value_id'],
            'value' => $row['value'],
            'slug' => $row['slug']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - LuxePerfume Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <header class="admin-header">
                <h1><i class="fas fa-plus-circle"></i> Add New Product</h1>
                <div class="header-actions">
                    <a href="products.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                    <a href="attributes.php" class="btn btn-primary">
                        <i class="fas fa-tags"></i> Manage Attributes
                    </a>
                </div>
            </header>

            <main class="admin-main">
                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" class="product-form">
                    <?php echo CSRF::getTokenField(); ?>
                    <div class="card">
    <div class="card-header">
        <h2><i class="fas fa-cube"></i> Product Type</h2>
    </div>
    <div class="card-body">
        <div class="form-grid">
            <div class="form-group">
                <label for="product_type">Product Type *</label>
                <select name="product_type" id="product_type" required onchange="toggleVariationsSection()">
                    <option value="simple">Simple Product (No Variations)</option>
                    <option value="variable" selected>Variable Product (With Variations)</option>
                </select>
            </div>
            
            <!-- Simple Product Fields (hidden by default) -->
            <div id="simple_product_fields" style="display: none;" class="form-group full-width">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="simple_price">Price ($) *</label>
                        <input type="number" name="simple_price" id="simple_price" step="0.01" min="0" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="simple_sale_price">Sale Price ($)</label>
                        <input type="number" name="simple_sale_price" id="simple_sale_price" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="simple_stock">Stock Quantity *</label>
                        <input type="number" name="simple_stock" id="simple_stock" min="0" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="simple_weight">Weight (g)</label>
                        <input type="number" name="simple_weight" id="simple_weight" step="0.01" min="0" value="0">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-info-circle"></i> Basic Information</h2>
                        </div>
                        <div class="card-body">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="sku">SKU *</label>
                                    <input type="text" name="sku" id="sku" required 
                                           placeholder="e.g., PERF001" 
                                           pattern="[A-Z0-9-]+" 
                                           title="Uppercase letters, numbers, and hyphens only">
                                </div>
                                
                                <div class="form-group">
                                    <label for="name">Product Name *</label>
                                    <input type="text" name="name" id="name" required 
                                           placeholder="e.g., Noir Essence">
                                </div>
                                
                                <div class="form-group">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug" 
                                           placeholder="auto-generated">
                                    <small>Leave empty to auto-generate from product name</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="brand">Brand *</label>
                                    <input type="text" name="brand" id="brand" required 
                                           placeholder="e.g., Luxe" value="ATK">
                                </div>
                                
                                <div class="form-group">
                                    <label for="category_id">Category *</label>
                                    <select name="category_id" id="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group full-width">
                                    <label for="short_desc">Short Description *</label>
                                    <textarea name="short_desc" id="short_desc" required 
                                              placeholder="Brief description for product listings" 
                                              rows="3"></textarea>
                                </div>
                                
                                <div class="form-group full-width">
                                    <label for="long_desc">Long Description</label>
                                    <textarea name="long_desc" id="long_desc" 
                                              placeholder="Detailed product description" 
                                              rows="6"></textarea>
                                </div>
                                
                                <div class="form-group full-width">
                                    <label for="tags">Tags</label>
                                    <input type="text" name="tags" id="tags" 
                                           placeholder="e.g., men, luxury, evening (comma separated)">
                                    <small>Separate tags with commas</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-images"></i> Product Images</h2>
                        </div>
                        <div class="card-body">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="main_image">Main Image *</label>
                                    <input type="file" name="main_image" id="main_image" 
                                           accept="image/*" required>
                                    <small>Recommended: 800x800px, JPEG/PNG/WEBP</small>
                                    <div id="main-image-preview" class="image-preview"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="gallery_images">Gallery Images</label>
                                    <input type="file" name="gallery_images[]" id="gallery_images" 
                                           multiple accept="image/*">
                                    <small>Hold Ctrl to select multiple images</small>
                                    <div id="gallery-preview" class="gallery-preview"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                   <div class="card" id="variations_section">
    <div class="card-header">
        <h2><i class="fas fa-cubes"></i> Product Variations</h2>
        <button type="button" class="btn btn-primary" onclick="addVariation()" id="add_variation_btn">
            <i class="fas fa-plus"></i> Add Variation
        </button>
    </div>
    <div class="card-body">
        <div id="variations-container">
            <!-- Variations will be added here dynamically -->
            <div class="variation-item" data-index="0">
                <div class="variation-header">
                    <h3>Variation #1 <span class="badge badge-primary">Default</span></h3>
                    <button type="button" class="btn btn-danger btn-sm remove-variation-btn" 
                            onclick="removeVariation(0)" style="display: none;">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label>SKU *</label>
                                            <input type="text" name="variations[0][sku]" required 
                                                   placeholder="e.g., PERF001-50ML-EDT">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Price ($) *</label>
                                            <input type="number" name="variations[0][price]" 
                                                   step="0.01" min="0" required value="0">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Sale Price ($)</label>
                                            <input type="number" name="variations[0][sale_price]" 
                                                   step="0.01" min="0" value="0">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Stock Quantity *</label>
                                            <input type="number" name="variations[0][stock]" 
                                                   min="0" required value="0">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Weight (g)</label>
                                            <input type="number" name="variations[0][weight]" 
                                                   step="0.01" min="0" value="0">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Variation Image</label>
                                            <input type="file" name="variation_images[0]" 
                                                   accept="image/*">
                                            <div class="variation-image-preview" data-index="0"></div>
                                        </div>
                                        
                                        <?php foreach ($attributes as $attr_name => $attr_data): ?>
                                            <div class="form-group">
                                                <label><?php echo htmlspecialchars($attr_name); ?></label>
                                                <?php if ($attr_data['type'] === 'select' && !empty($attr_data['values'])): ?>
                                                    <select name="variations[0][attributes][<?php echo htmlspecialchars($attr_name); ?>]">
                                                        <option value="">Select <?php echo htmlspecialchars($attr_name); ?></option>
                                                        <?php foreach ($attr_data['values'] as $value): ?>
                                                            <option value="<?php echo htmlspecialchars($value['value']); ?>">
                                                                <?php echo htmlspecialchars($value['value']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <input type="text" name="variations[0][attributes][<?php echo htmlspecialchars($attr_name); ?>]" 
                                                           placeholder="Enter <?php echo htmlspecialchars($attr_name); ?>">
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Add Product
                        </button>
                        <a href="products.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        let variationCount = 1;
        
        function addVariation() {
            const container = document.getElementById('variations-container');
            const newVariation = document.createElement('div');
            newVariation.className = 'variation-item';
            newVariation.setAttribute('data-index', variationCount);
            
            // Generate attribute HTML
            let attributesHtml = '';
            <?php foreach ($attributes as $attr_name => $attr_data): ?>
                attributesHtml += `
                    <div class="form-group">
                        <label><?php echo htmlspecialchars($attr_name); ?></label>
                        <?php if ($attr_data['type'] === 'select' && !empty($attr_data['values'])): ?>
                            <select name="variations[${variationCount}][attributes][<?php echo htmlspecialchars($attr_name); ?>]">
                                <option value="">Select <?php echo htmlspecialchars($attr_name); ?></option>
                                <?php foreach ($attr_data['values'] as $value): ?>
                                    <option value="<?php echo htmlspecialchars($value['value']); ?>">
                                        <?php echo htmlspecialchars($value['value']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" name="variations[${variationCount}][attributes][<?php echo htmlspecialchars($attr_name); ?>]" 
                                   placeholder="Enter <?php echo htmlspecialchars($attr_name); ?>">
                        <?php endif; ?>
                    </div>
                `;
            <?php endforeach; ?>
            
            newVariation.innerHTML = `
                <div class="variation-header">
                    <h3>Variation #${variationCount + 1}</h3>
                    <button type="button" class="btn btn-danger btn-sm remove-variation-btn" onclick="removeVariation(${variationCount})">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>SKU *</label>
                        <input type="text" name="variations[${variationCount}][sku]" required 
                               placeholder="e.g., PERF001-50ML-EDT">
                    </div>
                    
                    <div class="form-group">
                        <label>Price ($) *</label>
                        <input type="number" name="variations[${variationCount}][price]" 
                               step="0.01" min="0" required value="0">
                    </div>
                    
                    <div class="form-group">
                        <label>Sale Price ($)</label>
                        <input type="number" name="variations[${variationCount}][sale_price]" 
                               step="0.01" min="0" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="variations[${variationCount}][stock]" 
                               min="0" required value="0">
                    </div>
                    
                    <div class="form-group">
                        <label>Weight (g)</label>
                        <input type="number" name="variations[${variationCount}][weight]" 
                               step="0.01" min="0" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label>Variation Image</label>
                        <input type="file" name="variation_images[${variationCount}]" 
                               accept="image/*" onchange="previewVariationImage(${variationCount}, this)">
                        <div class="variation-image-preview" data-index="${variationCount}"></div>
                    </div>
                    
                    ${attributesHtml}
                </div>
                <hr>
            `;
            
            container.appendChild(newVariation);
            variationCount++;
            
            // Show remove buttons on all variations when we have more than one
            if (variationCount > 1) {
                document.querySelectorAll('.remove-variation-btn').forEach(btn => {
                    btn.style.display = 'inline-block';
                });
            }
        }
        
        function removeVariation(index) {
            const variation = document.querySelector(`.variation-item[data-index="${index}"]`);
            if (variation && variationCount > 1) {
                variation.remove();
                variationCount--;
                
                // Re-index remaining variations
                const variations = document.querySelectorAll('.variation-item');
                variations.forEach((variation, newIndex) => {
                    variation.setAttribute('data-index', newIndex);
                    variation.querySelector('h3').innerHTML = `Variation #${newIndex + 1} ${newIndex === 0 ? '<span class="badge badge-primary">Default</span>' : ''}`;
                    
                    // Update all input names
                    const inputs = variation.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        const name = input.getAttribute('name');
                        if (name) {
                            input.setAttribute('name', name.replace(/variations\[\d+\]/, `variations[${newIndex}]`));
                        }
                    });
                    
                    // Update file input
                    const fileInput = variation.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.setAttribute('name', `variation_images[${newIndex}]`);
                        fileInput.setAttribute('onchange', `previewVariationImage(${newIndex}, this)`);
                    }
                    
                    // Update preview container
                    const preview = variation.querySelector('.variation-image-preview');
                    if (preview) {
                        preview.setAttribute('data-index', newIndex);
                    }
                });
                
                // Hide remove buttons if only one variation remains
                if (variationCount === 1) {
                    document.querySelectorAll('.remove-variation-btn').forEach(btn => {
                        btn.style.display = 'none';
                    });
                }
            }
        }
        
        function previewVariationImage(index, input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector(`.variation-image-preview[data-index="${index}"]`);
                    preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100px; max-height: 100px; margin-top: 10px; border: 2px solid var(--gold); border-radius: 5px;">`;
                };
                reader.readAsDataURL(file);
            }
        }
        
        // Auto-generate slug from product name
        document.getElementById('name').addEventListener('input', function() {
            const slugField = document.getElementById('slug');
            if (!slugField.value) {
                const slug = this.value.toLowerCase()
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
                slugField.value = slug;
            }
        });
        
        // Auto-generate SKU variations
        document.getElementById('sku').addEventListener('input', function() {
            const baseSku = this.value;
            document.querySelectorAll('input[name^="variations"]').forEach(input => {
                if (input.name.includes('[sku]') && !input.value) {
                    input.value = baseSku + '-';
                }
            });
        });
        
        // Image preview functionality
        document.getElementById('main_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('main-image-preview');
                    preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; max-height: 200px; margin-top: 10px; border: 2px solid var(--gold); border-radius: 5px;">`;
                };
                reader.readAsDataURL(file);
            }
        });
        
        document.getElementById('gallery_images').addEventListener('change', function(e) {
            const preview = document.getElementById('gallery-preview');
            preview.innerHTML = '';
            
            Array.from(e.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100px';
                    img.style.maxHeight = '100px';
                    img.style.margin = '5px';
                    img.style.border = '2px solid var(--gold)';
                    img.style.borderRadius = '5px';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
        // Toggle between simple and variable products
function toggleVariationsSection() {
    const productType = document.getElementById('product_type').value;
    const variationsSection = document.getElementById('variations_section');
    const simpleFields = document.getElementById('simple_product_fields');
    const addVariationBtn = document.getElementById('add_variation_btn');
    
    if (productType === 'simple') {
        variationsSection.style.display = 'none';
        simpleFields.style.display = 'block';
        addVariationBtn.style.display = 'none';
        
        // Auto-fill simple product fields from first variation
        const firstVariation = document.querySelector('input[name="variations[0][price]"]');
        if (firstVariation) {
            document.getElementById('simple_price').value = firstVariation.value || '0';
        }
    } else {
        variationsSection.style.display = 'block';
        simpleFields.style.display = 'none';
        addVariationBtn.style.display = 'inline-flex';
    }
}

// Call on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleVariationsSection();
});
    </script>

    <style>
        .variation-item {
            background: #f9f9f9;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: var(--radius-md);
            border: 1px solid #e0e0e0;
        }
        
        .variation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .variation-header h3 {
            margin: 0;
            font-family: 'Cinzel', serif;
            color: var(--black);
        }
        
        .image-preview img, .gallery-preview img, .variation-image-preview img {
            border: 2px solid var(--gold);
            border-radius: var(--radius-sm);
        }
        
        .gallery-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        
        .form-actions {
            text-align: center;
            padding: 2rem;
            border-top: 1px solid #e0e0e0;
            margin-top: 2rem;
        }
        
        .product-form .form-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }
        
        .remove-variation-btn {
            display: none;
        }
        
        .variation-item:first-child .remove-variation-btn {
            display: none !important;
        }
    </style>
</body>
</html>