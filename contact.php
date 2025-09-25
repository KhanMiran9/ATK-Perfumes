<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$database = new Database();
$conn = $database->getConnection();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (validateCsrfToken($csrf_token)) {
        // Validate inputs
        if (empty($name) || empty($email) || empty($message)) {
            $error = 'Please fill in all required fields';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address';
        } else {
            // Insert message into database
            $query = "INSERT INTO messages (name, email, subject, message) 
                      VALUES (:name, :email, :subject, :message)";
            
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':subject' => $subject,
                ':message' => $message
            ]);
            
            $success = 'Thank you for your message! We will get back to you soon.';
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
    <title>Contact Us | LuxePerfume</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="contact-page">
        <div class="container">
            <h1>Contact Us</h1>
            
            <div class="contact-layout">
                <div class="contact-info">
                    <h2>Get in Touch</h2>
                    <p>We'd love to hear from you. Please fill out the form or use the contact information below.</p>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <h3>Address</h3>
                            <p>123 Luxury Avenue<br>Fragrance District<br>New York, NY 10001</p>
                        </div>
                        
                        <div class="contact-item">
                            <h3>Phone</h3>
                            <p>+1 (555) 123-4567</p>
                        </div>
                        
                        <div class="contact-item">
                            <h3>Email</h3>
                            <p>info@luxeperfume.com</p>
                        </div>
                        
                        <div class="contact-item">
                            <h3>Hours</h3>
                            <p>Monday - Friday: 9AM - 6PM<br>Saturday: 10AM - 4PM<br>Sunday: Closed</p>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h2>Send us a Message</h2>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject">
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="map-section">
        <div class="container">
            <h2>Find Us</h2>
            <div class="map-placeholder">
                <div class="map-content">
                    <h3>LuxePerfume Store</h3>
                    <p>123 Luxury Avenue, New York, NY 10001</p>
                    <p>📍 Click to view on Google Maps</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>