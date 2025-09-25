<?php
// toggle_wishlist.php
header('Content-Type: application/json');
session_start();
$input = json_decode(file_get_contents('php://input'), true);
if(!$input){ http_response_code(400); echo json_encode(['error'=>'No input']); exit; }

$product_id = (int)($input['product_id'] ?? 0);
$action = ($input['action'] ?? 'add');

if(!isset($_SESSION['user_id'])){
  http_response_code(401); echo json_encode(['error'=>'Login required']); exit;
}

$user_id = $_SESSION['user_id'];

// DB connect (replace credentials)
$host = 'localhost'; $db = 'perfume_store'; $user = 'dbuser'; $pass = 'dbpass'; $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try { $pdo = new PDO($dsn,$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]); } catch(Exception $e){ http_response_code(500); echo json_encode(['error'=>'DB connection']); exit; }

try {
  if($action === 'add'){
    $stmt = $pdo->prepare("INSERT IGNORE INTO wishlist (user_id, product_id, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $product_id]);
    echo json_encode(['success'=>true,'action'=>'added']);
  } else {
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ? LIMIT 1");
    $stmt->execute([$user_id, $product_id]);
    echo json_encode(['success'=>true,'action'=>'removed']);
  }
} catch(Exception $e){
  http_response_code(500); echo json_encode(['error'=>'DB error']); exit;
}
