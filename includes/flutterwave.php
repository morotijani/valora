<?php
require_once 'config.php';

function initiateFlutterwavePayment($orderData) {
    $url = "https://api.flutterwave.com/v3/payments";
    
    $payload = [
        "tx_ref" => $orderData['uuid'],
        "amount" => $orderData['total_amount'],
        "currency" => $orderData['currency'] ?? "USD",
        "redirect_url" => BASE_URL . "/order_verify_flw.php",
        "customer" => [
            "email" => $orderData['email'],
            "name" => "Valora Customer"
        ],
        "customizations" => [
            "title" => SITE_NAME,
            "description" => "Payment for Gift Card: " . $orderData['product_name']
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . FLW_SECRET_KEY,
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ["error" => "CURL Error: " . $err];
    }

    return json_decode($response, true);
}

function verifyFlutterwaveWebhook($payload, $signature) {
    if (!$signature) return false;
    return $signature === FLW_WEBHOOK_HASH;
}
