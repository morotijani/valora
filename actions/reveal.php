<?php
require_once '../includes/db.php';
require_once '../includes/encryption.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once '../includes/rate_limit.php';
$user_id = $_SESSION['user_id'];
if (!checkRateLimit("reveal_$user_id", 10, 60)) {
    echo json_encode(['error' => 'Too many reveal attempts. Slow down.']);
    exit();
}

$order_id = $_GET['order_id'] ?? 0;

// Verify ownership and status
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? AND status = 'paid'");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if ($order) {
    $stmt = $pdo->prepare("SELECT code_encrypted FROM voucher_codes WHERE order_id = ? AND status = 'sold'");
    $stmt->execute([$order_id]);
    $encoded = $stmt->fetchColumn();
    
    $decrypted = decryptCode($encoded);
    
    echo json_encode(['code' => $decrypted]);
} else {
    echo json_encode(['error' => 'Order not found or not paid.']);
}
