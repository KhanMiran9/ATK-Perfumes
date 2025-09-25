<?php
// Check user role and permissions
$currentPage = basename($_SERVER['PHP_SELF']);
$auth = new Auth();
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h2>LuxePerfume Admin</h2>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li class="nav-item <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                <a href="dashboard.php">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <?php if ($auth->hasPermission('manage_products')): ?>
            <li class="nav-item <?php echo in_array($currentPage, ['products.php', 'product_add.php', 'product_edit.php']) ? 'active' : ''; ?>">
                <a href="products.php">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    <span>Products</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($auth->hasPermission('manage_categories')): ?>
            <li class="nav-item <?php echo $currentPage === 'categories.php' ? 'active' : ''; ?>">
                <a href="categories.php">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <span>Categories</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($auth->hasPermission('manage_orders')): ?>
            <li class="nav-item <?php echo $currentPage === 'orders.php' ? 'active' : ''; ?>">
                <a href="orders.php">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <span>Orders</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($auth->hasPermission('manage_users')): ?>
            <li class="nav-item <?php echo $currentPage === 'users.php' ? 'active' : ''; ?>">
                <a href="users.php">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Users</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($auth->hasPermission('manage_discounts')): ?>
            <li class="nav-item <?php echo $currentPage === 'discounts.php' ? 'active' : ''; ?>">
                <a href="discounts.php">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 19l7-7 3 3-7 7-3-3z"></path>
                        <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path>
                        <path d="M2 2l7.586 7.586"></path>
                        <circle cx="11" cy="11" r="2"></circle>
                    </svg>
                    <span>Discounts</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($auth->hasPermission('view_reports')): ?>
            <li class="nav-item">
                <a href="reports.php">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <span>Reports</span>
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a href="../logout.php">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>