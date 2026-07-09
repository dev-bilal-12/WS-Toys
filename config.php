<?php
// Admin login (change these)
define('ADMIN_USERNAME', 'wstoys');
define('ADMIN_PASSWORD', 'wstoys1402');

// Data file paths
define('PRODUCTS_FILE', __DIR__ . '/data/products.json');
define('ORDERS_FILE', __DIR__ . '/data/orders.json');
define('CONTACTS_FILE', __DIR__ . '/data/contacts.json');

// WhatsApp number
define('WHATSAPP_NUMBER', '923293048299');

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function readJSON($file) {
    if (!file_exists($file)) return [];
    $data = file_get_contents($file);
    return json_decode($data, true) ?: [];
}

function writeJSON($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}
