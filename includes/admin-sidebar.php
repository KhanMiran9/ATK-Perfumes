<?php
// Check user role and permissions
$currentPage = basename($_SERVER['PHP_SELF']);
$auth = new Auth();
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <i class="fas fa-wine-bottle-alt logo-icon"></i>
            <h2>Luxe<span>Perfume</span></h2>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <!-- Dashboard -->
            <li class="nav-item <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Products Management -->
            <?php if ($auth->hasPermission('manage_products')): ?>
            <li class="nav-item has-submenu <?php echo in_array($currentPage, ['products.php', 'product_add.php', 'product_edit.php', 'product_variations.php', 'product_media.php']) ? 'active' : ''; ?>">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-cube"></i>
                    <span>Products</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="products.php" class="<?php echo $currentPage === 'products.php' ? 'active' : ''; ?>">All Products</a></li>
                    <li><a href="product_add.php" class="<?php echo $currentPage === 'product_add.php' ? 'active' : ''; ?>">Add New</a></li>
                    <li><a href="product_variations.php" class="<?php echo $currentPage === 'product_variations.php' ? 'active' : ''; ?>">Variations</a></li>
                    <li><a href="product_media.php" class="<?php echo $currentPage === 'product_media.php' ? 'active' : ''; ?>">Media Library</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Categories Management -->
            <?php if ($auth->hasPermission('manage_categories')): ?>
            <li class="nav-item <?php echo in_array($currentPage, ['categories.php', 'category_add.php', 'category_edit.php']) ? 'active' : ''; ?>">
                <a href="categories.php">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Inventory Management -->
            <?php if ($auth->hasPermission('manage_inventory')): ?>
            <li class="nav-item has-submenu <?php echo in_array($currentPage, ['inventory.php', 'inventory_logs.php', 'stock_management.php']) ? 'active' : ''; ?>">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-boxes"></i>
                    <span>Inventory</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="inventory.php" class="<?php echo $currentPage === 'inventory.php' ? 'active' : ''; ?>">Stock Overview</a></li>
                    <li><a href="stock_management.php" class="<?php echo $currentPage === 'stock_management.php' ? 'active' : ''; ?>">Stock Management</a></li>
                    <li><a href="inventory_logs.php" class="<?php echo $currentPage === 'inventory_logs.php' ? 'active' : ''; ?>">Inventory Logs</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Orders Management -->
            <?php if ($auth->hasPermission('manage_orders')): ?>
            <li class="nav-item has-submenu <?php echo in_array($currentPage, ['orders.php', 'order_view.php', 'order_edit.php']) ? 'active' : ''; ?>">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                    <span class="badge badge-warning">12</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="orders.php" class="<?php echo $currentPage === 'orders.php' ? 'active' : ''; ?>">All Orders</a></li>
                    <li><a href="orders.php?status=pending" class="<?php echo $currentPage === 'orders.php' && ($_GET['status'] ?? '') === 'pending' ? 'active' : ''; ?>">Pending</a></li>
                    <li><a href="orders.php?status=paid" class="<?php echo $currentPage === 'orders.php' && ($_GET['status'] ?? '') === 'paid' ? 'active' : ''; ?>">Paid</a></li>
                    <li><a href="orders.php?status=shipped" class="<?php echo $currentPage === 'orders.php' && ($_GET['status'] ?? '') === 'shipped' ? 'active' : ''; ?>">Shipped</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Customers Management -->
            <?php if ($auth->hasPermission('manage_users')): ?>
            <li class="nav-item has-submenu <?php echo in_array($currentPage, ['customers.php', 'customer_view.php', 'wishlist.php']) ? 'active' : ''; ?>">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="customers.php" class="<?php echo $currentPage === 'customers.php' ? 'active' : ''; ?>">All Customers</a></li>
                    <li><a href="wishlist.php" class="<?php echo $currentPage === 'wishlist.php' ? 'active' : ''; ?>">Wishlists</a></li>
                    <li><a href="reviews.php" class="<?php echo $currentPage === 'reviews.php' ? 'active' : ''; ?>">Reviews</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Discounts & Promotions -->
            <?php if ($auth->hasPermission('manage_discounts')): ?>
            <li class="nav-item <?php echo in_array($currentPage, ['discounts.php', 'discount_add.php', 'discount_edit.php']) ? 'active' : ''; ?>">
                <a href="discounts.php">
                    <i class="fas fa-tag"></i>
                    <span>Discounts</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Content Management -->
            <li class="nav-item has-submenu <?php echo in_array($currentPage, ['pages.php', 'media.php', 'banners.php']) ? 'active' : ''; ?>">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-palette"></i>
                    <span>Content</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="pages.php" class="<?php echo $currentPage === 'pages.php' ? 'active' : ''; ?>">Pages</a></li>
                    <li><a href="media.php" class="<?php echo $currentPage === 'media.php' ? 'active' : ''; ?>">Media Library</a></li>
                    <li><a href="banners.php" class="<?php echo $currentPage === 'banners.php' ? 'active' : ''; ?>">Banners</a></li>
                </ul>
            </li>
            
            <!-- Reports & Analytics -->
            <?php if ($auth->hasPermission('view_reports')): ?>
            <li class="nav-item has-submenu <?php echo in_array($currentPage, ['reports.php', 'analytics.php', 'sales_report.php']) ? 'active' : ''; ?>">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="reports.php" class="<?php echo $currentPage === 'reports.php' ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="sales_report.php" class="<?php echo $currentPage === 'sales_report.php' ? 'active' : ''; ?>">Sales Report</a></li>
                    <li><a href="analytics.php" class="<?php echo $currentPage === 'analytics.php' ? 'active' : ''; ?>">Analytics</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- User Management -->
            <?php if ($auth->hasPermission('manage_users')): ?>
            <li class="nav-item has-submenu <?php echo in_array($currentPage, ['users.php', 'user_add.php', 'user_edit.php', 'roles.php']) ? 'active' : ''; ?>">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-user-cog"></i>
                    <span>Users & Roles</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="users.php" class="<?php echo $currentPage === 'users.php' ? 'active' : ''; ?>">All Users</a></li>
                    <li><a href="user_add.php" class="<?php echo $currentPage === 'user_add.php' ? 'active' : ''; ?>">Add User</a></li>
                    <li><a href="roles.php" class="<?php echo $currentPage === 'roles.php' ? 'active' : ''; ?>">Roles</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Messages & Communications -->
            <li class="nav-item <?php echo in_array($currentPage, ['messages.php', 'message_view.php']) ? 'active' : ''; ?>">
                <a href="messages.php">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                    <span class="badge badge-primary">5</span>
                </a>
            </li>
            
            <!-- Settings -->
            <li class="nav-item has-submenu <?php echo in_array($currentPage, ['settings.php', 'general_settings.php', 'payment_settings.php', 'shipping_settings.php']) ? 'active' : ''; ?>">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="general_settings.php" class="<?php echo $currentPage === 'general_settings.php' ? 'active' : ''; ?>">General</a></li>
                    <li><a href="payment_settings.php" class="<?php echo $currentPage === 'payment_settings.php' ? 'active' : ''; ?>">Payment</a></li>
                    <li><a href="shipping_settings.php" class="<?php echo $currentPage === 'shipping_settings.php' ? 'active' : ''; ?>">Shipping</a></li>
                </ul>
            </li>
            
            <!-- Audit Logs -->
            <?php if ($auth->hasPermission('manage_roles')): ?>
            <li class="nav-item <?php echo $currentPage === 'audit_logs.php' ? 'active' : ''; ?>">
                <a href="audit_logs.php">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Audit Logs</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-details">
                <span class="user-name"><?php echo $_SESSION['user_name'] ?? 'Admin'; ?></span>
                <span class="user-role"><?php echo $_SESSION['user_role'] ?? 'Administrator'; ?></span>
            </div>
        </div>
        <a href="../logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const adminContent = document.querySelector('.admin-content');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            adminContent.classList.toggle('expanded');
        });
    }
    
    // Submenu toggle functionality
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.parentElement;
            parent.classList.toggle('open');
            
            // Close other open submenus
            submenuToggles.forEach(otherToggle => {
                if (otherToggle !== this) {
                    otherToggle.parentElement.classList.remove('open');
                }
            });
        });
    });
    
    // Auto-open active submenu
    const activeSubmenu = document.querySelector('.nav-item.active.has-submenu');
    if (activeSubmenu) {
        activeSubmenu.classList.add('open');
    }
});
</script>