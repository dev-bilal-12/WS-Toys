<?php
require_once __DIR__ . '/../config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$name = trim($input['customer_name'] ?? '');
$phone = trim($input['customer_phone'] ?? '');
$email = trim($input['customer_email'] ?? '');
$address = trim($input['customer_address'] ?? '');
$notes = trim($input['extra_notes'] ?? '');
$items = $input['items'] ?? [];

if (!$name || !$phone || !$address || empty($items)) {
    jsonResponse(['success' => false, 'error' => 'Missing required fields'], 400);
}

$orders = readJSON(ORDERS_FILE);

$order = [
    'id' => count($orders) + 1,
    'customer_name' => $name,
    'customer_phone' => $phone,
    'customer_email' => $email,
    'customer_address' => $address,
    'extra_notes' => $notes,
    'items' => $items,
    'total' => array_reduce($items, function($sum, $item) {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0),
    'status' => 'pending',
    'created_at' => date('Y-m-d H:i:s')
];

$orders[] = $order;
writeJSON(ORDERS_FILE, $orders);

jsonResponse(['success' => true, 'data' => $order]);
