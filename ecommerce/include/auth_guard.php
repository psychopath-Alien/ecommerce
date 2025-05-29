  <?php
  require_once __DIR__ . '/jwt_utils.php';

$token = $_COOKIE['token'] ?? '';

if (!$token) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['error' => true, 'message' => 'Missing or invalid Authorization header']);
        exit;
    }

    $token = $matches[1]; // Extract token from "Bearer ..."
}

$decoded = validateJWT($token);

if (!$decoded) {
    http_response_code(401);
    echo json_encode(['error' => true, 'message' => 'Invalid or expired token']);
    exit;
}
  // JWT is valid, expose user data to the page
  $userId = $decoded->sub;
  $userEmail = $decoded->email;
  $userRole = $decoded->role;
