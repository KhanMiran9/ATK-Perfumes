<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';

$auth = new Auth();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (validateCsrfToken($csrf_token)) {
        // Validate inputs
        if (empty($name) || empty($email) || empty($password)) {
            $error = 'Please fill in all required fields';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long';
        } else {
            // Attempt registration
            if ($auth->register($name, $email, $password, $phone, $address)) {
                $success = 'Registration successful! Redirecting to your account...';
                header('Refresh: 2; URL=index.php');
            } else {
                $error = 'Registration failed. Email may already be in use.';
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
    <title>Register | LuxePerfume</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="auth-section">
        <div class="container">
            <div class="auth-form">
                <h2>Create Account</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Create Account</button>
                </form>
                
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>