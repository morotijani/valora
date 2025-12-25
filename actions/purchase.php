<?php
require_once '../includes/db.php';
require_once '../includes/csrf.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $product_id = $_POST['product_id'] ?? 0;
    
    // Check stock
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM voucher_codes WHERE product_id = ? AND status = 'available'");
    $stmt->execute([$product_id]);
    if ($stmt->fetchColumn() <= 0) {
        die("Out of stock!");
    }
    
    // Create pending order
    $uuid = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $price = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("INSERT INTO orders (uuid, user_id, total_amount, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$uuid, $_SESSION['user_id'], $price]);
    $order_id = $pdo->lastInsertId();
    
    // Reserve one code
    $stmt = $pdo->prepare("UPDATE voucher_codes SET order_id = ?, status = 'reserved', reserved_at = NOW() WHERE product_id = ? AND status = 'available' LIMIT 1");
    $stmt->execute([$order_id, $product_id]);
    
    // Get product info for FLW
    $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $p_name = $stmt->fetchColumn();

    // Initiate Real Flutterwave Payment
    require_once '../includes/flutterwave.php';
    $flwData = initiateFlutterwavePayment([
        'uuid' => $uuid,
        'total_amount' => $price,
        'email' => $_SESSION['user_email'] ?? 'customer@example.com', // Need email in session!
        'product_name' => $p_name
    ]);

    if (isset($flwData['status']) && $flwData['status'] === 'success') {
        header("Location: " . $flwData['data']['link']);
    } else {
        die("Flutterwave error: " . ($flwData['message'] ?? 'Unknown error'));
    }
    exit();
}
