<?php
require_once '../vendor/autoload.php'; // Assuming you installed firebase/php-jwt via Composer

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$key = 'JuanMichaelRicky'; // your secret key

function generateJWT($user) {
    global $key;

    $issuedAt = time();
    $expire = $issuedAt + 3600; // 1 hour

    $payload = [
        'iat' => $issuedAt,
        'exp' => $expire,
        'sub' => $user['id'],  // subject: user id
        'email' => $user['email'],
        'role' => $user['role'],
        'name' => $user['name'] 
    ];

    return JWT::encode($payload, $key, 'HS256');
}

function validateJWT($jwt) {
    global $key;

    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        return $decoded;
    } catch (Exception $e) {
        return false;
    }
}

function isValidToken($token) {
    $data = json_decode(base64_decode($token), true);
    return isset($data['user_id']) && is_numeric($data['user_id']);
}

function getUserIdFromToken($token) {
    $data = json_decode(base64_decode($token), true);
    return $data['user_id'] ?? null;
}


function decodeJWT($token) {
    global $key;
    try {
        return JWT::decode($token, new Key($key, 'HS256'));
    } catch (Exception $e) {
        return null;
    }
}