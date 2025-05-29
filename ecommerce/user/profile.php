<?php
require_once '../include/auth_guard.php'; 
require_once '../include/db.php';

// Initialize variables
$successMessage = '';
$errorMessage = '';

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    try {
        // Validate required fields
        if (empty($_POST['email'])) {
            throw new Exception('Email is required.');
        }

        $updateStmt = $pdo->prepare("UPDATE users SET 
            name = ?,  
            email = ?, 
            phone = ?, 
            address = ?, 
            city = ?, 
            country = ?
            WHERE id = ?");
        
        $updateStmt->execute([
            trim($_POST['name'] ?? ''),
            trim($_POST['email']),
            trim($_POST['phone'] ?? ''),
            trim($_POST['address'] ?? ''),
            trim($_POST['city'] ?? ''),
            trim($_POST['country'] ?? ''),
            $userId
        ]);
        
        $successMessage = 'Profile updated successfully!';
        
        // Refresh user data after update
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $users = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        $errorMessage = 'Error updating profile: ' . $e->getMessage();
    }
} else {
    // Fetch user profile data from the database
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $users = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$users) {
            throw new Exception('User not found.');
        }
    } catch (Exception $e) {
        $errorMessage = 'Error fetching user data: ' . $e->getMessage();
        $users = [];
    }
}

// Get user's name (prioritize 'name' field, fallback to concatenated first/last name if exists)
$fullName = '';
if (!empty($users['name'])) {
    $fullName = trim($users['name']);
}

if (empty($fullName)) {
    $fullName = $users['username'] ?? $users['email'] ?? 'Unknown User';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fas fa-user-cog me-2"></i>Profile Management</a>
        
        <!-- Navigation Menu -->
        <div class="navbar-nav mx-auto">
            <a href="dashboard.php" class="btn btn-outline-light me-3">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
        
        <!-- User Info -->
        <div class="navbar-nav ms-auto">
            <span class="navbar-text text-white">
                <i class="fas fa-user me-1"></i><?= htmlspecialchars($fullName) ?>
            </span>
        </div>
    </div>
</nav>

<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="display-6 fw-bold text-primary mb-2">
                        <i class="fas fa-user-circle me-3"></i>My Profile
                    </h1>
                    <p class="text-muted mb-0">Manage your personal information and account settings</p>
                </div>
                <div class="d-none d-md-block">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-user-edit fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if ($errorMessage): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-lg me-3"></i>
                    <div>
                        <strong>Error!</strong> <?= htmlspecialchars($errorMessage) ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($successMessage): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-lg me-3"></i>
                    <div>
                        <strong>Success!</strong> <?= htmlspecialchars($successMessage) ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Profile Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-white border-0 pb-0">
                    <ul class="nav nav-pills nav-fill" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-4 py-3 fw-semibold" id="overview-tab" 
                                data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab">
                                <i class="fas fa-eye me-2"></i>Profile Overview
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 py-3 fw-semibold" id="edit-tab" 
                                data-bs-toggle="pill" data-bs-target="#edit" type="button" role="tab">
                                <i class="fas fa-edit me-2"></i>Edit Profile
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <div class="p-5">
                                <!-- Profile Header -->
                                <div class="row mb-5">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-4 me-4">
                                                <i class="fas fa-user fa-3x text-primary"></i>
                                            </div>
                                            <div>
                                                <h3 class="fw-bold text-dark mb-1"><?= htmlspecialchars($fullName) ?></h3>
                                                <p class="text-muted mb-0">
                                                    <i class="fas fa-envelope me-2"></i><?= htmlspecialchars($users['email'] ?? '') ?>
                                                </p>
                                                <?php if (!empty($users['phone'])): ?>
                                                <p class="text-muted mb-0">
                                                    <i class="fas fa-phone me-2"></i><?= htmlspecialchars($users['phone']) ?>
                                                </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Profile Information Grid -->
                                <div class="row g-4">
                                    <!-- Personal Information -->
                                    <div class="col-lg-6">
                                        <div class="h-100">
                                            <div class="bg-light rounded-3 p-4 h-100">
                                                <h5 class="fw-bold text-primary mb-4">
                                                    <i class="fas fa-user me-2"></i>Personal Information
                                                </h5>
                                                <div class="row g-3">
                                                    <?php if (!empty($fullName)): ?>
                                                    <div class="col-12">
                                                        <div class="d-flex flex-column">
                                                            <small class="text-muted fw-semibold">Name</small>
                                                            <span class="fw-medium"><?= htmlspecialchars($fullName) ?></span>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="col-12">
                                                        <div class="d-flex flex-column">
                                                            <small class="text-muted fw-semibold">Email Address</small>
                                                            <span class="fw-medium"><?= htmlspecialchars($users['email'] ?? '') ?></span>
                                                        </div>
                                                    </div>
                                                    
                                                    <?php if (!empty($users['phone'])): ?>
                                                    <div class="col-12">
                                                        <div class="d-flex flex-column">
                                                            <small class="text-muted fw-semibold">Phone Number</small>
                                                            <span class="fw-medium"><?= htmlspecialchars($users['phone']) ?></span>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location Information -->
                                    <div class="col-lg-6">
                                        <div class="h-100">
                                            <div class="bg-light rounded-3 p-4 h-100">
                                                <h5 class="fw-bold text-success mb-4">
                                                    <i class="fas fa-map-marker-alt me-2"></i>Location Information
                                                </h5>
                                                <div class="row g-3">
                                                    <?php if (!empty($users['city'])): ?>
                                                    <div class="col-6">
                                                        <div class="d-flex flex-column">
                                                            <small class="text-muted fw-semibold">City</small>
                                                            <span class="fw-medium"><?= htmlspecialchars($users['city']) ?></span>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($users['country'])): ?>
                                                    <div class="col-6">
                                                        <div class="d-flex flex-column">
                                                            <small class="text-muted fw-semibold">Country</small>
                                                            <span class="fw-medium"><?= htmlspecialchars($users['country']) ?></span>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($users['address'])): ?>
                                                    <div class="col-12">
                                                        <div class="d-flex flex-column">
                                                            <small class="text-muted fw-semibold">Address</small>
                                                            <span class="fw-medium"><?= nl2br(htmlspecialchars($users['address'])) ?></span>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Account Info -->
                                <div class="row mt-5">
                                    <div class="col-12">
                                        <div class="border-top pt-4">
                                            <div class="row text-center">
                                                <?php if (!empty($users['created_at'])): ?>
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-calendar-plus text-primary me-2"></i>
                                                        <div>
                                                            <small class="text-muted d-block">Member Since</small>
                                                            <span class="fw-semibold"><?= date('F j, Y', strtotime($users['created_at'])) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($users['updated_at'])): ?>
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-sync-alt text-success me-2"></i>
                                                        <div>
                                                            <small class="text-muted d-block">Last Updated</small>
                                                            <span class="fw-semibold"><?= date('F j, Y', strtotime($users['updated_at'])) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Profile Tab -->
                        <div class="tab-pane fade" id="edit" role="tabpanel">
                            <div class="p-5">
                                <div class="row justify-content-center">
                                    <div class="col-lg-10">
                                        <form method="POST" action="" class="needs-validation" novalidate>
                                            <div class="row g-4">
                                                <!-- Personal Information Section -->
                                                <div class="col-12">
                                                    <h5 class="fw-bold text-primary mb-4">
                                                        <i class="fas fa-user me-2"></i>Personal Information
                                                    </h5>
                                                </div>
                                                
                                                <div class="col-12">
                                                    <label for="name" class="form-label fw-semibold">
                                                        <i class="fas fa-user me-2 text-primary"></i>Name
                                                    </label>
                                                    <input type="text" class="form-control form-control-lg" id="name" name="name" 
                                                        value="<?= htmlspecialchars($users['name'] ?? $fullName) ?>" placeholder="Enter your full name">
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="email" class="form-label fw-semibold">
                                                        <i class="fas fa-envelope me-2 text-success"></i>Email Address *
                                                    </label>
                                                    <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                                        value="<?= htmlspecialchars($users['email'] ?? '') ?>" required>
                                                    <div class="invalid-feedback">Please provide a valid email address.</div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="phone" class="form-label fw-semibold">
                                                        <i class="fas fa-phone me-2 text-info"></i>Phone Number
                                                    </label>
                                                    <input type="tel" class="form-control form-control-lg" id="phone" name="phone" 
                                                        value="<?= htmlspecialchars($users['phone'] ?? '') ?>" placeholder="Enter your phone number">
                                                </div>

                                                <!-- Location Information Section -->
                                                <div class="col-12 mt-5">
                                                    <h5 class="fw-bold text-success mb-4">
                                                        <i class="fas fa-map-marker-alt me-2"></i>Location Information
                                                    </h5>
                                                </div>
                                                
                                                <div class="col-12">
                                                    <label for="address" class="form-label fw-semibold">
                                                        <i class="fas fa-home me-2 text-warning"></i>Address
                                                    </label>
                                                    <textarea class="form-control form-control-lg" id="address" name="address" rows="3" 
                                                        placeholder="Enter your full address"><?= htmlspecialchars($users['address'] ?? '') ?></textarea>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="city" class="form-label fw-semibold">
                                                        <i class="fas fa-building me-2 text-secondary"></i>City
                                                    </label>
                                                    <input type="text" class="form-control form-control-lg" id="city" name="city" 
                                                        value="<?= htmlspecialchars($users['city'] ?? '') ?>" placeholder="Enter your city">
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <label for="country" class="form-label fw-semibold">
                                                        <i class="fas fa-globe me-2 text-dark"></i>Country
                                                    </label>
                                                    <input type="text" class="form-control form-control-lg" id="country" name="country" 
                                                        value="<?= htmlspecialchars($users['country'] ?? '') ?>" placeholder="Enter your country">
                                                </div>
                                            </div>
                                            
                                            <!-- Form Actions -->
                                            <div class="row mt-5">
                                                <div class="col-12">
                                                    <div class="d-flex gap-3 justify-content-end">
                                                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" onclick="resetForm()">
                                                            <i class="fas fa-undo me-2"></i>Reset Changes
                                                        </button>
                                                        <button type="submit" name="update_profile" class="btn btn-primary btn-lg px-4">
                                                            <i class="fas fa-save me-2"></i>Update Profile
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> Profile Management System. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

function resetForm() {
    if (confirm('Are you sure you want to reset all changes? This will reload the page and lose any unsaved changes.')) {
        location.reload();
    }
}

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>

</body>
</html>