<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "SELECT p.id, p.name, p.slug, p.short_desc, 
                     pv.price, pv.sale_price, pm.file_path as image
              FROM products p
              LEFT JOIN product_variations pv ON p.id = pv.product_id AND pv.is_default = 1
              LEFT JOIN product_media pm ON p.id = pm.product_id AND pm.sort_order = 0
              WHERE p.is_active = 1
              ORDER BY p.created_at DESC
              LIMIT 8";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['image']) {
            $row['image'] = '../assets/uploads/products/' . $row['image'];
        } else {
            $row['image'] = '../assets/images/placeholder.jpg';
        }
        $products[] = $row;
    }
    
    echo json_encode(['success' => true, 'products' => $products]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>