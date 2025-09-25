<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$auth = new Auth();
$database = new Database();
$pdo = $database->getConnection();

// Check permissions
if (!$auth->hasPermission('manage_roles')) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!validateCsrfToken($csrf_token)) {
        $error = 'Invalid CSRF token.';
    } else {
        // Add new role
        if (isset($_POST['add_role'])) {
            $role_name = sanitizeInput($_POST['role_name']);
            
            if (empty($role_name)) {
                $error = 'Role name is required.';
            } else {
                try {
                    $stmt = $pdo->prepare("INSERT INTO roles (name) VALUES (?)");
                    $stmt->execute([$role_name]);
                    $success = 'Role added successfully!';
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) { // Duplicate entry
                        $error = 'Role name already exists.';
                    } else {
                        $error = "Error adding role: " . $e->getMessage();
                    }
                }
            }
        }
        // Update role permissions
        elseif (isset($_POST['update_permissions'])) {
            $role_id = intval($_POST['role_id']);
            $permissions = $_POST['permissions'] ?? [];
            
            try {
                // Begin transaction
                $pdo->beginTransaction();
                
                // Remove existing permissions
                $stmt = $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?");
                $stmt->execute([$role_id]);
                
                // Add new permissions
                $stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
                foreach ($permissions as $permission_id) {
                    $stmt->execute([$role_id, intval($permission_id)]);
                }
                
                $pdo->commit();
                $success = 'Permissions updated successfully!';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "Error updating permissions: " . $e->getMessage();
            }
        }
        // Delete role
        elseif (isset($_POST['delete_role'])) {
            $role_id = intval($_POST['role_id']);
            
            // Check if role is in use
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role_id = ?");
            $stmt->execute([$role_id]);
            $user_count = $stmt->fetchColumn();
            
            if ($user_count > 0) {
                $error = 'Cannot delete role that is assigned to users.';
            } else {
                try {
                    $stmt = $pdo->prepare("DELETE FROM roles WHERE id = ?");
                    $stmt->execute([$role_id]);
                    $success = 'Role deleted successfully!';
                } catch (PDOException $e) {
                    $error = "Error deleting role: " . $e->getMessage();
                }
            }
        }
    }
}

// Fetch data
$roles = [];
$permissions = [];
$role_permissions = [];

try {
    // Fetch all roles
    $stmt = $pdo->query("SELECT * FROM roles ORDER BY name");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch all permissions
    $stmt = $pdo->query("SELECT * FROM permissions ORDER BY name");
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch role permissions mapping
    $stmt = $pdo->query("SELECT role_id, permission_id FROM role_permissions");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $role_permissions[$row['role_id']][] = $row['permission_id'];
    }
} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}

$csrf_token = generateCsrfToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles & Permissions | LuxePerfume Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/admin-sidebar.php'; ?>
        
        <div class="admin-content">
            <header class="admin-header">
                <h1><i class="fas fa-shield-alt"></i> Roles & Permissions</h1>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openAddRoleModal()">
                        <i class="fas fa-plus"></i> Add Role
                    </button>
                </div>
            </header>

            <main class="admin-main">
                <div class="container-fluid">
                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-list"></i> System Roles</h2>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Role Name</th>
                                            <th>Users</th>
                                            <th>Permissions</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($roles as $role): ?>
                                            <?php
                                            // Count users with this role
                                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role_id = ?");
                                            $stmt->execute([$role['id']]);
                                            $user_count = $stmt->fetchColumn();
                                            
                                            // Get permissions for this role
                                            $role_perms = $role_permissions[$role['id']] ?? [];
                                            $perm_count = count($role_perms);
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars(ucfirst($role['name'])); ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $user_count > 0 ? 'badge-primary' : 'badge-secondary'; ?>">
                                                        <?php echo $user_count; ?> users
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning"><?php echo $perm_count; ?> permissions</span>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($role['created_at'])); ?></td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button class="btn btn-sm btn-primary" onclick="openPermissionsModal(<?php echo $role['id']; ?>)">
                                                            <i class="fas fa-key"></i> Permissions
                                                        </button>
                                                        <?php if ($role['name'] !== 'superadmin' && $user_count === 0): ?>
                                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                                <input type="hidden" name="role_id" value="<?php echo $role['id']; ?>">
                                                                <button type="submit" name="delete_role" class="btn btn-sm btn-danger">
                                                                    <i class="fas fa-trash"></i> Delete
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-list-check"></i> Available Permissions</h2>
                        </div>
                        <div class="card-body">
                            <div class="permissions-grid">
                                <?php foreach ($permissions as $permission): ?>
                                    <div class="permission-card">
                                        <h4>
                                            <i class="fas fa-check-circle text-gold"></i>
                                            <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $permission['name']))); ?>
                                        </h4>
                                        <p><?php echo htmlspecialchars($permission['description']); ?></p>
                                        <div class="permission-roles">
                                            <strong>Assigned to:</strong>
                                            <?php
                                            $assigned_roles = [];
                                            foreach ($roles as $role) {
                                                if (in_array($permission['id'], $role_permissions[$role['id']] ?? [])) {
                                                    $assigned_roles[] = $role['name'];
                                                }
                                            }
                                            echo $assigned_roles ? implode(', ', $assigned_roles) : 'No roles';
                                            ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div id="addRoleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus"></i> Add New Role</h3>
                <span class="close" onclick="closeAddRoleModal()">&times;</span>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="role_name">Role Name</label>
                        <input type="text" id="role_name" name="role_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAddRoleModal()">Cancel</button>
                    <button type="submit" name="add_role" class="btn btn-primary">Add Role</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Permissions Modal -->
    <div id="permissionsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-key"></i> Manage Permissions</h3>
                <span class="close" onclick="closePermissionsModal()">&times;</span>
            </div>
            <form method="POST" id="permissionsForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="role_id" id="modalRoleId">
                <div class="modal-body">
                    <div id="permissionsList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closePermissionsModal()">Cancel</button>
                    <button type="submit" name="update_permissions" class="btn btn-primary">Save Permissions</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Modal functions
    function openAddRoleModal() {
        document.getElementById('addRoleModal').style.display = 'block';
    }
    
    function closeAddRoleModal() {
        document.getElementById('addRoleModal').style.display = 'none';
    }
    
    function openPermissionsModal(roleId) {
        document.getElementById('modalRoleId').value = roleId;
        
        // Fetch role details and permissions
        fetchRolePermissions(roleId);
        document.getElementById('permissionsModal').style.display = 'block';
    }
    
    function closePermissionsModal() {
        document.getElementById('permissionsModal').style.display = 'none';
    }
    
    function fetchRolePermissions(roleId) {
        // This would typically be an AJAX call to get role-specific data
        // For now, we'll populate with available permissions
        const permissions = <?php echo json_encode($permissions); ?>;
        const rolePermissions = <?php echo json_encode($role_permissions); ?>;
        
        let html = '<div class="permissions-checkbox-group">';
        permissions.forEach(perm => {
            const isChecked = rolePermissions[roleId] && rolePermissions[roleId].includes(parseInt(perm.id));
            html += `
                <div class="checkbox-item">
                    <label class="checkbox-label">
                        <input type="checkbox" name="permissions[]" value="${perm.id}" ${isChecked ? 'checked' : ''}>
                        <span class="checkmark"></span>
                        <div class="permission-info">
                            <strong>${perm.name.replace(/_/g, ' ')}</strong>
                            <span>${perm.description}</span>
                        </div>
                    </label>
                </div>
            `;
        });
        html += '</div>';
        
        document.getElementById('permissionsList').innerHTML = html;
    }
    
    // Close modals when clicking outside
    window.onclick = function(event) {
        const addModal = document.getElementById('addRoleModal');
        const permModal = document.getElementById('permissionsModal');
        
        if (event.target === addModal) {
            closeAddRoleModal();
        }
        if (event.target === permModal) {
            closePermissionsModal();
        }
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddRoleModal();
            closePermissionsModal();
        }
    });
    </script>
</body>
</html>