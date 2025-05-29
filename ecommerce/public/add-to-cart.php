<?php
require_once '../include/db.php';
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to add items to your cart.']);
    header('Location: ../login.php');
    exit;
}

// Get raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['product_id']) || !is_numeric($data['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
    exit;
}

$productId = (int)$data['product_id'];
$quantity = isset($data['quantity']) && is_numeric($data['quantity']) ? (int)$data['quantity'] : 1;
$userId = $_SESSION['user_id'];

try {
    // Check if the product is already in the user's cart
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update quantity
        $newQuantity = $existing['quantity'] + $quantity;
        $updateStmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $updateStmt->execute([$newQuantity, $existing['id']]);
    } else {
        // Insert new cart item
        $insertStmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insertStmt->execute([$userId, $productId, $quantity]);
    }

    echo json_encode(['success' => true, 'message' => 'Product added to cart.']);
} catch (Exception $e) {
    error_log('Add to cart error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
