<?php
file_put_contents('headers_log.txt', print_r(getallheaders(), true));
require_once '../include/db.php';
session_start();

// Initialize cart count
$cartCount = 0;

// Get cart count if user is logged in
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$result = $stmt->fetch();
$cartCount = $result['total'] ?? 0;

// Fetch products with category name
$stmt = $pdo->query("SELECT p.id, p.name, p.brand, c.name AS category, p.price, p.rating, p.reviews, p.icon
                      FROM products p
                      JOIN categories c ON p.categories_id = c.id");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch sizes for each product
$sizeStmt = $pdo->query("SELECT product_id, size FROM product_sizes");
$sizesRaw = $sizeStmt->fetchAll(PDO::FETCH_ASSOC);

// Map sizes to products
$productSizes = [];
foreach ($sizesRaw as $row) {
    $productSizes[$row['product_id']][] = (int)$row['size'];
}

// Append sizes to products
foreach ($products as &$product) {
    $product['sizes'] = $productSizes[$product['id']] ?? [];
}
unset($product);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyShop - Premium Shoes Collection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="product-style.css">
    <style>
        /* Additional styles for product images */
        .product-image {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
        }
        
        .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
        .image-placeholder {
            color: #6c757d;
            font-size: 3rem;
        }
        
        .product-image-error {
            color: #dc3545;
            font-size: 2rem;
            text-align: center;
        }
    </style>
</head>
<script>
    window.isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    window.cartCount = <?= $cartCount ?>;
</script>
<body>
    
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="../user/dashboard.php">
            <i class="fas fa-store me-2"></i>MyShop
        </a>
        <div class="d-flex align-items-center">
            <div class="cart-container me-3">
                <a href="cart.php" class="btn btn-outline-light">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" id="cartCount"><?= $cartCount ?></span>
                </a>
            </div>
            <a href="../user/dashboard.php" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</nav>

<div class="hero-section">
    <div class="container">
        <h1 class="hero-title">Premium Shoes Collection</h1>
        <p class="hero-subtitle">Step into style with our curated selection of premium footwear</p>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="filter-sidebar">
                <h5 class="filter-title">
                    <i class="fas fa-filter me-2"></i>Filters
                </h5>
                <!-- Brand Filter -->
                <div class="filter-section">
                    <h6>Brand</h6>
                    <?php foreach (["Nike", "Adidas", "Puma", "Reebok"] as $brand): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input filter-brand" type="checkbox" value="<?= strtolower($brand) ?>" id="<?= strtolower($brand) ?>" onchange="applyFilters()">
                        <label class="form-check-label" for="<?= strtolower($brand) ?>"><?= $brand ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- Category Filter -->
                <div class="filter-section">
                    <h6>Category</h6>
                    <?php foreach (["running", "casual", "sports", "formal"] as $cat): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input filter-category" type="checkbox" value="<?= $cat ?>" id="<?= $cat ?>" onchange="applyFilters()">
                        <label class="form-check-label" for="<?= $cat ?>"><?= ucfirst($cat) ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- Price Range -->
                <div class="filter-section">
                    <h6>Price Range</h6>
                    <div class="price-range">
                        <label for="priceRange">$<span id="priceValue">200</span></label>
                        <input type="range" class="form-range" min="50" max="500" value="200" id="priceRange" oninput="updatePriceValue(); applyFilters()">
                        <div class="d-flex justify-content-between">
                            <small>$50</small>
                            <small>$500</small>
                        </div>
                    </div>
                </div>
                <!-- Size Filter -->
                <div class="filter-section">
                    <h6>Size</h6>
                    <div class="row g-2">
                        <?php foreach ([7,8,9,10,11,12] as $size): ?>
                        <div class="col-4">
                            <div class="form-check">
                                <input class="form-check-input filter-size" type="checkbox" value="<?= $size ?>" id="size<?= $size ?>" onchange="applyFilters()">
                                <label class="form-check-label" for="size<?= $size ?>"><?= $size ?></label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="btn btn-outline-primary w-100 mt-3" onclick="clearFilters()">
                    <i class="fas fa-times me-2"></i>Clear Filters
                </button>
            </div>
        </div>

        <!-- Products Display -->
        <div class="col-lg-9">
            <div class="row">
                <div class="col-12">
                    <div class="search-container mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control search-input" placeholder="Search for shoes..." id="searchInput" oninput="applyFilters()">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div id="resultsCount" class="text-muted">Showing all products</div>
                        <select class="form-select" style="width: auto;" id="sortSelect" onchange="applyFilters()">
                            <option value="default">Sort by</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="rating">Rating</option>
                            <option value="name">Name A-Z</option>
                        </select>
                    </div>
                    <div class="row g-4" id="productsGrid">
                        <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6 product-item" 
                             data-brand="<?= strtolower($product['brand']) ?>"
                             data-category="<?= strtolower($product['category']) ?>"
                             data-price="<?= $product['price'] ?>"
                             data-rating="<?= $product['rating'] ?>"
                             data-name="<?= strtolower($product['name']) ?>"
                             data-sizes="<?= implode(',', $product['sizes']) ?>"
                             data-id="<?= $product['id'] ?>">
                            <div class="card product-card">
                                <div class="product-image">
                                    <?php if (!empty($product['icon'])): ?>
                                        <img src="<?= htmlspecialchars($product['icon']) ?>" 
                                             alt="<?= htmlspecialchars($product['name']) ?>"
                                             loading="lazy"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div class="product-image-error" style="display: none;">
                                            <i class="fas fa-image"></i>
                                            <div>Image not found</div>
                                        </div>
                                    <?php else: ?>
                                        <div class="image-placeholder">
                                            <i class="fas fa-shoe-prints"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info p-3">
                                    <div class="product-brand text-muted"><?= htmlspecialchars($product['brand']) ?></div>
                                    <h5 class="product-name"><?= htmlspecialchars($product['name']) ?></h5>
                                    <div class="product-price fw-bold">$<?= number_format($product['price'], 2) ?></div>
                                    <div class="product-rating">
                                        <div class="rating-stars">
                                            <?php
                                            $fullStars = floor($product['rating']);
                                            $halfStar = $product['rating'] - $fullStars >= 0.5;
                                            for ($i = 0; $i < $fullStars; $i++) echo '<i class="fas fa-star"></i>';
                                            if ($halfStar) echo '<i class="fas fa-star-half-alt"></i>';
                                            for ($i = $fullStars + $halfStar; $i < 5; $i++) echo '<i class="far fa-star"></i>';
                                            ?>
                                        </div>
                                        <span class="rating-text"><?= number_format($product['rating'], 1) ?> (<?= $product['reviews'] ?> reviews)</span>
                                    </div>
                                    <div class="available-sizes mt-2">
                                        <small class="text-muted">Sizes: <?= implode(', ', $product['sizes']) ?></small>
                                    </div>
                                    <button class="btn btn-add-cart w-100 mt-2" onclick="addToCart(<?= $product['id'] ?>)">
                                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="noResults" class="text-center py-5" style="display: none;">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No products found</h5>
                        <p class="text-muted">Try adjusting your filters or search terms</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize products array from DOM
document.addEventListener('DOMContentLoaded', function() {
    const productItems = document.querySelectorAll('.product-item');
    window.allProducts = Array.from(productItems).map(item => ({
        element: item,
        brand: item.dataset.brand,
        category: item.dataset.category,
        price: parseFloat(item.dataset.price),
        rating: parseFloat(item.dataset.rating),
        name: item.dataset.name,
        sizes: item.dataset.sizes.split(',').map(s => parseInt(s)).filter(s => !isNaN(s)),
        id: item.dataset.id
    }));
    
    // Initialize cart count
    document.getElementById('cartCount').textContent = window.cartCount || 0;
});

function updatePriceValue() {
    const priceRange = document.getElementById('priceRange');
    const priceValue = document.getElementById('priceValue');
    priceValue.textContent = priceRange.value;
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const maxPrice = parseFloat(document.getElementById('priceRange').value);
    const sortBy = document.getElementById('sortSelect').value;
    
    // Get selected filters
    const selectedBrands = Array.from(document.querySelectorAll('.filter-brand:checked')).map(cb => cb.value);
    const selectedCategories = Array.from(document.querySelectorAll('.filter-category:checked')).map(cb => cb.value);
    const selectedSizes = Array.from(document.querySelectorAll('.filter-size:checked')).map(cb => parseInt(cb.value));
    
    // Filter products
    let filteredProducts = window.allProducts.filter(product => {
        // Search filter
        const matchesSearch = !searchTerm || 
            product.name.includes(searchTerm) || 
            product.brand.includes(searchTerm);
        
        // Brand filter
        const matchesBrand = selectedBrands.length === 0 || selectedBrands.includes(product.brand);
        
        // Category filter
        const matchesCategory = selectedCategories.length === 0 || selectedCategories.includes(product.category);
        
        // Price filter
        const matchesPrice = product.price <= maxPrice;
        
        // Size filter
        const matchesSize = selectedSizes.length === 0 || 
            selectedSizes.some(size => product.sizes.includes(size));
        
        return matchesSearch && matchesBrand && matchesCategory && matchesPrice && matchesSize;
    });
    
    // Sort products
    if (sortBy !== 'default') {
        filteredProducts.sort((a, b) => {
            switch (sortBy) {
                case 'price-low':
                    return a.price - b.price;
                case 'price-high':
                    return b.price - a.price;
                case 'rating':
                    return b.rating - a.rating;
                case 'name':
                    return a.name.localeCompare(b.name);
                default:
                    return 0;
            }
        });
    }
    
    // Hide all products first
    window.allProducts.forEach(product => {
        product.element.style.display = 'none';
    });
    
    // Show filtered products
    const productsGrid = document.getElementById('productsGrid');
    filteredProducts.forEach((product, index) => {
        product.element.style.display = 'block';
        productsGrid.appendChild(product.element);
    });
    
    // Update results count
    const resultsCount = document.getElementById('resultsCount');
    const noResults = document.getElementById('noResults');
    
    if (filteredProducts.length === 0) {
        resultsCount.textContent = 'No results found';
        noResults.style.display = 'block';
    } else {
        resultsCount.textContent = `Showing ${filteredProducts.length} of ${window.allProducts.length} products`;
        noResults.style.display = 'none';
    }
}

function clearFilters() {
    // Clear all checkboxes
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    
    // Reset price range
    document.getElementById('priceRange').value = 200;
    document.getElementById('priceValue').textContent = '200';
    
    // Clear search input
    document.getElementById('searchInput').value = '';
    
    // Reset sort select
    document.getElementById('sortSelect').value = 'default';
    
    // Apply filters to show all products
    applyFilters();
}

function addToCart(productId) {
    const productElement = document.querySelector(`[data-id="${productId}"]`);
    const button = productElement ? productElement.querySelector('.btn-add-cart') : null;

    if (button) {
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
        button.disabled = true;
    }

    fetch('add-to-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.cartCount += 1;
            document.getElementById('cartCount').textContent = window.cartCount;
            if (button) {
                button.innerHTML = '<i class="fas fa-check me-2"></i>Added';
                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Add to Cart';
                    button.disabled = false;
                }, 1500);
            }
        } else {
            alert(data.message || 'Failed to add to cart.');
            if (button) {
                button.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Add to Cart';
                button.disabled = false;
            }
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        alert('An error occurred. Please try again.');
        if (button) {
            button.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Add to Cart';
            button.disabled = false;
        }
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    setTimeout(() => {
        if (alertDiv.parentNode) alertDiv.remove();
    }, 3000);
}

// Add some CSS for smooth animations
const style = document.createElement('style');
style.textContent = `
    .product-item {
        transition: all 0.3s ease;
    }
    
    .alert {
        animation: slideInRight 0.3s ease;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .btn-add-cart:hover {
        transform: translateY(-1px);
    }
`;
document.head.appendChild(style);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>