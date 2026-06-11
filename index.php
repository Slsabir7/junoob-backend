<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config/database.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/api/', '', $uri);
$uri = trim($uri, '/');
$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents('php://input'), true) ?? [];

// Router
switch (true) {
    // Auth
    case $uri === 'auth/register' && $method === 'POST':
        require 'api/auth.php'; register($body); break;
    case $uri === 'auth/login' && $method === 'POST':
        require 'api/auth.php'; login($body); break;

    // Products
    case $uri === 'products' && $method === 'GET':
        require 'api/products.php'; getProducts(); break;
    case preg_match('/^products\/(\d+)$/', $uri, $m) && $method === 'GET':
        require 'api/products.php'; getProduct($m[1]); break;
    case $uri === 'products' && $method === 'POST':
        require 'api/products.php'; createProduct($body); break;
    case preg_match('/^products\/(\d+)$/', $uri, $m) && $method === 'PUT':
        require 'api/products.php'; updateProduct($m[1], $body); break;
    case preg_match('/^products\/(\d+)$/', $uri, $m) && $method === 'DELETE':
        require 'api/products.php'; deleteProduct($m[1]); break;

    // Orders
    case $uri === 'orders' && $method === 'POST':
        require 'api/orders.php'; createOrder($body); break;
    case $uri === 'orders' && $method === 'GET':
        require 'api/orders.php'; getOrders(); break;
    case preg_match('/^orders\/user\/(\d+)$/', $uri, $m) && $method === 'GET':
        require 'api/orders.php'; getUserOrders($m[1]); break;
    case preg_match('/^orders\/(\d+)\/status$/', $uri, $m) && $method === 'PUT':
        require 'api/orders.php'; updateOrderStatus($m[1], $body); break;

    // User
    case preg_match('/^users\/(\d+)$/', $uri, $m) && $method === 'GET':
        require 'api/users.php'; getUser($m[1]); break;
    case preg_match('/^users\/(\d+)$/', $uri, $m) && $method === 'PUT':
        require 'api/users.php'; updateUser($m[1], $body); break;
    case $uri === 'users/change-password' && $method === 'POST':
        require 'api/users.php'; changePassword($body); break;

    // Settings
    case $uri === 'settings' && $method === 'GET':
        require 'api/settings.php'; getSettings(); break;
    case $uri === 'settings' && $method === 'PUT':
        require 'api/settings.php'; updateSettings($body); break;

    // Promo
    case $uri === 'promo/validate' && $method === 'POST':
        require 'api/promo.php'; validatePromo($body); break;

    // Dashboard
    case $uri === 'dashboard' && $method === 'GET':
        require 'api/dashboard.php'; getDashboard(); break;

    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
}
