<?php 
require_once '../include/auth_guard.php'; 
require_once '../include/db.php';

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Build WHERE clause for filtering
$where_clause = "WHERE order_id IN (SELECT id FROM orders WHERE user_id = ?)";
$params = [$userId];

if (!empty($status_filter)) {
    $where_clause .= " AND status = ?";
    $params[] = $status_filter;
}

// Get total count for pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM order_items $where_clause");
$count_stmt->execute($params);
$total_items = $count_stmt->fetchColumn();
$total_pages = ceil($total_items / $per_page);

// Fetch order items with pagination
$allowed_sort = ['id', 'product_name', 'price', 'quantity', 'total', 'status'];
$sort_by = in_array($sort_by, $allowed_sort) ? $sort_by : 'id';
$sort_order = in_array($sort_order, ['ASC', 'DESC']) ? $sort_order : 'DESC';

$stmt = $pdo->prepare("SELECT oi.id, oi.product_name, oi.price, oi.quantity, oi.total, oi.status, 
                              o.created_at as order_date, o.id as order_id
                       FROM order_items oi 
                       JOIN orders o ON oi.order_id = o.id 
                       $where_clause 
                       ORDER BY oi.$sort_by $sort_order 
                       LIMIT $per_page OFFSET $offset");
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get status counts for filter badges
$status_stmt = $pdo->prepare("SELECT status, COUNT(*) as count 
                             FROM order_items 
                             WHERE order_id IN (SELECT id FROM orders WHERE user_id = ?) 
                             GROUP BY status");
$status_stmt->execute([$userId]);
$status_counts = $status_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
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
    <link rel="stylesheet" href="dashboard-style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-store me-2"></i>MyShop
        </a>
        <div class="d-flex align-items-center">
            <div class="user-info me-3">
                <i class="fas fa-user-circle me-2"></i>
                <span><?= htmlspecialchars($userEmail) ?></span>
            </div>
            <a href="dashboard.php" class="btn btn-outline-light me-2">
                <i class="fas fa-arrow-left me-2"></i>Dashboard
            </a>
            <a href="#" class="btn btn-logout" onclick="handleLogout(event)">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="bg-primary text-white py-5 mb-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">
                    <i class="fas fa-shopping-bag me-3"></i>My Orders
                </h1>
                <p class="mb-0 opacity-75">Track and manage all your order items</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="fs-5">
                    <strong><?= $total_items ?></strong> Total Items
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-filter me-2"></i>Filter by Status
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="?<?= http_build_query(array_merge($_GET, ['status' => '', 'page' => 1])) ?>" 
                           class="btn <?= empty($status_filter) ? 'btn-primary' : 'btn-outline-secondary' ?> btn-sm">
                            All Orders
                            <?php if (isset($status_counts)): ?>
                                <span class="badge bg-light text-dark ms-1"><?= array_sum($status_counts) ?></span>
                            <?php endif; ?>
                        </a>
                        <?php foreach ($status_counts as $status => $count): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['status' => $status, 'page' => 1])) ?>" 
                           class="btn <?= $status_filter === $status ? 'btn-primary' : 'btn-outline-secondary' ?> btn-sm">
                            <?= ucfirst($status) ?>
                            <span class="badge bg-light text-dark ms-1"><?= $count ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-sort me-2"></i>Sort Options
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'id', 'order' => 'DESC'])) ?>" 
                           class="btn <?= $sort_by === 'id' ? 'btn-success' : 'btn-outline-success' ?> btn-sm">
                            <i class="fas fa-sort-numeric-down me-1"></i>Newest First
                        </a>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'total', 'order' => 'DESC'])) ?>" 
                           class="btn <?= $sort_by === 'total' ? 'btn-success' : 'btn-outline-success' ?> btn-sm">
                            <i class="fas fa-dollar-sign me-1"></i>Highest Amount
                        </a>
                        <a href="?<?= http_build_query(array_merge($_GET, ['sort' => 'product_name', 'order' => 'ASC'])) ?>" 
                           class="btn <?= $sort_by === 'product_name' ? 'btn-success' : 'btn-outline-success' ?> btn-sm">
                            <i class="fas fa-sort-alpha-down me-1"></i>Product Name
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow-sm">
        <?php if (count($orders) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold"><i class="fas fa-hashtag me-2"></i>Item ID</th>
                        <th class="fw-bold"><i class="fas fa-receipt me-2"></i>Order ID</th>
                        <th class="fw-bold"><i class="fas fa-box me-2"></i>Product Name</th>
                        <th class="fw-bold"><i class="fas fa-dollar-sign me-2"></i>Price</th>
                        <th class="fw-bold"><i class="fas fa-sort-numeric-up me-2"></i>Qty</th>
                        <th class="fw-bold"><i class="fas fa-calculator me-2"></i>Total</th>
                        <th class="fw-bold"><i class="fas fa-info-circle me-2"></i>Status</th>
                        <th class="fw-bold"><i class="fas fa-calendar me-2"></i>Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <strong class="text-primary">#<?= htmlspecialchars($order['id']) ?></strong>
                        </td>
                        <td>
                            <strong class="text-secondary">#<?= htmlspecialchars($order['order_id']) ?></strong>
                        </td>
                        <td>
                            <div class="fw-medium"><?= htmlspecialchars($order['product_name']) ?></div>
                        </td>
                        <td>
                            <span class="text-success fw-bold">$<?= number_format($order['price'], 2) ?></span>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= htmlspecialchars($order['quantity']) ?></span>
                        </td>
                        <td>
                            <span class="text-success fw-bold fs-6">$<?= number_format($order['total'], 2) ?></span>
                        </td>
                        <td>
                            <?php 
                            $status = strtolower($order['status']);
                            $badge_class = 'bg-secondary';
                            switch($status) {
                                case 'pending': $badge_class = 'bg-warning text-dark'; break;
                                case 'processing': $badge_class = 'bg-info'; break;
                                case 'shipped': $badge_class = 'bg-primary'; break;
                                case 'delivered': $badge_class = 'bg-success'; break;
                                case 'cancelled': $badge_class = 'bg-danger'; break;
                            }
                            ?>
                            <span class="badge <?= $badge_class ?> text-uppercase fw-bold">
                                <?= htmlspecialchars(ucfirst($order['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <div class="text-muted small">
                                <i class="fas fa-calendar-alt me-1"></i>
                                <?= date('M j, Y', strtotime($order['order_date'])) ?>
                                <br>
                                <small class="text-muted">
                                    <?= date('g:i A', strtotime($order['order_date'])) ?>
                                </small>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="card-footer bg-light">
            <nav aria-label="Orders pagination" class="d-flex justify-content-center">
                <ul class="pagination mb-0">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="card-body text-center py-5">
            <i class="fas fa-shopping-bag display-1 text-muted mb-3"></i>
            <h4 class="text-muted">No Order Items Found</h4>
            <?php if (!empty($status_filter)): ?>
            <p class="text-muted">No order items found with status "<?= htmlspecialchars(ucfirst($status_filter)) ?>".</p>
            <a href="orders.php" class="btn btn-outline-primary">
                <i class="fas fa-eye me-2"></i>View All Orders
            </a>
            <?php else: ?>
            <p class="text-muted">You haven't placed any orders yet. Start shopping to see your orders here!</p>
            <a href="../public/product.php" class="btn btn-primary">
                <i class="fas fa-shopping-cart me-2"></i>Start Shopping
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
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