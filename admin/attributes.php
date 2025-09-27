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

// Handle form submissions
if ($_POST) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!CSRF::validateToken($csrf_token)) {
        $error = "Invalid CSRF token";
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add_attribute') {
            // Add new attribute
            $name = trim($_POST['name'] ?? '');
            $type = $_POST['type'] ?? 'select';
            
            if ($name) {
                $query = "INSERT INTO attributes (name, type) VALUES (:name, :type)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':type', $type);
                
                if ($stmt->execute()) {
                    $success = "Attribute added successfully";
                } else {
                    $error = "Failed to add attribute";
                }
            }
        } elseif ($action === 'add_value') {
            // Add attribute value
            $attribute_id = $_POST['attribute_id'] ?? '';
            $value = trim($_POST['value'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            
            if ($attribute_id && $value) {
                if (empty($slug)) {
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $value)));
                }
                
                $query = "INSERT INTO attribute_values (attribute_id, value, slug) VALUES (:attribute_id, :value, :slug)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':attribute_id', $attribute_id);
                $stmt->bindParam(':value', $value);
                $stmt->bindParam(':slug', $slug);
                
                if ($stmt->execute()) {
                    $success = "Attribute value added successfully";
                } else {
                    $error = "Failed to add attribute value";
                }
            }
        } elseif ($action === 'delete_attribute') {
            // Delete attribute
            $attribute_id = $_POST['attribute_id'] ?? '';
            
            if ($attribute_id) {
                $query = "DELETE FROM attributes WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $attribute_id);
                
                if ($stmt->execute()) {
                    $success = "Attribute deleted successfully";
                } else {
                    $error = "Failed to delete attribute";
                }
            }
        } elseif ($action === 'delete_value') {
            // Delete attribute value
            $value_id = $_POST['value_id'] ?? '';
            
            if ($value_id) {
                $query = "DELETE FROM attribute_values WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $value_id);
                
                if ($stmt->execute()) {
                    $success = "Attribute value deleted successfully";
                } else {
                    $error = "Failed to delete attribute value";
                }
            }
        }
    }
}

// Get all attributes with their values
$attributes_query = "SELECT a.*, 
                    (SELECT COUNT(*) FROM attribute_values av WHERE av.attribute_id = a.id) as value_count
                    FROM attributes a ORDER BY a.name";
$attributes_stmt = $db->prepare($attributes_query);
$attributes_stmt->execute();
$attributes = $attributes_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get attribute values for each attribute
$attribute_values = [];
foreach ($attributes as $attribute) {
    $values_query = "SELECT * FROM attribute_values WHERE attribute_id = :attribute_id ORDER BY value";
    $values_stmt = $db->prepare($values_query);
    $values_stmt->bindParam(':attribute_id', $attribute['id']);
    $values_stmt->execute();
    $attribute_values[$attribute['id']] = $values_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attributes Management - LuxePerfume Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <header class="admin-header">
                <h1><i class="fas fa-tags"></i> Attributes Management</h1>
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
                        <h2><i class="fas fa-plus-circle"></i> Add New Attribute</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" class="form-grid">
                            <?php echo CSRF::getTokenField(); ?>
                            <input type="hidden" name="action" value="add_attribute">
                            
                            <div class="form-group">
                                <label for="name">Attribute Name *</label>
                                <input type="text" name="name" id="name" required 
                                       placeholder="e.g., Volume, Concentration">
                            </div>
                            
                            <div class="form-group">
                                <label for="type">Input Type *</label>
                                <select name="type" id="type" required>
                                    <option value="select">Dropdown Select</option>
                                    <option value="radio">Radio Buttons</option>
                                    <option value="text">Text Input</option>
                                </select>
                            </div>
                            
                            <div class="form-group full-width">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Attribute
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> Existing Attributes</h2>
                    </div>
                    <div class="card-body">
                        <?php if (empty($attributes)): ?>
                            <p>No attributes found. Create your first attribute above.</p>
                        <?php else: ?>
                            <?php foreach ($attributes as $attribute): ?>
                                <div class="attribute-item">
                                    <div class="attribute-header">
                                        <h3>
                                            <?php echo htmlspecialchars($attribute['name']); ?>
                                            <span class="badge badge-primary"><?php echo $attribute['type']; ?></span>
                                            <span class="badge badge-secondary"><?php echo $attribute['value_count']; ?> values</span>
                                        </h3>
                                        <div class="attribute-actions">
                                            <button class="btn btn-primary btn-sm" 
                                                    onclick="showAddValueModal(<?php echo $attribute['id']; ?>, '<?php echo htmlspecialchars($attribute['name']); ?>')">
                                                <i class="fas fa-plus"></i> Add Value
                                            </button>
                                            <form method="post" style="display: inline;">
                                                <?php echo CSRF::getTokenField(); ?>
                                                <input type="hidden" name="action" value="delete_attribute">
                                                <input type="hidden" name="attribute_id" value="<?php echo $attribute['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Delete this attribute and all its values?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($attribute_values[$attribute['id']])): ?>
                                        <div class="attribute-values">
                                            <table class="data-table">
                                                <thead>
                                                    <tr>
                                                        <th>Value</th>
                                                        <th>Slug</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($attribute_values[$attribute['id']] as $value): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($value['value']); ?></td>
                                                            <td><code><?php echo $value['slug']; ?></code></td>
                                                            <td>
                                                                <form method="post" style="display: inline;">
                                                                    <?php echo CSRF::getTokenField(); ?>
                                                                    <input type="hidden" name="action" value="delete_value">
                                                                    <input type="hidden" name="value_id" value="<?php echo $value['id']; ?>">
                                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                                            onclick="return confirm('Delete this value?')">
                                                                        <i class="fas fa-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <p class="no-values">No values added yet.</p>
                                    <?php endif; ?>
                                </div>
                                <hr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Value Modal -->
    <div id="addValueModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus"></i> Add Value to <span id="modalAttributeName"></span></h3>
                <span class="close">&times;</span>
            </div>
            <form method="post">
                <div class="modal-body">
                    <?php echo CSRF::getTokenField(); ?>
                    <input type="hidden" name="action" value="add_value">
                    <input type="hidden" name="attribute_id" id="modalAttributeId">
                    
                    <div class="form-group">
                        <label for="modalValue">Value *</label>
                        <input type="text" name="value" id="modalValue" required 
                               placeholder="e.g., 50ml, EDP">
                    </div>
                    
                    <div class="form-group">
                        <label for="modalSlug">Slug</label>
                        <input type="text" name="slug" id="modalSlug" 
                               placeholder="auto-generated">
                        <small>Leave empty to auto-generate from value</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeValueModal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Value</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality for adding values
        const valueModal = document.getElementById('addValueModal');
        const closeBtn = document.querySelector('.close');
        const closeValueModalBtn = document.getElementById('closeValueModal');
        
        function showAddValueModal(attributeId, attributeName) {
            document.getElementById('modalAttributeId').value = attributeId;
            document.getElementById('modalAttributeName').textContent = attributeName;
            valueModal.style.display = 'block';
        }
        
        closeBtn.onclick = function() {
            valueModal.style.display = 'none';
        }
        
        closeValueModalBtn.onclick = function() {
            valueModal.style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target == valueModal) {
                valueModal.style.display = 'none';
            }
        }
        
        // Auto-generate slug from value
        document.getElementById('modalValue').addEventListener('input', function() {
            const slugField = document.getElementById('modalSlug');
            if (!slugField.value) {
                const slug = this.value.toLowerCase()
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
                slugField.value = slug;
            }
        });
    </script>

    <style>
        .attribute-item {
            background: #f9f9f9;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: var(--radius-md);
            border: 1px solid #e0e0e0;
        }
        
        .attribute-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .attribute-header h3 {
            margin: 0;
            font-family: 'Cinzel', serif;
            color: var(--black);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .attribute-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .attribute-values {
            margin-top: 1rem;
        }
        
        .no-values {
            color: var(--muted);
            font-style: italic;
            text-align: center;
            padding: 1rem;
        }
        
        .attribute-item hr {
            margin: 1.5rem -1.5rem 0 -1.5rem;
            border-color: #e0e0e0;
        }
    </style>
</body>
</html>