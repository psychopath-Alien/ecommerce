<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// Start the session
session_start();

require_once '../include/db.php'; // PDO connection
require_once '../vendor/autoload.php'; // for Firebase JWT

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents("php://input"), true);

// Change this to your secure secret key
if ($action === 'register') {
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $role = 'user';
    $created_at = date('Y-m-d H:i:s');

    // Validate input
    if (!$name || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }

    // Hash password and insert user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword, $role, $created_at]);

    echo json_encode(['success' => true, 'message' => 'Registration successful']);
    exit;
}

if ($action === 'login') {
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!$email || !$password) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }

    require_once '../include/jwt_utils.php';
    $token = generateJWT($user);

    // ✅ Store user ID in PHP session
    $_SESSION['user_id'] = $user['id'];

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'token' => $token,
        'userId' => $user['id']
    ]);
    exit;
}

http_response_code(404);
echo json_encode(['success' => false, 'message' => 'Invalid action']);
