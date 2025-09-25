<?php
// api/get_featured_products.php
require_once '../includes/config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "
        SELECT 
            p.id,
            p.name,
            p.slug,
            p.short_desc as description,
            p.brand,
            c.name as category_name,
            pm.file_path as image_url,
            pv.price,
            pv.sale_price,
            pv.stock,
            pv.sku_attributes_json,
            GROUP_CONCAT(DISTINCT pt.tag) as tags
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN product_media pm ON p.id = pm.product_id AND pm.sort_order = 0
        LEFT JOIN product_variations pv ON p.id = pv.product_id AND pv.is_default = 1
        LEFT JOIN product_tags pt ON p.id = pt.product_id
        WHERE p.is_active = 1
        GROUP BY p.id
        ORDER BY p.created_at DESC
        LIMIT 12
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process the data
    foreach ($products as &$product) {
        $product['tags'] = $product['tags'] ? explode(',', $product['tags']) : [];
        $product['variations'] = getProductVariations($conn, $product['id']);
        $product['image_url'] = $product['image_url'] ?: 'assets/images/pexels-photo-1961791.jpeg';
    }
    
    echo json_encode($products);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch products: ' . $e->getMessage()]);
}

function getProductVariations($conn, $productId) {
    $query = "
        SELECT 
            id,
            sku,
            price,
            sale_price,
            stock,
            sku_attributes_json
        FROM product_variations 
        WHERE product_id = :product_id
        ORDER BY price ASC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':product_id', $productId);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>