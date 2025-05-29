<?php 
require_once '../include/auth_guard.php'; 
require_once '../include/db.php';

// Initialize variables
$cartItems = [];
$totalAmount = 0;
$orderPlaced = false;
$errorMessage = '';

// Fetch cart items
$stmt = $pdo->prepare("SELECT c.id, p.name AS product_name, p.price, c.quantity, (p.price * c.quantity) AS total
                        FROM cart c
                        JOIN products p ON c.product_id = p.id
                        WHERE c.user_id = ?");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total amount
foreach ($cartItems as $item) {
    $totalAmount += $item['total'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($cartItems)) {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $zipCode = trim($_POST['zip_code'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? '';
    
    // Validation
    if (empty($fullName) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($zipCode) || empty($paymentMethod)) {
        $errorMessage = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } else {
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            // Create order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, full_name, email, phone, address, city, zip_code, payment_method, total_amount, order_status, created_at) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([$userId, $fullName, $email, $phone, $address, $city, $zipCode, $paymentMethod, $totalAmount]);
            
            $orderId = $pdo->lastInsertId();
            
            // Insert order items
            foreach ($cartItems as $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, price, quantity, total) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$orderId, $item['product_name'], $item['price'], $item['quantity'], $item['total']]);
            }
            
            // Clear cart
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Commit transaction
            $pdo->commit();
            
            $orderPlaced = true;
            
        } catch (Exception $e) {
            // Rollback transaction
            $pdo->rollback();
            $errorMessage = 'An error occurred while processing your order. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - MyShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-light" style="font-family: 'Inter', sans-serif;">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="fas fa-store me-2"></i>MyShop
        </a>
        <div class="d-flex align-items-center">
            <div class="text-white me-3">
                <i class="fas fa-user-circle me-2"></i>
                <span><?= htmlspecialchars($userEmail) ?></span>
            </div>
            <a href="#" class="btn btn-logout" onclick="handleLogout(event)">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <?php if ($orderPlaced): ?>
        <!-- Order Success Message -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="text-success mb-3">Order Placed Successfully!</h2>
                        <p class="lead mb-4">Thank you for your purchase. Your order has been received and is being processed.</p>
                        <p class="text-muted mb-4">Order ID: #<?= $orderId ?></p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="../user/orders.php" class="btn btn-primary me-md-2">
                                <i class="fas fa-list me-2"></i>View My Orders
                            </a>
                            <a href="../public/product.php" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-cart me-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif (empty($cartItems)): ?>
        <!-- Empty Cart Message -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-shopping-cart text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="text-muted mb-3">Your Cart is Empty</h2>
                        <p class="lead mb-4">Add some items to your cart before proceeding to checkout.</p>
                        <a href="../public/product.php" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Checkout Form -->
        <form method="POST" id="checkoutForm">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow mb-4">
                        <div class="card-header bg-white py-3">
                            <h4 class="mb-0">
                                <i class="fas fa-shipping-fast me-2 text-primary"></i>Shipping Information
                            </h4>
                        </div>
                        <div class="card-body p-4">
                            <?php if ($errorMessage): ?>
                                <div class="alert alert-danger" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($errorMessage) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label fw-semibold">Full Name *</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold">Phone Number *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="zip_code" class="form-label fw-semibold">Zip Code *</label>
                                    <input type="text" class="form-control" id="zip_code" name="zip_code" 
                                           value="<?= htmlspecialchars($_POST['zip_code'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label fw-semibold">Street Address *</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label for="city" class="form-label fw-semibold">City *</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?= htmlspecialchars($_POST['city'] ?? '') ?>" required>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h5 class="mb-3">
                                <i class="fas fa-credit-card me-2 text-primary"></i>Payment Method
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check p-3 border rounded">
                                        <input class="form-check-input" type="radio" name="payment_method" id="credit_card" 
                                               value="credit_card" <?= ($_POST['payment_method'] ?? '') === 'credit_card' ? 'checked' : '' ?> required>
                                        <label class="form-check-label fw-semibold" for="credit_card">
                                            <i class="fas fa-credit-card me-2 text-primary"></i>Credit Card
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check p-3 border rounded">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paypal" 
                                               value="paypal" <?= ($_POST['payment_method'] ?? '') === 'paypal' ? 'checked' : '' ?> required>
                                        <label class="form-check-label fw-semibold" for="paypal">
                                            <i class="fab fa-paypal me-2 text-primary"></i>PayPal
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check p-3 border rounded">
                                        <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" 
                                               value="bank_transfer" <?= ($_POST['payment_method'] ?? '') === 'bank_transfer' ? 'checked' : '' ?> required>
                                        <label class="form-check-label fw-semibold" for="bank_transfer">
                                            <i class="fas fa-university me-2 text-primary"></i>Bank Transfer
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check p-3 border rounded">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cash_on_delivery" 
                                               value="cash_on_delivery" <?= ($_POST['payment_method'] ?? '') === 'cash_on_delivery' ? 'checked' : '' ?> required>
                                        <label class="form-check-label fw-semibold" for="cash_on_delivery">
                                            <i class="fas fa-money-bill-wave me-2 text-primary"></i>Cash on Delivery
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Order Summary -->
                    <div class="card border-0 shadow">
                        <div class="card-header bg-white py-3">
                            <h4 class="mb-0">
                                <i class="fas fa-receipt me-2 text-primary"></i>Order Summary
                            </h4>
                        </div>
                        <div class="card-body p-4">
                            <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                                    <small class="text-muted">Qty: <?= htmlspecialchars($item['quantity']) ?> × $<?= number_format($item['price'], 2) ?></small>
                                </div>
                                <span class="fw-semibold">$<?= number_format($item['total'], 2) ?></span>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Subtotal:</span>
                                <span>$<?= number_format($totalAmount, 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Shipping:</span>
                                <span class="text-success">Free</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4 pt-3 border-top">
                                <span class="fw-bold fs-5">Total:</span>
                                <span class="fw-bold fs-5 text-primary">$<?= number_format($totalAmount, 2) ?></span>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-lock me-2"></i>Place Order
                                </button>
                                <a href="cart.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Cart
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
        function handleLogout(e) {
        e.preventDefault();
        const logoutBtn = document.querySelector('.btn-logout');
        logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging out...';
        logoutBtn.classList.add('disabled');
        setTimeout(() => {
            window.location.href = '../public/logout.php';
        }, 1000);
    }
    // Form validation
    document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            e.preventDefault();
            alert('Please select a payment method.');
            return false;
        }
        
        // Additional validation for required fields
        const requiredFields = ['full_name', 'email', 'phone', 'address', 'city', 'zip_code'];
        for (let field of requiredFields) {
            const element = document.getElementById(field);
            if (!element.value.trim()) {
                e.preventDefault();
                alert(`Please fill in the ${field.replace('_', ' ')} field.`);
                element.focus();
                return false;
            }
        }
        
        // Email validation
        const email = document.getElementById('email').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address.');
            document.getElementById('email').focus();
            return false;
        }
    });

    // Smooth animations
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>

</body>
</html>