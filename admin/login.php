<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

$auth = new Auth();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (validateCsrfToken($csrf_token)) {
        if ($auth->login($email, $password)) {
            // Redirect based on user role
            if ($auth->hasPermission('manage_roles')) {
                redirect('admin/dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            $error = 'Invalid email or password';
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
    <title>Login | LuxePerfume</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <section class="auth-section">
        <div class="container">
            <div class="auth-form">
                <h2>Login to Your Account</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
                
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </section>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>