<?php
require_once 'includes/db.php';
session_start();

$uuid = $_GET['uuid'] ?? '';
if (!$uuid) die("Invalid order.");

// Simulating a successful payment verification
$stmt = $pdo->prepare("SELECT * FROM orders WHERE uuid = ?");
$stmt->execute([$uuid]);
$order = $stmt->fetch();

if ($order && $order['status'] === 'pending') {
    // Fulfill
    $pdo->prepare("UPDATE orders SET status = 'paid' WHERE id = ?")->execute([$order['id']]);
    $pdo->prepare("UPDATE voucher_codes SET status = 'sold', sold_at = NOW() WHERE order_id = ?")->execute([$order['id']]);
    
    header("Location: dashboard.php?msg=success");
    exit();
} else {
    die("Order not found or already processed.");
}
