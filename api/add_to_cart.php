<?php
// add_to_cart.php
header('Content-Type: application/json');
session_start();
$input = json_decode(file_get_contents('php://input'), true);
if(!$input) { http_response_code(400); echo json_encode(['error'=>'No input']); exit; }

$product_id = (int)($input['product_id'] ?? 0);
$variation_id = $input['variation_id'] ? (int)$input['variation_id'] : null;
$quantity = max(1, (int)($input['quantity'] ?? 1));

// DB config - replace with your config
$host = 'localhost'; $db = 'perfume_store'; $user = 'dbuser'; $pass = 'dbpass'; $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
  $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
  http_response_code(500); echo json_encode(['error'=>'DB connection failed']); exit;
}

// identify user / guest session
$user_id = $_SESSION['user_id'] ?? null;
$session_id = session_id();

// check if an existing cart item exists
if($user_id){
  $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ? AND (variation_id = ? OR (? IS NULL AND variation_id IS NULL)) LIMIT 1");
  $stmt->execute([$user_id, $product_id, $variation_id, $variation_id]);
  $existing = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
  $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE session_id = ? AND product_id = ? AND (variation_id = ? OR (? IS NULL AND variation_id IS NULL)) LIMIT 1");
  $stmt->execute([$session_id, $product_id, $variation_id, $variation_id]);
  $existing = $stmt->fetch(PDO::FETCH_ASSOC);
}

try {
  if($existing){
    $newQty = $existing['quantity'] + $quantity;
    $upd = $pdo->prepare("UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE id = ?");
    $upd->execute([$newQty, $existing['id']]);
  } else {
    // find current price from variations table (optional)
    $priceStmt = $pdo->prepare("SELECT price, sale_price FROM product_variations WHERE id = ? LIMIT 1");
    $priceAtAdd = 0.00;
    if($variation_id){
      $priceStmt->execute([$variation_id]);
      $v = $priceStmt->fetch(PDO::FETCH_ASSOC);
      if($v){
        $priceAtAdd = ($v['sale_price'] !== null) ? $v['sale_price'] : $v['price'];
      }
    } else {
      // fallback to product default price
      $priceAtAdd = 0.00;
    }

    $ins = $pdo->prepare("INSERT INTO cart_items (user_id, session_id, product_id, variation_id, quantity, price_at_added, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
    $ins->execute([$user_id, $session_id, $product_id, $variation_id, $quantity, $priceAtAdd]);
  }

  echo json_encode(['success'=>true]);
} catch (Exception $e) {
  http_response_code(500); echo json_encode(['error'=>'DB error']); exit;
}
