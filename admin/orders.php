<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$auth = new Auth();

// Check if user is logged in and has permission
if (!$auth->isLoggedIn() || !$auth->hasPermission('manage_orders')) {
    redirect('../login.php');
}

$database = new Database();
$conn = $database->getConnection();

// Get orders with filters and pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT SQL_CALC_FOUND_ROWS o.*, u.name as customer_name 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE 1=1";

$params = [];

if (!empty($status) && $status !== 'all') {
    $query .= " AND o.status = :status";
    $params[':status'] = $status;
}

if (!empty($search)) {
    $query .= " AND (o.order_number LIKE :search OR u.name LIKE :search2 OR u.email LIKE :search3)";
    $params[':search'] = "%$search%";
    $params[':search2'] = "%$search%";
    $params[':search3'] = "%$search%";
}

$query .= " ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset";
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
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalResult = $conn->query("SELECT FOUND_ROWS()")->fetchColumn();
$totalPages = ceil($totalResult / $limit);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (validateCsrfToken($csrf_token)) {
        if ($action === 'update_status') {
            $orderId = (int)$_POST['order_id'];
            $newStatus = sanitizeInput($_POST['status']);
            
            $updateQuery = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param('si', $newStatus, $orderId);
            
            if ($stmt->execute()) {
                $success = 'Order status updated successfully!';
                // Refresh orders list
                $stmt = $conn->prepare($query);
                if (!empty($types)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } else {
                $error = 'Error updating order status: ' . $stmt->error;
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
    <title>Manage Orders | LuxePerfume Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Manage Orders</h1>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <!-- Filters -->
            <div class="filters-card">
                <form method="GET" class="filter-form">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Search orders..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <select name="status">
                            <option value="all">All Status</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo $status === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="shipped" <?php echo $status === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $status === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="refunded" <?php echo $status === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-secondary">Apply Filters</button>
                    <a href="orders.php" class="btn btn-outline">Clear</a>
                </form>
            </div>
            
            <!-- Orders Table -->
            <div class="card">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($orders) > 0): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $order['order_number']; ?></strong>
                                            <br>
                                            <small class="text-muted">ID: <?php echo $order['id']; ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo ucfirst($order['payment_method']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                                
                                                <!-- Status Update Form -->
                                                <form method="POST" class="inline-form">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <select name="status" onchange="this.form.submit()" class="status-select">
                                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="paid" <?php echo $order['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                                        <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                        <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                        <option value="refunded" <?php echo $order['status'] === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                                                    </select>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No orders found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
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