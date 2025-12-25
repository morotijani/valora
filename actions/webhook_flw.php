<?php
require_once '../includes/db.php';
require_once '../includes/flutterwave.php';
require_once '../includes/mailer.php';

// Retrieve the request body
$body = file_get_contents('php://input');
$signature = $_SERVER['HTTP_VERIF_HASH'] ?? '';

// HMAC Verification
if (!verifyFlutterwaveWebhook($body, $signature)) {
    http_response_code(401);
    exit();
}

$event = json_decode($body, true);

if (isset($event['event']) && $event['event'] === 'charge.completed') {
    $tx_ref = $event['data']['tx_ref'];
    $flw_id = $event['data']['id'];
    $status = $event['data']['status'];

    if ($status === 'successful') {
        $stmt = $pdo->prepare("SELECT id, status FROM orders WHERE uuid = ?");
        $stmt->execute([$tx_ref]);
        $order = $stmt->fetch();

        if ($order && $order['status'] === 'pending') {
            // Fulfill Order
            $pdo->prepare("UPDATE orders SET status = 'paid', provider_ref = ? WHERE id = ?")->execute([$flw_id, $order['id']]);
            $pdo->prepare("UPDATE voucher_codes SET status = 'sold', sold_at = NOW() WHERE order_id = ?")->execute([$order['id']]);
            
            // Get user email
            $stmt = $pdo->prepare("SELECT email FROM users WHERE id = (SELECT user_id FROM orders WHERE id = ?)");
            $stmt->execute([$order['id']]);
            $u_email = $stmt->fetchColumn();
            
            sendOrderConfirmation($tx_ref, $u_email, $event['data']['amount']);

            // Log this action
            error_log("Webhook Success: Order $tx_ref fulfilled.");
        }
    }
}

http_response_code(200);
echo "Webhook handled.";
