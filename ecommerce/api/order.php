<?php 
require_once '../include/auth_guard.php'; 
require_once '../include/db.php';

// Initialize variables
$orders = [];
$selectedOrder = null;
$orderItems = [];
$statusMessage = '';

// Handle status update (if admin functionality is needed later)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['new_status'];
    
    // Validate status
    $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($newStatus, $validStatuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$newStatus, $orderId, $userId])) {
            $statusMessage = 'Order status updated successfully.';
        } else {
            $statusMessage = 'Failed to update order status.';
        }
    }
}

// Handle order cancellation
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $orderId = (int)$_GET['cancel'];
    
    // Only allow cancellation of pending orders
    $stmt = $pdo->prepare("UPDATE orders SET order_status = 'cancelled', updated_at = NOW() 
                           WHERE id = ? AND user_id = ? AND order_status = 'pending'");
    if ($stmt->execute([$orderId, $userId])) {
        $statusMessage = 'Order cancelled successfully.';
    } else {
        $statusMessage = 'Unable to cancel order. Order may have already been processed.';
    }
}

// Fetch user's orders
$stmt = $pdo->prepare("SELECT id, full_name, email, phone, address, city, zip_code, payment_method, 
                              total_amount, order_status, created_at, updated_at
                       FROM orders 
                       WHERE user_id = ? 
                       ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get specific order details if requested
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $orderId = (int)$_GET['view'];
    
    // Get order details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $userId]);
    $selectedOrder = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($selectedOrder) {
        // Get order items
        $stmt = $pdo->prepare("SELECT product_name, price, quantity, total FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Helper function to get status badge class
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending': return 'bg-warning text-dark';
        case 'processing': return 'bg-info text-white';
        case 'shipped': return 'bg-primary text-white';
        case 'delivered': return 'bg-success text-white';
        case 'cancelled': return 'bg-danger text-white';
        default: return 'bg-secondary text-white';
    }
}

// Helper function to format status text
function formatStatus($status) {
    return ucfirst(str_replace('_', ' ', $status));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - MyShop</title>
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
            <a href="../logout.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <!-- Navigation Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../public/product.php" class="text-decoration-none">Shop</a></li>
            <li class="breadcrumb-item"><a href="cart.php" class="text-decoration-none">Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Orders</li>
        </ol>
    </nav>

    <?php if ($statusMessage): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i><?= htmlspecialchars($statusMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($selectedOrder): ?>
        <!-- Order Details View -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-receipt me-2 text-primary"></i>Order #<?= $selectedOrder['id'] ?>
                            </h4>
                            <small class="text-muted">Placed on <?= date('F j, Y \a\t g:i A', strtotime($selectedOrder['created_at'])) ?></small>
                        </div>
                        <div>
                            <span class="badge <?= getStatusBadgeClass($selectedOrder['order_status']) ?> fs-6 px-3 py-2 me-2">
                                <?= formatStatus($selectedOrder['order_status']) ?>
                            </span>
                            <a href="orders.php" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back to Orders
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Order Information -->
                            <div class="col-md-6 mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-shipping-fast me-2"></i>Shipping Information
                                </h6>
                                <address class="mb-0">
                                    <strong><?= htmlspecialchars($selectedOrder['full_name']) ?></strong><br>
                                    <?= htmlspecialchars($selectedOrder['address']) ?><br>
                                    <?= htmlspecialchars($selectedOrder['city']) ?>, <?= htmlspecialchars($selectedOrder['zip_code']) ?><br>
                                    <i class="fas fa-phone me-1"></i><?= htmlspecialchars($selectedOrder['phone']) ?><br>
                                    <i class="fas fa-envelope me-1"></i><?= htmlspecialchars($selectedOrder['email']) ?>
                                </address>
                            </div>
                            
                            <!-- Payment Information -->
                            <div class="col-md-6 mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-credit-card me-2"></i>Payment Information
                                </h6>
                                <p class="mb-2">
                                    <strong>Payment Method:</strong> <?= formatStatus($selectedOrder['payment_method']) ?>
                                </p>
                                <p class="mb-2">
                                    <strong>Total Amount:</strong> 
                                    <span class="h5 text-success">$<?= number_format($selectedOrder['total_amount'], 2) ?></span>
                                </p>
                                <?php if ($selectedOrder['updated_at']): ?>
                                <p class="mb-0">
                                    <small class="text-muted">Last updated: <?= date('F j, Y \a\t g:i A', strtotime($selectedOrder['updated_at'])) ?></small>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Order Items -->
                        <hr class="my-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-shopping-bag me-2"></i>Order Items
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($item['product_name']) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark"><?= htmlspecialchars($item['quantity']) ?></span>
                                        </td>
                                        <td class="text-end">$<?= number_format($item['price'], 2) ?></td>
                                        <td class="text-end fw-semibold">$<?= number_format($item['total'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Total Amount:</th>
                                        <th class="text-end text-success">$<?= number_format($selectedOrder['total_amount'], 2) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <!-- Order Actions -->
                        <?php if ($selectedOrder['order_status'] === 'pending'): ?>
                        <div class="alert alert-warning mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Order Pending:</strong> You can still cancel this order if needed.
                                </div>
                                <a href="?cancel=<?= $selectedOrder['id'] ?>" 
                                   class="btn btn-outline-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to cancel this order?');">
                                    <i class="fas fa-times me-2"></i>Cancel Order
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Orders List View -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-list-alt me-2 text-primary"></i>My Orders
                        </h4>
                        <div>
                            <span class="badge bg-primary"><?= count($orders) ?> Order<?= count($orders) !== 1 ? 's' : '' ?></span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <?php if (count($orders) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Payment</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">#<?= $order['id'] ?></span>
                                            </td>
                                            <td>
                                                <div><?= date('M j, Y', strtotime($order['created_at'])) ?></div>
                                                <small class="text-muted"><?= date('g:i A', strtotime($order['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                // Get item count for this order
                                                $stmt = $pdo->prepare("SELECT COUNT(*) as item_count FROM order_items WHERE order_id = ?");
                                                $stmt->execute([$order['id']]);
                                                $itemCount = $stmt->fetch(PDO::FETCH_ASSOC)['item_count'];
                                                ?>
                                                <span class="badge bg-light text-dark"><?= $itemCount ?> item<?= $itemCount !== 1 ? 's' : '' ?></span>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-success">$<?= number_format($order['total_amount'], 2) ?></span>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= formatStatus($order['payment_method']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge <?= getStatusBadgeClass($order['order_status']) ?>">
                                                    <?= formatStatus($order['order_status']) ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="?view=<?= $order['id'] ?>" class="btn btn-outline-primary btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($order['order_status'] === 'pending'): ?>
                                                    <a href="?cancel=<?= $order['id'] ?>" 
                                                       class="btn btn-outline-danger btn-sm" 
                                                       title="Cancel Order"
                                                       onclick="return confirm('Are you sure you want to cancel this order?');">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-shopping-bag text-muted" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="text-muted mb-3">No Orders Found</h5>
                                <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping to see your orders here.</p>
                                <a href="../public/product.php" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Quick Actions -->
    <?php if (!$selectedOrder && count($orders) > 0): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body text-center py-3">
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="../public/product.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-shopping-cart me-2"></i>Continue Shopping
                        </a>
                        <a href="cart.php" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-shopping-bag me-2"></i>View Cart
                        </a>
                        <a href="checkout.php" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-credit-card me-2"></i>Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
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

    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });

    // Confirmation dialogs with better UX
    document.querySelectorAll('a[onclick*="confirm"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.getAttribute('title') || 'perform this action';
            if (confirm(`Are you sure you want to ${action.toLowerCase()}?`)) {
                window.location.href = this.href;
            }
        });
    });
</script>

</body>
</html>