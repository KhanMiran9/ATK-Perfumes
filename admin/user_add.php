<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

$auth = new Auth();
$database = new Database();
$pdo = $database->getConnection();

// Check permissions
if (!$auth->hasPermission('manage_users')) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Fetch roles for dropdown
$roles = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM roles ORDER BY name");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching roles: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role_id = intval($_POST['role_id']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $status = $_POST['status'];
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email address already exists.';
            } else {
                // Hash password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role_id, phone, address, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $password_hash, $role_id, $phone, $address, $status]);
                
                $success = 'User created successfully!';
                
                // Clear form
                $_POST = [];
            }
        } catch (PDOException $e) {
            $error = "Error creating user: " . $e->getMessage();
        }
    }
}

$csrf_token = generateCsrfToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User | LuxePerfume Admin</title>
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
                <h1><i class="fas fa-user-plus"></i> Add New User</h1>
                <div class="header-actions">
                    <a href="users.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
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
                            <h2><i class="fas fa-user-cog"></i> User Information</h2>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="form-grid">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                
                                <div class="form-group">
                                    <label for="name"><i class="fas fa-user"></i> Full Name *</label>
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email"><i class="fas fa-envelope"></i> Email Address *</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="role_id"><i class="fas fa-shield-alt"></i> Role *</label>
                                    <select id="role_id" name="role_id" required>
                                        <option value="">Select Role</option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?php echo $role['id']; ?>" <?php echo (isset($_POST['role_id']) && $_POST['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars(ucfirst($role['name'])); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="status"><i class="fas fa-circle"></i> Status *</label>
                                    <select id="status" name="status" required>
                                        <option value="active" <?php echo (isset($_POST['status']) && $_POST['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo (isset($_POST['status']) && $_POST['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        <option value="suspended" <?php echo (isset($_POST['status']) && $_POST['status'] == 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                </div>
                                
                                <div class="form-group full-width">
                                    <label for="address"><i class="fas fa-map-marker-alt"></i> Address</label>
                                    <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password"><i class="fas fa-lock"></i> Password *</label>
                                    <div class="password-toggle">
                                        <input type="password" id="password" name="password" required>
                                        <button type="button" class="toggle-password" data-target="password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password *</label>
                                    <div class="password-toggle">
                                        <input type="password" id="confirm_password" name="confirm_password" required>
                                        <button type="button" class="toggle-password" data-target="confirm_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="form-group full-width">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Create User
                                    </button>
                                    <a href="users.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2><i class="fas fa-info-circle"></i> Role Information</h2>
                        </div>
                        <div class="card-body">
                            <div class="role-info-grid">
                                <?php foreach ($roles as $role): ?>
                                    <div class="role-info-card">
                                        <h4><?php echo htmlspecialchars(ucfirst($role['name'])); ?></h4>
                                        <?php
                                        // Fetch permissions for this role
                                        $stmt = $pdo->prepare("
                                            SELECT p.name, p.description 
                                            FROM permissions p 
                                            JOIN role_permissions rp ON p.id = rp.permission_id 
                                            WHERE rp.role_id = ?
                                        ");
                                        $stmt->execute([$role['id']]);
                                        $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        ?>
                                        <ul>
                                            <?php foreach ($permissions as $perm): ?>
                                                <li><?php echo htmlspecialchars($perm['description']); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle functionality
        const toggleButtons = document.querySelectorAll('.toggle-password');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
        
        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
            
            return true;
        });
        
        // Real-time password strength indicator
        const passwordInput = document.getElementById('password');
        passwordInput.addEventListener('input', function() {
            const strengthIndicator = document.getElementById('password-strength');
            if (!strengthIndicator) {
                const indicator = document.createElement('div');
                indicator.id = 'password-strength';
                indicator.style.marginTop = '0.5rem';
                indicator.style.fontSize = '0.8rem';
                passwordInput.parentNode.appendChild(indicator);
            }
            
            const strength = checkPasswordStrength(this.value);
            const indicator = document.getElementById('password-strength');
            indicator.innerHTML = `Strength: <span style="color: ${strength.color}">${strength.text}</span>`;
        });
        
        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    return { text: 'Weak', color: '#dc3545' };
                case 2:
                    return { text: 'Fair', color: '#fd7e14' };
                case 3:
                    return { text: 'Good', color: '#ffc107' };
                case 4:
                    return { text: 'Strong', color: '#28a745' };
                default:
                    return { text: 'Weak', color: '#dc3545' };
            }
        }
    });
    </script>
</body>
</html>