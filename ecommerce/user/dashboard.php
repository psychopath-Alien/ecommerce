<?php 
require_once '../include/auth_guard.php'; 
require_once '../include/db.php';

// Fetch recent order items for user
$stmt = $pdo->prepare("SELECT id, product_name, price, quantity, total, status 
FROM order_items 
WHERE order_id IN (SELECT id FROM orders WHERE user_id = ?) 
ORDER BY status DESC 
LIMIT 5;");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - MyShop</title>
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
            <a href="#" class="btn btn-logout" onclick="handleLogout(event)">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row g-4">
        <div class="col-12 fade-in">
            <div class="card welcome-card">
                <div class="card-body p-4">
                    <h1 class="welcome-text">Welcome!</h1>
                    <div class="user-stats">
                        <div class="stat-item">
                            <i class="fas fa-envelope stat-icon"></i>
                            <span><strong>Email:</strong> <?= htmlspecialchars($userEmail) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 fade-in">
            <div class="card feature-card orders-card">
                <div class="card-body">
                    <div class="feature-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h5 class="card-title">My Orders</h5>
                    <p class="card-text">Track your purchases and view your complete order history with detailed information.</p>
                    <a href="orders.php" class="btn btn-feature btn-orders">
                        <i class="fas fa-eye me-2"></i>View Orders
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 fade-in">
            <div class="card feature-card profile-card">
                <div class="card-body">
                    <div class="feature-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h5 class="card-title">My Profile</h5>
                    <p class="card-text">Update your personal information, preferences, and account settings easily.</p>
                    <a href="profile.php" class="btn btn-feature btn-profile">
                        <i class="fas fa-edit me-2"></i>Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 fade-in">
            <div class="card feature-card shop-card">
                <div class="card-body">
                    <div class="feature-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h5 class="card-title">Shop Now</h5>
                    <p class="card-text">Discover amazing products and exclusive deals in our curated collection.</p>
                    <button class="btn btn-feature btn-shop" onclick="window.location.href='../public/product.php'">
                        <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="row mt-4">
        <div class="col-12 fade-in">
            <div class="card orders-table">
                <div class="card-body p-4">
                    <h2 class="table-title">
                        <i class="fas fa-clock me-2"></i>Recent Order Items
                    </h2>
                    <?php if (count($orders) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag me-2"></i>Item ID</th>
                                    <th><i class="fas fa-box me-2"></i>Product Name</th>
                                    <th><i class="fas fa-dollar-sign me-2"></i>Price</th>
                                    <th><i class="fas fa-sort-numeric-up me-2"></i>Quantity</th>
                                    <th><i class="fas fa-dollar-sign me-2"></i>Total Amount</th>
                                    <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($order['id']) ?></strong></td>
                                    <td><?= htmlspecialchars($order['product_name']) ?></td>
                                    <td><span class="amount-display">$<?= number_format($order['price'], 2) ?></span></td>
                                    <td><?= htmlspecialchars($order['quantity']) ?></td>
                                    <td><span class="amount-display">$<?= number_format($order['total'], 2) ?></span></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower(htmlspecialchars($order['status'])) ?>">
                                            <?= htmlspecialchars(ucfirst($order['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="no-orders">
                        <i class="fas fa-shopping-bag"></i>
                        <h5>No Recent Order Items</h5>
                        <p>You haven't placed any orders yet. Start shopping to see your order items here!</p>
                        <a href="../public/product.php" class="btn btn-feature btn-shop mt-3">
                            <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
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
            window.location.href = '../public/logout.php';
        }, 1000);
    }

    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Add loading animation on page load
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