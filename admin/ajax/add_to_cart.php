<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

header('Content-Type: application/json');

$auth = new Auth();
$database = new Database();
$conn = $database->getConnection();

$response = ['success' => false];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    $productId = (int)$_POST['product_id'];
    $variationId = (int)$_POST['variation_id'];
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    if ($productId <= 0 || $variationId <= 0 || $quantity <= 0) {
        throw new Exception('Invalid parameters');
    }
    
    // Check if variation exists and has enough stock
    $stockQuery = "SELECT stock FROM product_variations WHERE id = ? AND product_id = ?";
    $stockStmt = $conn->prepare($stockQuery);
    $stockStmt->bind_param('ii', $variationId, $productId);
    $stockStmt->execute();
    $stockResult = $stockStmt->get_result()->fetch_assoc();
    
    if (!$stockResult) {
        throw new Exception('Product variation not found');
    }
    
    if ($stockResult['stock'] < $quantity) {
        throw new Exception('Insufficient stock');
    }
    
    if ($auth->isLoggedIn()) {
        // User is logged in - use database cart
        $userId = $_SESSION['user_id'];
        
        // Check if item already exists in cart
        $checkQuery = "SELECT id, quantity FROM cart_items 
                      WHERE user_id = ? AND product_id = ? AND variation_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param('iii', $userId, $productId, $variationId);
        $checkStmt->execute();
        $existingItem = $checkStmt->get_result()->fetch_assoc();
        
        // Get current price
        $priceQuery = "SELECT COALESCE(sale_price, price) as current_price 
                      FROM product_variations WHERE id = ?";
        $priceStmt = $conn->prepare($priceQuery);
        $priceStmt->bind_param('i', $variationId);
        $priceStmt->execute();
        $priceResult = $priceStmt->get_result()->fetch_assoc();
        $currentPrice = $priceResult['current_price'];
        
        if ($existingItem) {
            // Update existing item
            $newQuantity = $existingItem['quantity'] + $quantity;
            $updateQuery = "UPDATE cart_items SET quantity = ?, price_at_added = ?, updated_at = NOW()
                           WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('idi', $newQuantity, $currentPrice, $existingItem['id']);
            $updateStmt->execute();
        } else {
            // Add new item
            $insertQuery = "INSERT INTO cart_items (user_id, product_id, variation_id, quantity, price_at_added)
                           VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param('iiidd', $userId, $productId, $variationId, $quantity, $currentPrice);
            $insertStmt->execute();
        }
        
    } else {
        // User is not logged in - use session cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $cartItemKey = $productId . '_' . $variationId;
        
        if (isset($_SESSION['cart'][$cartItemKey])) {
            $_SESSION['cart'][$cartItemKey]['quantity'] += $quantity;
        } else {
            // Get product details for session
            $productQuery = "SELECT p.name, pv.sku_attributes_json, COALESCE(pv.sale_price, pv.price) as price
                           FROM products p
                           JOIN product_variations pv ON p.id = pv.product_id
                           WHERE p.id = ? AND pv.id = ?";
            $productStmt = $conn->prepare($productQuery);
            $productStmt->bind_param('ii', $productId, $variationId);
            $productStmt->execute();
            $productResult = $productStmt->get_result()->fetch_assoc();
            
            $_SESSION['cart'][$cartItemKey] = [
                'product_id' => $productId,
                'variation_id' => $variationId,
                'quantity' => $quantity,
                'price' => $productResult['price'],
                'name' => $productResult['name'],
                'attributes' => json_decode($productResult['sku_attributes_json'], true),
                'added_at' => time()
            ];
        }
    }
    
    $response['success'] = true;
    $response['message'] = 'Product added to cart successfully';
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>