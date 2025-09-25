<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$auth = new Auth();

// Check if user is logged in and has permission (only superadmin and admin can manage users)
if (!$auth->isLoggedIn() || !$auth->hasPermission('manage_users')) {
    redirect('../login.php');
}

$database = new Database();
$conn = $database->getConnection(); // PDO expected

// Get users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {
    // Get total count for pagination
    $countStmt = $conn->query("SELECT COUNT(*) FROM users");
    $totalResult = (int) $countStmt->fetchColumn();
    $totalPages = ($totalResult > 0) ? (int)ceil($totalResult / $limit) : 1;

    // Fetch users with role name (use prepared stmt and bind ints)
    $query = "SELECT u.*, r.name as role_name 
              FROM users u 
              JOIN roles r ON u.role_id = r.id 
              ORDER BY u.created_at DESC
              LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get roles for editing
    $rolesStmt = $conn->query("SELECT * FROM roles ORDER BY id");
    $roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Handle DB error gracefully
    $users = [];
    $roles = [];
    $totalPages = 1;
    $error = 'Database error: ' . $e->getMessage();
}

$error = $error ?? '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (validateCsrfToken($csrf_token)) {
        if ($action === 'update_role') {
            $userId = (int)$_POST['user_id'];
            $roleId = (int)$_POST['role_id'];

            // Prevent users from modifying their own role or superadmin role
            if ($userId === $_SESSION['user_id']) {
                $error = 'You cannot change your own role';
            } else {
                try {
                    // Get current role_id
                    $userQuery = "SELECT role_id FROM users WHERE id = :id";
                    $userStmt = $conn->prepare($userQuery);
                    $userStmt->bindValue(':id', $userId, PDO::PARAM_INT);
                    $userStmt->execute();
                    $currentRole = $userStmt->fetch(PDO::FETCH_ASSOC);

                    if ($currentRole && (int)$currentRole['role_id'] === 1) {
                        $error = 'Cannot modify superadmin role';
                    } else {
                        $updateQuery = "UPDATE users SET role_id = :role_id WHERE id = :id";
                        $updateStmt = $conn->prepare($updateQuery);
                        $updateStmt->bindValue(':role_id', $roleId, PDO::PARAM_INT);
                        $updateStmt->bindValue(':id', $userId, PDO::PARAM_INT);

                        if ($updateStmt->execute()) {
                            $success = 'User role updated successfully!';
                        } else {
                            $errInfo = $updateStmt->errorInfo();
                            $error = 'Error updating user role: ' . ($errInfo[2] ?? 'Unknown error');
                        }
                    }
                } catch (PDOException $e) {
                    $error = 'Database error: ' . $e->getMessage();
                }
            }
        } elseif ($action === 'update_status') {
            $userId = (int)$_POST['user_id'];
            $status = sanitizeInput($_POST['status']);

            if ($userId === $_SESSION['user_id']) {
                $error = 'You cannot change your own status';
            } else {
                try {
                    $userQuery = "SELECT role_id FROM users WHERE id = :id";
                    $userStmt = $conn->prepare($userQuery);
                    $userStmt->bindValue(':id', $userId, PDO::PARAM_INT);
                    $userStmt->execute();
                    $userRole = $userStmt->fetch(PDO::FETCH_ASSOC);

                    if ($userRole && (int)$userRole['role_id'] === 1) {
                        $error = 'Cannot modify superadmin status';
                    } else {
                        $updateQuery = "UPDATE users SET status = :status WHERE id = :id";
                        $updateStmt = $conn->prepare($updateQuery);
                        $updateStmt->bindValue(':status', $status, PDO::PARAM_STR);
                        $updateStmt->bindValue(':id', $userId, PDO::PARAM_INT);

                        if ($updateStmt->execute()) {
                            $success = 'User status updated successfully!';
                        } else {
                            $errInfo = $updateStmt->errorInfo();
                            $error = 'Error updating user status: ' . ($errInfo[2] ?? 'Unknown error');
                        }
                    }
                } catch (PDOException $e) {
                    $error = 'Database error: ' . $e->getMessage();
                }
            }
        }
    } else {
        $error = 'Invalid CSRF token';
    }

    // Refresh users list after a POST action (re-run the list query)
    try {
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // roles may have changed, refresh
        $rolesStmt = $conn->query("SELECT * FROM roles ORDER BY id");
        $roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);

        // update total pages as well (in case)
        $countStmt = $conn->query("SELECT COUNT(*) FROM users");
        $totalResult = (int) $countStmt->fetchColumn();
        $totalPages = ($totalResult > 0) ? (int)ceil($totalResult / $limit) : 1;
    } catch (PDOException $e) {
        // ignore; we've already set $error earlier
    }
}

$csrf_token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | LuxePerfume Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1>Manage Users</h1>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="card">
                <h2>Users List</h2>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($users) > 0): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                            <?php if ($user['id'] === $_SESSION['user_id']): ?>
                                                <br><small class="text-muted">(You)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <?php if ($user['role_id'] == 1): ?>
                                                <span class="role-badge role-superadmin">Superadmin</span>
                                            <?php elseif ($user['role_id'] == 2): ?>
                                                <span class="role-badge role-admin">Admin</span>
                                            <?php elseif ($user['role_id'] == 3): ?>
                                                <span class="role-badge role-manager">Shop Manager</span>
                                            <?php else: ?>
                                                <span class="role-badge role-customer">Customer</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo htmlspecialchars($user['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($user['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <!-- Role Update Form -->
                                                <?php if ($user['role_id'] != 1 && $user['id'] !== $_SESSION['user_id']): ?>
                                                    <form method="POST" class="inline-form">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                        <input type="hidden" name="action" value="update_role">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <select name="role_id" onchange="this.form.submit()" class="role-select">
                                                            <?php foreach ($roles as $role): 
                                                                if ($role['id'] == 1) continue; // Skip superadmin role for assignment
                                                            ?>
                                                                <option value="<?php echo $role['id']; ?>" 
                                                                    <?php echo $user['role_id'] == $role['id'] ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($role['name']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <!-- Status Update Form -->
                                                <?php if ($user['role_id'] != 1 && $user['id'] !== $_SESSION['user_id']): ?>
                                                    <form method="POST" class="inline-form">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <select name="status" onchange="this.form.submit()" class="status-select">
                                                            <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                            <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                            <option value="suspended" <?php echo $user['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                                                        </select>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No users found</td>
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
