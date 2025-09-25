<?php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

function getBaseUrl() {
    return BASE_URL;
}

function getUploadPath() {
    return UPLOAD_PATH;
}

function handleFileUpload($file, $targetDir = null) {
    if ($targetDir === null) {
        $targetDir = UPLOAD_PATH;
    }
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $fileName = uniqid() . '_' . basename($file['name']);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return ['success' => false, 'message' => 'File is not an image.'];
    }
    
    // Check file size (5MB max)
    if ($file['size'] > 5000000) {
        return ['success' => false, 'message' => 'File is too large.'];
    }
    
    // Allow certain file formats
    $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedFormats)) {
        return ['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed.'];
    }
    
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'file_name' => $fileName];
    } else {
        return ['success' => false, 'message' => 'Sorry, there was an error uploading your file.'];
    }
}
?>