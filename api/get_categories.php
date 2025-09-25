<?php
// api/get_categories.php
require_once '../includes/config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Updated query without the status column
    $query = "SELECT id, name, image as image_url FROM categories ORDER BY name ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($categories);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch categories: ' . $e->getMessage()]);
}
?>