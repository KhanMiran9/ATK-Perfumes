<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';

$auth = new Auth();
$database = new Database();
$conn = $database->getConnection();

// Redirect if not logged in
if (!$auth->isLoggedIn()) {
    $_SESSION['redirect_to'] = 'checkout.php';
    header('Location: login.php');
    exit();
}

// Get cart items
$cartItems = [];
$cartTotal = 0;

$query = "SELECT ci.*, p.name, pv.sku_attributes_json, pv.price, pv.sale_price
          FROM cart_items ci
          JOIN products p ON ci.product_id = p.id
          JOIN product_variations pv ON ci.variation_id = pv.id
          WHERE ci.user_id = :user_id";
          
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
foreach ($cartItems as $item) {
    $price = $item['sale_price'] ?: $item['price'];
    $cartTotal += $price * $item['quantity'];
}

// Redirect if cart is empty
if (empty($cartItems)) {
    header('Location: cart.php');
    exit();
}

// Get user details
$userQuery = "SELECT * FROM users WHERE id = :user_id";
$userStmt = $conn->prepare($userQuery);
$userStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$userStmt->execute();
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shippingAddress = [
        'first_name' => sanitizeInput($_POST['shipping_first_name']),
        'last_name' => sanitizeInput($_POST['shipping_last_name']),
        'email' => sanitizeInput($_POST['shipping_email']),
        'phone' => sanitizeInput($_POST['shipping_phone']),
        'address' => sanitizeInput($_POST['shipping_address']),
        'city' => sanitizeInput($_POST['shipping_city']),
        'state' => sanitizeInput($_POST['shipping_state']),
        'zip' => sanitizeInput($_POST['shipping_zip']),
        'country' => sanitizeInput($_POST['shipping_country'])
    ];
    
    $billingAddress = [];
    if (isset($_POST['billing_same']) && $_POST['billing_same'] === 'on') {
        $billingAddress = $shippingAddress;
    } else {
        $billingAddress = [
            'first_name' => sanitizeInput($_POST['billing_first_name']),
            'last_name' => sanitizeInput($_POST['billing_last_name']),
            'email' => sanitizeInput($_POST['billing_email']),
            'phone' => sanitizeInput($_POST['billing_phone']),
            'address' => sanitizeInput($_POST['billing_address']),
            'city' => sanitizeInput($_POST['billing_city']),
            'state' => sanitizeInput($_POST['billing_state']),
            'zip' => sanitizeInput($_POST['billing_zip']),
            'country' => sanitizeInput($_POST['billing_country'])
        ];
    }
    
    $paymentMethod = sanitizeInput($_POST['payment_method']);
    $shippingMethod = sanitizeInput($_POST['shipping_method']);
    
    // Calculate shipping cost
    $shippingCost = $shippingMethod === 'express' ? 15.00 : 10.00;
    
    // Calculate tax (8%)
    $tax = $cartTotal * 0.08;
    
    // Create order
    try {
        $conn->beginTransaction();
        
        // Generate order number
        $orderNumber = 'ORD' . date('Ymd') . strtoupper(uniqid());
        
        // Insert order
        $orderQuery = "INSERT INTO orders (user_id, order_number, total_amount, shipping_amount, 
                         discount_amount, status, payment_method, shipping_address_json, billing_address_json)
                      VALUES (:user_id, :order_number, :total_amount, :shipping_amount, 
                         :discount_amount, 'pending', :payment_method, :shipping_address, :billing_address)";
        
        $orderStmt = $conn->prepare($orderQuery);
        $orderStmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':order_number' => $orderNumber,
            ':total_amount' => $cartTotal + $shippingCost + $tax,
            ':shipping_amount' => $shippingCost,
            ':discount_amount' => 0,
            ':payment_method' => $paymentMethod,
            ':shipping_address' => json_encode($shippingAddress),
            ':billing_address' => json_encode($billingAddress)
        ]);
        
        $orderId = $conn->lastInsertId();
        
        // Insert order items
        $orderItemQuery = "INSERT INTO order_items (order_id, product_id, variation_id, 
                            product_name, quantity, unit_price, total_price)
                          VALUES (:order_id, :product_id, :variation_id, 
                            :product_name, :quantity, :unit_price, :total_price)";
        
        $orderItemStmt = $conn->prepare($orderItemQuery);
        
        foreach ($cartItems as $item) {
            $price = $item['sale_price'] ?: $item['price'];
            $totalPrice = $price * $item['quantity'];
            
            $orderItemStmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $item['product_id'],
                ':variation_id' => $item['variation_id'],
                ':product_name' => $item['name'],
                ':quantity' => $item['quantity'],
                ':unit_price' => $price,
                ':total_price' => $totalPrice
            ]);
            
            // Update inventory
            $inventoryQuery = "UPDATE product_variations SET stock = stock - :quantity 
                              WHERE id = :variation_id";
            $inventoryStmt = $conn->prepare($inventoryQuery);
            $inventoryStmt->execute([
                ':quantity' => $item['quantity'],
                ':variation_id' => $item['variation_id']
            ]);
            
            // Log inventory change
            $logQuery = "INSERT INTO inventory_logs (product_id, variation_id, change_qty, reason, changed_by_user_id)
                        VALUES (:product_id, :variation_id, :change_qty, 'sale', :user_id)";
            $logStmt = $conn->prepare($logQuery);
            $logStmt->execute([
                ':product_id' => $item['product_id'],
                ':variation_id' => $item['variation_id'],
                ':change_qty' => -$item['quantity'],
                ':user_id' => $_SESSION['user_id']
            ]);
        }
        
        // Clear cart
        $clearCartQuery = "DELETE FROM cart_items WHERE user_id = :user_id";
        $clearCartStmt = $conn->prepare($clearCartQuery);
        $clearCartStmt->execute([':user_id' => $_SESSION['user_id']]);
        
        $conn->commit();
        
        // Redirect to order confirmation
        header('Location: order-confirmation.php?order_id=' . $orderId);
        exit();
        
    } catch (Exception $e) {
        $conn->rollBack();
        $error = "Order processing failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | LuxePerfume</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="checkout-page">
        <div class="container">
            <h1>Checkout</h1>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="checkout-form">
                <div class="checkout-layout">
                    <div class="checkout-main">
                        <!-- Shipping Address -->
                        <div class="checkout-section">
                            <h2>Shipping Address</h2>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="shipping_first_name">First Name *</label>
                                    <input type="text" id="shipping_first_name" name="shipping_first_name" 
                                           value="<?php echo $user['name'] ? explode(' ', $user['name'])[0] : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="shipping_last_name">Last Name *</label>
                                    <input type="text" id="shipping_last_name" name="shipping_last_name" 
                                           value="<?php echo $user['name'] ? (explode(' ', $user['name'])[1] ?? '') : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="shipping_email">Email *</label>
                                    <input type="email" id="shipping_email" name="shipping_email" 
                                           value="<?php echo $user['email']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="shipping_phone">Phone *</label>
                                    <input type="tel" id="shipping_phone" name="shipping_phone" 
                                           value="<?php echo $user['phone']; ?>" required>
                                </div>
                                <div class="form-group full-width">
                                    <label for="shipping_address">Address *</label>
                                    <input type="text" id="shipping_address" name="shipping_address" 
                                           value="<?php echo $user['address']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="shipping_city">City *</label>
                                    <input type="text" id="shipping_city" name="shipping_city" required>
                                </div>
                                <div class="form-group">
                                    <label for="shipping_state">State *</label>
                                    <input type="text" id="shipping_state" name="shipping_state" required>
                                </div>
                                <div class="form-group">
                                    <label for="shipping_zip">ZIP Code *</label>
                                    <input type="text" id="shipping_zip" name="shipping_zip" required>
                                </div>
                                <div class="form-group">
                                    <label for="shipping_country">Country *</label>
                                    <select id="shipping_country" name="shipping_country" required>
                                        <option value="US">United States</option>
                                        <option value="UK">United Kingdom</option>
                                        <option value="CA">Canada</option>
                                        <option value="AU">Australia</option>
                                        <!-- Add more countries as needed -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Address -->
                        <div class="checkout-section">
                            <h2>Billing Address</h2>
                            <div class="form-check">
                                <input type="checkbox" id="billing_same" name="billing_same" checked>
                                <label for="billing_same">Same as shipping address</label>
                            </div>
                            
                            <div id="billing-address-fields" style="display: none;">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="billing_first_name">First Name *</label>
                                        <input type="text" id="billing_first_name" name="billing_first_name">
                                    </div>
                                    <div class="form-group">
                                        <label for="billing_last_name">Last Name *</label>
                                        <input type="text" id="billing_last_name" name="billing_last_name">
                                    </div>
                                    <div class="form-group">
                                        <label for="billing_email">Email *</label>
                                        <input type="email" id="billing_email" name="billing_email">
                                    </div>
                                    <div class="form-group">
                                        <label for="billing_phone">Phone *</label>
                                        <input type="tel" id="billing_phone" name="billing_phone">
                                    </div>
                                    <div class="form-group full-width">
                                        <label for="billing_address">Address *</label>
                                        <input type="text" id="billing_address" name="billing_address">
                                    </div>
                                    <div class="form-group">
                                        <label for="billing_city">City *</label>
                                        <input type="text" id="billing_city" name="billing_city">
                                    </div>
                                    <div class="form-group">
                                        <label for="billing_state">State *</label>
                                        <input type="text" id="billing_state" name="billing_state">
                                    </div>
                                    <div class="form-group">
                                        <label for="billing_zip">ZIP Code *</label>
                                        <input type="text" id="billing_zip" name="billing_zip">
                                    </div>
                                    <div class="form-group">
                                        <label for="billing_country">Country *</label>
                                        <select id="billing_country" name="billing_country">
                                            <option value="US">United States</option>
                                            <option value="UK">United Kingdom</option>
                                            <option value="CA">Canada</option>
                                            <option value="AU">Australia</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="checkout-section">
                            <h2>Shipping Method</h2>
                            <div class="shipping-options">
                                <label class="shipping-option">
                                    <input type="radio" name="shipping_method" value="standard" checked>
                                    <div class="option-content">
                                        <span class="option-name">Standard Shipping</span>
                                        <span class="option-price">$10.00</span>
                                        <span class="option-desc">3-5 business days</span>
                                    </div>
                                </label>
                                <label class="shipping-option">
                                    <input type="radio" name="shipping_method" value="express">
                                    <div class="option-content">
                                        <span class="option-name">Express Shipping</span>
                                        <span class="option-price">$15.00</span>
                                        <span class="option-desc">1-2 business days</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="checkout-section">
                            <h2>Payment Method</h2>
                            <div class="payment-options">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="credit_card" checked>
                                    <span>Credit Card</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="paypal">
                                    <span>PayPal</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="stripe">
                                    <span>Stripe</span>
                                </label>
                            </div>
                            
                            <!-- Credit Card Form (shown when credit card is selected) -->
                            <div id="credit-card-form" class="payment-form">
                                <div class="form-grid">
                                    <div class="form-group full-width">
                                        <label for="card_number">Card Number *</label>
                                        <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456">
                                    </div>
                                    <div class="form-group">
                                        <label for="card_name">Name on Card *</label>
                                        <input type="text" id="card_name" name="card_name">
                                    </div>
                                    <div class="form-group">
                                        <label for="card_expiry">Expiry Date *</label>
                                        <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/YY">
                                    </div>
                                    <div class="form-group">
                                        <label for="card_cvv">CVV *</label>
                                        <input type="text" id="card_cvv" name="card_cvv" placeholder="123">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-sidebar">
                        <div class="order-summary">
                            <h3>Order Summary</h3>
                            
                            <div class="order-items">
                                <?php foreach ($cartItems as $item): 
                                    $price = $item['sale_price'] ?: $item['price'];
                                    $itemTotal = $price * $item['quantity'];
                                    $attributes = json_decode($item['sku_attributes_json'], true);
                                ?>
                                    <div class="order-item">
                                        <div class="item-image">
                                            <img src="<?php echo $item['image'] ? 'assets/uploads/products/' . $item['image'] : 'assets/images/placeholder.jpg'; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        </div>
                                        <div class="item-details">
                                            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                            <?php if ($attributes): ?>
                                                <p><?php echo implode(' / ', $attributes); ?></p>
                                            <?php endif; ?>
                                            <p>Qty: <?php echo $item['quantity']; ?></p>
                                        </div>
                                        <div class="item-price">
                                            $<?php echo number_format($itemTotal, 2); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="order-totals">
                                <div class="total-row">
                                    <span>Subtotal</span>
                                    <span>$<?php echo number_format($cartTotal, 2); ?></span>
                                </div>
                                <div class="total-row">
                                    <span>Shipping</span>
                                    <span id="shipping-cost">$10.00</span>
                                </div>
                                <div class="total-row">
                                    <span>Tax</span>
                                    <span>$<?php echo number_format($cartTotal * 0.08, 2); ?></span>
                                </div>
                                <div class="total-row grand-total">
                                    <span>Total</span>
                                    <span id="order-total">$<?php echo number_format($cartTotal + 10 + ($cartTotal * 0.08), 2); ?></span>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-place-order">Place Order</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/checkout.js"></script>
</body>
</html>