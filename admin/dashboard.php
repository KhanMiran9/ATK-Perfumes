<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$auth = new Auth();

// Check if user is logged in and has admin access
if (!$auth->isLoggedIn() || !$auth->hasPermission('view_reports')) {
    redirect('../login.php');
}

// Get dashboard statistics
$database = new Database();
$conn = $database->getConnection();

// Total orders
$query = "SELECT COUNT(*) as total_orders FROM orders";
$stmt = $conn->prepare($query);
$stmt->execute();
$total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

// Total revenue
$query = "SELECT SUM(total_amount) as total_revenue FROM orders WHERE status IN ('paid', 'shipped', 'delivered')";
$stmt = $conn->prepare($query);
$stmt->execute();
$total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;

// Total products
$query = "SELECT COUNT(*) as total_products FROM products WHERE is_active = 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];

// Total customers
$query = "SELECT COUNT(*) as total_customers FROM users WHERE role_id = 4 AND status = 'active'";
$stmt = $conn->prepare($query);
$stmt->execute();
$total_customers = $stmt->fetch(PDO::FETCH_ASSOC)['total_customers'];

// Recent orders
$query = "SELECT o.*, u.name as customer_name 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC 
          LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->execute();
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Low stock products
$query = "SELECT p.name, pv.sku, pv.stock 
          FROM product_variations pv 
          JOIN products p ON pv.product_id = p.id 
          WHERE pv.stock < 10 
          ORDER BY pv.stock ASC 
          LIMIT 5";
$stmt = $conn->prepare($query);
$stmt->execute();
$low_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | LuxePerfume</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Dashboard</h1>
                <p>Welcome back, <?php echo $_SESSION['user_name']; ?></p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <p class="stat-number"><?php echo $total_orders; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <p class="stat-number">$<?php echo number_format($total_revenue, 2); ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <p class="stat-number"><?php echo $total_products; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Total Customers</h3>
                    <p class="stat-number"><?php echo $total_customers; ?></p>
                </div>
            </div>
            
            <div class="dashboard-sections">
                <div class="dashboard-section">
                    <h2>Recent Orders</h2>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><?php echo $order['order_number']; ?></td>
                                    <td><?php echo $order['customer_name']; ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                    <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="dashboard-section">
                    <h2>Low Stock Alert</h2>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($low_stock as $product): ?>
                                <tr>
                                    <td><?php echo $product['name']; ?></td>
                                    <td><?php echo $product['sku']; ?></td>
                                    <td><span class="stock-warning"><?php echo $product['stock']; ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($low_stock)): ?>
                                <tr>
                                    <td colspan="3" class="text-center">No low stock products</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>