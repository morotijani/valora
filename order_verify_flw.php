<?php
require_once 'includes/db.php';
require_once 'includes/flutterwave.php';
session_start();

$tx_ref = $_GET['tx_ref'] ?? '';
$status = $_GET['status'] ?? '';
$transaction_id = $_GET['transaction_id'] ?? '';

if (!$tx_ref) {
    die("Invalid request.");
}

// In standard redirect, we should verify the transaction status via API
// to ensure the user didn't just append ?status=successful to the URL.

$url = "https://api.flutterwave.com/v3/transactions/" . $transaction_id . "/verify";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . FLW_SECRET_KEY,
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

if (isset($data['status']) && $data['status'] === 'success' && $data['data']['status'] === 'successful') {
    // Transaction is verified
    $stmt = $pdo->prepare("SELECT id, status FROM orders WHERE uuid = ?");
    $stmt->execute([$tx_ref]);
    $order = $stmt->fetch();

    if ($order && $order['status'] === 'pending') {
        // Fulfill
        $pdo->prepare("UPDATE orders SET status = 'paid', provider_ref = ? WHERE id = ?")->execute([$transaction_id, $order['id']]);
        $pdo->prepare("UPDATE voucher_codes SET status = 'sold', sold_at = NOW() WHERE order_id = ?")->execute([$order['id']]);
        
        // Send Email
        require_once 'includes/mailer.php';
        sendOrderConfirmation($tx_ref, $_SESSION['user_email'] ?? 'customer@example.com', $data['data']['amount']);

        header("Location: dashboard.php?msg=success");
        exit();
    } elseif ($order && $order['status'] === 'paid') {
        header("Location: dashboard.php");
        exit();
    }
}

// If we reach here, either payment failed or it's still pending
die("Payment verification failed or pending. If you were charged, please contact support with Ref: " . $tx_ref);
