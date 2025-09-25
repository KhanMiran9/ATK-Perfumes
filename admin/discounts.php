<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$auth = new Auth();

// Check if user is logged in and has permission
if (!$auth->isLoggedIn() || !$auth->hasPermission('manage_discounts')) {
    redirect('../login.php');
}

$database = new Database();
$conn = $database->getConnection();

// Get discounts with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$query = "SELECT SQL_CALC_FOUND_ROWS * FROM discounts ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($query);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$discounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalResult = $conn->query("SELECT FOUND_ROWS()")->fetchColumn();
$totalPages = ceil($totalResult / $limit);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (validateCsrfToken($csrf_token)) {
        if ($action === 'add') {
            $code = sanitizeInput($_POST['code']);
            $type = sanitizeInput($_POST['type']);
            $value = (float)$_POST['value'];
            $minCartValue = (float)$_POST['min_cart_value'];
            $startsAt = $_POST['starts_at'] ?: null;
            $expiresAt = $_POST['expires_at'] ?: null;
            $usageLimit = !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null;
            $perUserLimit = !empty($_POST['per_user_limit']) ? (int)$_POST['per_user_limit'] : 1;
            $active = isset($_POST['active']) ? 1 : 0;
            
            $insertQuery = "INSERT INTO discounts (code, type, value, min_cart_value, starts_at, expires_at, usage_limit, per_user_limit, active)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param('ssddssiii', $code, $type, $value, $minCartValue, $startsAt, $expiresAt, $usageLimit, $perUserLimit, $active);
            
            if ($stmt->execute()) {
                $success = 'Discount added successfully!';
                // Refresh discounts list
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ii', $limit, $offset);
                $stmt->execute();
                $discounts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } else {
                $error = 'Error adding discount: ' . $stmt->error;
            }
        } elseif ($action === 'toggle') {
            $id = (int)$_POST['id'];
            $active = (int)$_POST['active'];
            
            $updateQuery = "UPDATE discounts SET active = ? WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param('ii', $active, $id);
            
            if ($stmt->execute()) {
                $success = 'Discount updated successfully!';
                $discounts = $conn->query("SELECT * FROM discounts ORDER BY created_at DESC LIMIT $limit OFFSET $offset")->fetch_all(MYSQLI_ASSOC);
            } else {
                $error = 'Error updating discount: ' . $stmt->error;
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            $deleteQuery = "DELETE FROM discounts WHERE id = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param('i', $id);
            
            if ($stmt->execute()) {
                $success = 'Discount deleted successfully!';
                $discounts = $conn->query("SELECT * FROM discounts ORDER BY created_at DESC LIMIT $limit OFFSET $offset")->fetch_all(MYSQLI_ASSOC);
            } else {
                $error = 'Error deleting discount: ' . $stmt->error;
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
    <title>Manage Discounts | LuxePerfume Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Manage Discounts</h1>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <h2>Add New Discount</h2>
                <form method="POST" class="discount-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="code">Discount Code *</label>
                            <input type="text" id="code" name="code" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="type">Type *</label>
                            <select id="type" name="type" required>
                                <option value="percent">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="value">Value *</label>
                            <input type="number" id="value" name="value" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="min_cart_value">Minimum Cart Value</label>
                            <input type="number" id="min_cart_value" name="min_cart_value" step="0.01" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="starts_at">Starts At</label>
                            <input type="datetime-local" id="starts_at" name="starts_at">
                        </div>
                        
                        <div class="form-group">
                            <label for="expires_at">Expires At</label>
                            <input type="datetime-local" id="expires_at" name="expires_at">
                        </div>
                        
                        <div class="form-group">
                            <label for="usage_limit">Usage Limit</label>
                            <input type="number" id="usage_limit" name="usage_limit" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="per_user_limit">Per User Limit</label>
                            <input type="number" id="per_user_limit" name="per_user_limit" min="1" value="1">
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="active" name="active" value="1" checked>
                                <span>Active</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Add Discount</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Discounts List</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Min Cart</th>
                                <th>Usage Limit</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($discounts) > 0): ?>
                                <?php foreach ($discounts as $discount): ?>
                                    <tr>
                                        <td><?php echo $discount['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($discount['code']); ?></strong></td>
                                        <td><?php echo ucfirst($discount['type']); ?></td>
                                        <td>
                                            <?php echo $discount['type'] === 'percent' ? 
                                                $discount['value'] . '%' : 
                                                '$' . number_format($discount['value'], 2); ?>
                                        </td>
                                        <td>$<?php echo number_format($discount['min_cart_value'], 2); ?></td>
                                        <td>
                                            <?php echo $discount['usage_limit'] ?: 'Unlimited'; ?>
                                            <?php if ($discount['per_user_limit'] > 1): ?>
                                                <br><small>(<?php echo $discount['per_user_limit']; ?> per user)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo $discount['active'] ? 'active' : 'inactive'; ?>">
                                                <?php echo $discount['active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                            <?php if ($discount['expires_at'] && strtotime($discount['expires_at']) < time()): ?>
                                                <br><small class="text-danger">Expired</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <form method="POST" class="inline-form">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="action" value="toggle">
                                                    <input type="hidden" name="id" value="<?php echo $discount['id']; ?>">
                                                    <input type="hidden" name="active" value="<?php echo $discount['active'] ? 0 : 1; ?>">
                                                    <button type="submit" class="btn btn-sm btn-<?php echo $discount['active'] ? 'warning' : 'success'; ?>">
                                                        <?php echo $discount['active'] ? 'Deactivate' : 'Activate'; ?>
                                                    </button>
                                                </form>
                                                <form method="POST" class="inline-form" onsubmit="return confirm('Delete this discount?')">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $discount['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No discounts found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="page-link">Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="page-link">Next</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>