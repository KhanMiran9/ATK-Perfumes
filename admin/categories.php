<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$auth = new Auth();

// Check if user is logged in and has permission
if (!$auth->isLoggedIn() || !$auth->hasPermission('manage_categories')) {
    redirect('../login.php');
}

$database = new Database();
$conn = $database->getConnection();

// Get all categories with parent information
$query = "SELECT c.*, p.name as parent_name 
          FROM categories c 
          LEFT JOIN categories p ON c.parent_id = p.id 
          ORDER BY c.parent_id, c.name";
$stmt = $conn->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get parent categories for dropdown
$parentCategories = $conn->query("SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (validateCsrfToken($csrf_token)) {
        if ($action === 'add') {
            $name = sanitizeInput($_POST['name']);
            $slug = sanitizeInput($_POST['slug']);
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $description = sanitizeInput($_POST['description'] ?? '');
            
            if (empty($name) || empty($slug)) {
                $error = 'Please fill in all required fields';
            } else {
                $insertQuery = "INSERT INTO categories (name, slug, parent_id, description) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($insertQuery);
                $stmt->bind_param('ssis', $name, $slug, $parentId, $description);
                
                if ($stmt->execute()) {
                    $success = 'Category added successfully!';
                    // Refresh categories list
                    $categories = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
                } else {
                    $error = 'Error adding category: ' . $stmt->error;
                }
            }
        } elseif ($action === 'edit') {
            $id = (int)$_POST['id'];
            $name = sanitizeInput($_POST['name']);
            $slug = sanitizeInput($_POST['slug']);
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $description = sanitizeInput($_POST['description'] ?? '');
            
            $updateQuery = "UPDATE categories SET name = ?, slug = ?, parent_id = ?, description = ? WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param('ssisi', $name, $slug, $parentId, $description, $id);
            
            if ($stmt->execute()) {
                $success = 'Category updated successfully!';
                $categories = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
            } else {
                $error = 'Error updating category: ' . $stmt->error;
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            // Check if category has products
            $checkQuery = "SELECT COUNT(*) as product_count FROM products WHERE category_id = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param('i', $id);
            $checkStmt->execute();
            $result = $checkStmt->get_result()->fetch_assoc();
            
            if ($result['product_count'] > 0) {
                $error = 'Cannot delete category with products. Please reassign products first.';
            } else {
                $deleteQuery = "DELETE FROM categories WHERE id = ?";
                $deleteStmt = $conn->prepare($deleteQuery);
                $deleteStmt->bind_param('i', $id);
                
                if ($deleteStmt->execute()) {
                    $success = 'Category deleted successfully!';
                    $categories = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
                } else {
                    $error = 'Error deleting category: ' . $deleteStmt->error;
                }
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
    <title>Manage Categories | LuxePerfume Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Manage Categories</h1>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <h2>Add New Category</h2>
                <form method="POST" class="category-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Category Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="slug">Slug *</label>
                            <input type="text" id="slug" name="slug" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="parent_id">Parent Category</label>
                            <select id="parent_id" name="parent_id">
                                <option value="">None (Top Level)</option>
                                <?php foreach ($parentCategories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Categories List</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Parent</th>
                                <th>Products</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($categories) > 0): ?>
                                <?php foreach ($categories as $category): 
                                    // Count products in this category
                                    $countQuery = "SELECT COUNT(*) as product_count FROM products WHERE category_id = ?";
                                    $countStmt = $conn->prepare($countQuery);
                                    $countStmt->bind_param('i', $category['id']);
                                    $countStmt->execute();
                                    $count = $countStmt->get_result()->fetch_assoc();
                                ?>
                                    <tr>
                                        <td><?php echo $category['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                            <?php if ($category['parent_id']): ?>
                                                <br><small class="text-muted">Child category</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $category['slug']; ?></td>
                                        <td><?php echo $category['parent_name'] ?: '—'; ?></td>
                                        <td><?php echo $count['product_count']; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                        onclick="openEditModal(<?php echo htmlspecialchars(json_encode($category)); ?>)">
                                                    Edit
                                                </button>
                                                <form method="POST" class="inline-form" onsubmit="return confirm('Delete this category?')">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No categories found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Category</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label for="edit_name">Category Name *</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_slug">Slug *</label>
                    <input type="text" id="edit_slug" name="slug" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_parent_id">Parent Category</label>
                    <select id="edit_parent_id" name="parent_id">
                        <option value="">None (Top Level)</option>
                        <?php foreach ($parentCategories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Category</button>
            </form>
        </div>
    </div>

    <script>
    function openEditModal(category) {
        document.getElementById('edit_id').value = category.id;
        document.getElementById('edit_name').value = category.name;
        document.getElementById('edit_slug').value = category.slug;
        document.getElementById('edit_parent_id').value = category.parent_id || '';
        document.getElementById('edit_description').value = category.description || '';
        
        document.getElementById('editModal').style.display = 'block';
    }
    
    document.querySelector('.close').addEventListener('click', function() {
        document.getElementById('editModal').style.display = 'none';
    });
    
    window.addEventListener('click', function(event) {
        if (event.target === document.getElementById('editModal')) {
            document.getElementById('editModal').style.display = 'none';
        }
    });
    </script>
</body>
</html>