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

// Handle form submissions
if ($_POST) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!CSRF::validateToken($csrf_token)) {
        $error = "Invalid CSRF token";
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add_variation') {
            // Add new variation
            $product_id = $_POST['product_id'] ?? '';
            $sku = $_POST['sku'] ?? '';
            $price = $_POST['price'] ?? '';
            $sale_price = $_POST['sale_price'] ?? '';
            $stock = $_POST['stock'] ?? '';
            $weight = $_POST['weight'] ?? '';
            $attributes = $_POST['attributes'] ?? [];
            
            if ($product_id && $sku && $price) {
                $sku_attributes_json = json_encode($attributes);
                
                $query = "INSERT INTO product_variations 
                         (product_id, sku, price, sale_price, stock, weight, sku_attributes_json) 
                         VALUES (:product_id, :sku, :price, :sale_price, :stock, :weight, :attributes)";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':product_id', $product_id);
                $stmt->bindParam(':sku', $sku);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':sale_price', $sale_price);
                $stmt->bindParam(':stock', $stock);
                $stmt->bindParam(':weight', $weight);
                $stmt->bindParam(':attributes', $sku_attributes_json);
                
                if ($stmt->execute()) {
                    $success = "Variation added successfully";
                } else {
                    $error = "Failed to add variation";
                }
            }
        } elseif ($action === 'update_variation') {
            // Update existing variation
            $variation_id = $_POST['variation_id'] ?? '';
            $sku = $_POST['sku'] ?? '';
            $price = $_POST['price'] ?? '';
            $sale_price = $_POST['sale_price'] ?? '';
            $stock = $_POST['stock'] ?? '';
            $weight = $_POST['weight'] ?? '';
            
            if ($variation_id) {
                $query = "UPDATE product_variations SET 
                         sku = :sku, price = :price, sale_price = :sale_price, 
                         stock = :stock, weight = :weight 
                         WHERE id = :id";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':sku', $sku);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':sale_price', $sale_price);
                $stmt->bindParam(':stock', $stock);
                $stmt->bindParam(':weight', $weight);
                $stmt->bindParam(':id', $variation_id);
                
                if ($stmt->execute()) {
                    $success = "Variation updated successfully";
                } else {
                    $error = "Failed to update variation";
                }
            }
        } elseif ($action === 'delete_variation') {
            // Delete variation
            $variation_id = $_POST['variation_id'] ?? '';
            
            if ($variation_id) {
                $query = "DELETE FROM product_variations WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $variation_id);
                
                if ($stmt->execute()) {
                    $success = "Variation deleted successfully";
                } else {
                    $error = "Failed to delete variation";
                }
            }
        } elseif ($action === 'set_default') {
            // Set as default variation
            $variation_id = $_POST['variation_id'] ?? '';
            $product_id = $_POST['product_id'] ?? '';
            
            if ($variation_id && $product_id) {
                // Reset all variations for this product
                $query = "UPDATE product_variations SET is_default = 0 WHERE product_id = :product_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':product_id', $product_id);
                $stmt->execute();
                
                // Set the selected variation as default
                $query = "UPDATE product_variations SET is_default = 1 WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $variation_id);
                
                if ($stmt->execute()) {
                    $success = "Default variation set successfully";
                }
            }
        }
    }
}

// Get products for dropdown
$products_query = "SELECT id, name, sku FROM products WHERE is_active = 1 ORDER BY name";
$products_stmt = $db->prepare($products_query);
$products_stmt->execute();
$products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get attributes for variations
$attributes_query = "SELECT a.id, a.name, a.type, av.id as value_id, av.value, av.slug 
                    FROM attributes a 
                    LEFT JOIN attribute_values av ON a.id = av.attribute_id 
                    ORDER BY a.name, av.value";
$attributes_stmt = $db->prepare($attributes_query);
$attributes_stmt->execute();
$attributes = [];
while ($row = $attributes_stmt->fetch(PDO::FETCH_ASSOC)) {
    $attributes[$row['name']]['type'] = $row['type'];
    $attributes[$row['name']]['values'][] = $row;
}

// Get variations with product info
$variations_query = "SELECT pv.*, p.name as product_name, p.sku as product_sku 
                    FROM product_variations pv 
                    JOIN products p ON pv.product_id = p.id 
                    ORDER BY p.name, pv.sku";
$variations_stmt = $db->prepare($variations_query);
$variations_stmt->execute();
$variations = $variations_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get selected product variations if product is selected
$selected_product_id = $_GET['product_id'] ?? ($_POST['product_id'] ?? '');
$product_variations = [];
if ($selected_product_id) {
    $product_var_query = "SELECT pv.*, p.name as product_name 
                         FROM product_variations pv 
                         JOIN products p ON pv.product_id = p.id 
                         WHERE pv.product_id = :product_id 
                         ORDER BY pv.is_default DESC, pv.sku";
    $product_var_stmt = $db->prepare($product_var_query);
    $product_var_stmt->bindParam(':product_id', $selected_product_id);
    $product_var_stmt->execute();
    $product_variations = $product_var_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Variations - LuxePerfume Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <header class="admin-header">
                <h1><i class="fas fa-cube"></i> Product Variations</h1>
                <div class="header-actions">
                    <a href="products.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
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

                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-filter"></i> Filter Variations</h2>
                    </div>
                    <div class="card-body">
                        <form method="get" class="form-grid">
                            <div class="form-group">
                                <label for="product_id">Select Product</label>
                                <select name="product_id" id="product_id" onchange="this.form.submit()">
                                    <option value="">All Products</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>" 
                                            <?php echo ($selected_product_id == $product['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($product['name']); ?> (<?php echo $product['sku']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-plus-circle"></i> Add New Variation</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" class="form-grid">
                            <?php echo CSRF::getTokenField(); ?>
                            <input type="hidden" name="action" value="add_variation">
                            
                            <div class="form-group">
                                <label for="add_product_id">Product *</label>
                                <select name="product_id" id="add_product_id" required>
                                    <option value="">Select Product</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>" 
                                            <?php echo ($selected_product_id == $product['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="sku">SKU *</label>
                                <input type="text" name="sku" id="sku" required 
                                       placeholder="e.g., PERF001-50ML-EDT">
                            </div>
                            
                            <div class="form-group">
                                <label for="price">Price ($) *</label>
                                <input type="number" name="price" id="price" step="0.01" min="0" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="sale_price">Sale Price ($)</label>
                                <input type="number" name="sale_price" id="sale_price" step="0.01" min="0">
                            </div>
                            
                            <div class="form-group">
                                <label for="stock">Stock Quantity *</label>
                                <input type="number" name="stock" id="stock" min="0" required value="0">
                            </div>
                            
                            <div class="form-group">
                                <label for="weight">Weight (g)</label>
                                <input type="number" name="weight" id="weight" step="0.01" min="0" value="0">
                            </div>
                            
                            <?php foreach ($attributes as $attr_name => $attr_data): ?>
                                <div class="form-group">
                                    <label for="attr_<?php echo strtolower($attr_name); ?>"><?php echo $attr_name; ?></label>
                                    <?php if ($attr_data['type'] === 'select'): ?>
                                        <select name="attributes[<?php echo $attr_name; ?>]" 
                                                id="attr_<?php echo strtolower($attr_name); ?>">
                                            <option value="">Select <?php echo $attr_name; ?></option>
                                            <?php foreach ($attr_data['values'] as $value): ?>
                                                <option value="<?php echo $value['value']; ?>">
                                                    <?php echo $value['value']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" name="attributes[<?php echo $attr_name; ?>]" 
                                               id="attr_<?php echo strtolower($attr_name); ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="form-group full-width">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Variation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> 
                            <?php echo $selected_product_id ? 'Product Variations' : 'All Variations'; ?>
                            <span class="badge badge-primary"><?php echo count($selected_product_id ? $product_variations : $variations); ?></span>
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Attributes</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Default</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $variations_list = $selected_product_id ? $product_variations : $variations; ?>
                                    <?php foreach ($variations_list as $variation): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($variation['product_name']); ?></td>
                                            <td><strong><?php echo $variation['sku']; ?></strong></td>
                                            <td>
                                                <?php 
                                                $attrs = json_decode($variation['sku_attributes_json'], true);
                                                if ($attrs) {
                                                    echo implode(', ', array_map(function($k, $v) {
                                                        return "$k: $v";
                                                    }, array_keys($attrs), $attrs));
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <span class="text-gold">$<?php echo $variation['price']; ?></span>
                                                <?php if ($variation['sale_price']): ?>
                                                    <br><small><s>$<?php echo $variation['price']; ?></s> 
                                                    <strong class="text-gold">$<?php echo $variation['sale_price']; ?></strong></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="<?php echo $variation['stock'] > 10 ? 'status-active' : 'status-warning'; ?>">
                                                    <?php echo $variation['stock']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($variation['is_default']): ?>
                                                    <span class="status-badge status-active">Default</span>
                                                <?php else: ?>
                                                    <form method="post" style="display: inline;">
                                                        <?php echo CSRF::getTokenField(); ?>
                                                        <input type="hidden" name="action" value="set_default">
                                                        <input type="hidden" name="variation_id" value="<?php echo $variation['id']; ?>">
                                                        <input type="hidden" name="product_id" value="<?php echo $variation['product_id']; ?>">
                                                        <button type="submit" class="btn btn-secondary btn-sm">
                                                            <i class="fas fa-star"></i> Set Default
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn btn-primary btn-sm" 
                                                            onclick="editVariation(<?php echo $variation['id']; ?>)">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <form method="post" style="display: inline;">
                                                        <?php echo CSRF::getTokenField(); ?>
                                                        <input type="hidden" name="action" value="delete_variation">
                                                        <input type="hidden" name="variation_id" value="<?php echo $variation['id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                                onclick="return confirm('Delete this variation?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Edit Variation Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Edit Variation</h3>
                <span class="close">&times;</span>
            </div>
            <form id="editForm" method="post">
                <div class="modal-body">
                    <?php echo CSRF::getTokenField(); ?>
                    <input type="hidden" name="action" value="update_variation">
                    <input type="hidden" name="variation_id" id="edit_variation_id">
                    
                    <div class="form-group">
                        <label for="edit_sku">SKU</label>
                        <input type="text" name="sku" id="edit_sku" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_price">Price ($)</label>
                        <input type="number" name="price" id="edit_price" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_sale_price">Sale Price ($)</label>
                        <input type="number" name="sale_price" id="edit_sale_price" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_stock">Stock Quantity</label>
                        <input type="number" name="stock" id="edit_stock" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_weight">Weight (g)</label>
                        <input type="number" name="weight" id="edit_weight" step="0.01" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Variation</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById('editModal');
        const closeBtn = document.querySelector('.close');
        const closeModalBtn = document.getElementById('closeModal');
        
        function editVariation(variationId) {
            // In a real implementation, you would fetch variation data via AJAX
            // For now, we'll simulate with existing data
            const variation = <?php echo json_encode($variations); ?>.find(v => v.id == variationId);
            if (variation) {
                document.getElementById('edit_variation_id').value = variation.id;
                document.getElementById('edit_sku').value = variation.sku;
                document.getElementById('edit_price').value = variation.price;
                document.getElementById('edit_sale_price').value = variation.sale_price || '';
                document.getElementById('edit_stock').value = variation.stock;
                document.getElementById('edit_weight').value = variation.weight || '';
                modal.style.display = 'block';
            }
        }
        
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }
        
        closeModalBtn.onclick = function() {
            modal.style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        // Auto-generate SKU based on product and attributes
        document.getElementById('add_product_id').addEventListener('change', function() {
            const productId = this.value;
            if (productId) {
                // Fetch product SKU and generate variation SKU
                const products = <?php echo json_encode($products); ?>;
                const product = products.find(p => p.id == productId);
                if (product) {
                    const baseSku = product.sku;
                    document.getElementById('sku').value = baseSku + '-';
                }
            }
        });
    </script>
</body>
</html>