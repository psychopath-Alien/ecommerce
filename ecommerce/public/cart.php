<?php 
require_once '../include/auth_guard.php'; 
require_once '../include/db.php';

// Handle deletion if `delete` is in query string
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];

    // Ensure the cart item belongs to the logged-in user
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$deleteId, $userId]);

    // Redirect to avoid repeated deletion on refresh
    header("Location: cart.php");
    exit;
}

// Fetch cart items
$stmt = $pdo->prepare("SELECT c.id, p.name AS product_name, p.price, c.quantity, (p.price * c.quantity) AS total
                        FROM cart c
                        JOIN products p ON c.product_id = p.id
                        WHERE c.user_id = ?");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - MyShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard-style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-store me-2"></i>MyShop
        </a>
        <div class="d-flex align-items-center">
            <div class="user-info me-3">
                <i class="fas fa-user-circle me-2"></i>
                <span><?= htmlspecialchars($userEmail) ?></span>
            </div>
            <!-- Proper Logout Link -->
            <a href="#" class="btn btn-logout" onclick="handleLogout(event)">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="col-12 fade-in">
        <div class="card orders-table">
            <div class="card-body p-4">
                <h2 class="table-title">
                    <i class="fas fa-shopping-cart me-2"></i>My Cart
                </h2>
                <?php if (count($cartItems) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td>$<?= number_format($item['total'], 2) ?></td>
                                <td>
                                    <a href="?delete=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this item?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="text-end mt-3">
                        <a href="checkout.php" class="btn btn-success">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <div class="no-orders">
                    <i class="fas fa-shopping-cart"></i>
                    <h5>Your cart is empty</h5>
                    <p>Add items to your cart to start your order.</p>
                    <a href="../public/product.php" class="btn btn-feature btn-shop mt-3">
                        <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function handleLogout(e) {
        e.preventDefault();
        const logoutBtn = document.querySelector('.btn-logout');
        logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging out...';
        logoutBtn.classList.add('disabled');
        setTimeout(() => {
            window.location.href = 'logout.php';
        }, 1000);
    }
    // Optional UI animation
    document.documentElement.style.scrollBehavior = 'smooth';
    window.addEventListener('load', () => {
        document.body.style.opacity = '0';
        setTimeout(() => {
            document.body.style.transition = 'opacity 0.5s ease-in-out';
            document.body.style.opacity = '1';
        }, 100);
    });
</script>

</body>
</html>
