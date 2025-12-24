<?php
require_once '../includes/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$order_id = $_GET['order_id'] ?? 0;

// Verify ownership and status
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? AND status = 'paid'");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if ($order) {
    // Get the code
    $stmt = $pdo->prepare("SELECT code_encrypted FROM voucher_codes WHERE order_id = ? AND status = 'sold'");
    $stmt->execute([$order_id]);
    $code = $stmt->fetchColumn();
    
    // In real life: Decrypt here
    // $decrypted = openssl_decrypt($code, ...);
    
    echo json_encode(['code' => $code]);
} else {
    echo json_encode(['error' => 'Order not found or not paid.']);
}
