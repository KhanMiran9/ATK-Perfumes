<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';

$auth = new Auth();
$database = new Database();
$conn = $database->getConnection();

// Get cart items
$cartItems = [];
$cartTotal = 0;
$cartCount = 0;

if ($auth->isLoggedIn()) {
    // Get cart from database for logged-in user
    $query = "SELECT ci.*, p.name, p.slug, pv.sku_attributes_json, pm.file_path as image,
                     pv.price, pv.sale_price, pv.stock
              FROM cart_items ci
              JOIN products p ON ci.product_id = p.id
              JOIN product_variations pv ON ci.variation_id = pv.id
              LEFT JOIN product_media pm ON p.id = pm.product_id AND pm.sort_order = 0
              WHERE ci.user_id = :user_id";
              
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Get cart from session for guest users
    $cartItems = $_SESSION['cart'] ?? [];
    
    // If we have session cart items, get their details from database
    if (!empty($cartItems)) {
        $itemIds = array_column($cartItems, 'variation_id');
        $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
        
        $query = "SELECT pv.*, p.name, p.slug, pm.file_path as image
                  FROM product_variations pv
                  JOIN products p ON pv.product_id = p.id
                  LEFT JOIN product_media pm ON p.id = pm.product_id AND pm.sort_order = 0
                  WHERE pv.id IN ($placeholders)";
                  
        $stmt = $conn->prepare($query);
        $stmt->execute($itemIds);
        $variationDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Merge session cart with product details
        foreach ($cartItems as &$item) {
            foreach ($variationDetails as $detail) {
                if ($detail['id'] == $item['variation_id']) {
                    $item = array_merge($item, $detail);
                    break;
                }
            }
        }
    }
}

// Calculate totals
foreach ($cartItems as $item) {
    $price = $item['sale_price'] ?: $item['price'];
    $cartTotal += $price * $item['quantity'];
    $cartCount += $item['quantity'];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = $_POST['product_id'] ?? '';
    $variationId = $_POST['variation_id'] ?? '';
    $quantity = $_POST['quantity'] ?? 1;
    
    if ($action === 'update') {
        // Update cart quantity
        foreach ($cartItems as &$item) {
            if ($item['product_id'] == $productId && $item['variation_id'] == $variationId) {
                $item['quantity'] = max(1, (int)$quantity);
                break;
            }
        }
        
        // Save updated cart
        if ($auth->isLoggedIn()) {
            // Update database
            $updateQuery = "UPDATE cart_items SET quantity = :quantity 
                            WHERE user_id = :user_id AND product_id = :product_id AND variation_id = :variation_id";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->execute([
                ':quantity' => $quantity,
                ':user_id' => $_SESSION['user_id'],
                ':product_id' => $productId,
                ':variation_id' => $variationId
            ]);
        } else {
            // Update session
            $_SESSION['cart'] = $cartItems;
        }
        
    } elseif ($action === 'remove') {
        // Remove item from cart
        $cartItems = array_filter($cartItems, function($item) use ($productId, $variationId) {
            return !($item['product_id'] == $productId && $item['variation_id'] == $variationId);
        });
        
        // Save updated cart
        if ($auth->isLoggedIn()) {
            // Remove from database
            $deleteQuery = "DELETE FROM cart_items 
                            WHERE user_id = :user_id AND product_id = :product_id AND variation_id = :variation_id";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':product_id' => $productId,
                ':variation_id' => $variationId
            ]);
        } else {
            // Update session
            $_SESSION['cart'] = $cartItems;
        }
    }
    
    // Recalculate totals
    $cartTotal = 0;
    $cartCount = 0;
    foreach ($cartItems as $item) {
        $price = $item['sale_price'] ?: $item['price'];
        $cartTotal += $price * $item['quantity'];
        $cartCount += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | LuxePerfume</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="cart-page">
        <div class="container">
            <h1>Shopping Cart</h1>
            
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <div class="empty-cart-content">
                        <h2>Your cart is empty</h2>
                        <p>Discover our luxury fragrances and find your perfect scent</p>
                        <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="cart-layout">
                    <div class="cart-items">
                        <div class="cart-header">
                            <div class="header-product">Product</div>
                            <div class="header-price">Price</div>
                            <div class="header-quantity">Quantity</div>
                            <div class="header-total">Total</div>
                            <div class="header-actions">Actions</div>
                        </div>
                        
                        <?php foreach ($cartItems as $item): 
                            $currentPrice = $item['sale_price'] ?: $item['price'];
                            $itemTotal = $currentPrice * $item['quantity'];
                            $attributes = json_decode($item['sku_attributes_json'], true);
                        ?>
                            <div class="cart-item" data-product-id="<?php echo $item['product_id']; ?>" 
                                 data-variation-id="<?php echo $item['variation_id']; ?>">
                                <div class="item-product">
                                    <img src="<?php echo $item['image'] ? 'assets/uploads/products/' . $item['image'] : 'assets/images/placeholder.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <div class="item-details">
                                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <?php if ($attributes): ?>
                                            <p class="item-variation">
                                                <?php echo implode(' / ', $attributes); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="item-price">
                                    $<?php echo number_format($currentPrice, 2); ?>
                                </div>
                                
                                <div class="item-quantity">
                                    <div class="quantity-controls">
                                        <button class="quantity-btn minus" onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo $item['variation_id']; ?>, -1)">-</button>
                                        <input type="number" class="quantity-input" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" max="<?php echo $item['stock']; ?>"
                                               onchange="updateQuantityInput(<?php echo $item['product_id']; ?>, <?php echo $item['variation_id']; ?>, this.value)">
                                        <button class="quantity-btn plus" onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo $item['variation_id']; ?>, 1)">+</button>
                                    </div>
                                </div>
                                
                                <div class="item-total">
                                    $<?php echo number_format($itemTotal, 2); ?>
                                </div>
                                
                                <div class="item-actions">
                                    <button class="remove-btn" 
                                            onclick="removeFromCart(<?php echo $item['product_id']; ?>, <?php echo $item['variation_id']; ?>)">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary">
                        <div class="summary-card">
                            <h3>Order Summary</h3>
                            
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span>$<?php echo number_format($cartTotal, 2); ?></span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span>$10.00</span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Tax</span>
                                <span>$<?php echo number_format($cartTotal * 0.08, 2); ?></span>
                            </div>
                            
                            <div class="summary-divider"></div>
                            
                            <div class="summary-row total">
                                <span>Total</span>
                                <span>$<?php echo number_format($cartTotal + 10 + ($cartTotal * 0.08), 2); ?></span>
                            </div>
                            
                            <a href="checkout.php" class="btn btn-primary btn-checkout">Proceed to Checkout</a>
                            <a href="shop.php" class="btn btn-secondary">Continue Shopping</a>
                            
                            <div class="promo-code">
                                <h4>Promo Code</h4>
                                <div class="promo-form">
                                    <input type="text" placeholder="Enter code">
                                    <button class="btn btn-secondary">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/cart.js"></script>
</body>
</html>