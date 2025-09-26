<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/config.php';

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 4;

try {
    // Create database connection
    $database = new Database();
    $pdo = $database->getConnection();
    
    $query = "SELECT p.id, p.name, p.slug, pv.price, 
                     pm1.file_path as image1, pm2.file_path as image2
              FROM products p
              LEFT JOIN product_variations pv ON p.id = pv.product_id AND pv.is_default = 1
              LEFT JOIN product_media pm1 ON p.id = pm1.product_id AND pm1.sort_order = 1
              LEFT JOIN product_media pm2 ON p.id = pm2.product_id AND pm2.sort_order = 2
              WHERE p.is_active = 1
              ORDER BY p.created_at DESC 
              LIMIT ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$limit]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the response
    $formattedProducts = [];
    foreach ($products as $product) {
        $formattedProducts[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => number_format($product['price'], 2),
            'image1' => $product['image1'] ? 'assets/uploads/' . $product['image1'] : 'assets/images/placeholder.jpg',
            'image2' => $product['image2'] ? 'assets/uploads/' . $product['image2'] : null,
            'url' => 'product.php?id=' . $product['id']
        ];
    }
    
    echo json_encode($formattedProducts);
} catch (PDOException $e) {
    // Fallback to sample data if database fails
    $fallbackProducts = [
        [
            'id' => 1,
            'name' => "Mystic Oud",
            'price' => "2,499.00",
            'image1' => "assets/images/mystic-oud-1.jpg",
            'image2' => "assets/images/mystic-oud-2.jpg",
            'url' => "product.php?id=1"
        ],
        [
            'id' => 2,
            'name' => "Noir Essence",
            'price' => "1,899.00",
            'image1' => "assets/images/noir-essence-1.jpg",
            'image2' => "assets/images/noir-essence-2.jpg",
            'url' => "product.php?id=2"
        ]
    ];
    echo json_encode($fallbackProducts);
}
?>