<?php
require_once 'config.php';

/**
 * Send an email confirmation.
 * This is a wrapper that can be extended to use PHPMailer or any other library.
 */
function sendOrderConfirmation($order_uuid, $customer_email, $amount) {
    $subject = "Your " . SITE_NAME . " Purchase Confirmation";
    $message = "
    <html>
    <head>
        <title>Purchase Confirmation</title>
    </head>
    <body style='font-family: sans-serif; background: #f4f4f4; padding: 20px;'>
        <div style='max-width: 600px; margin: auto; background: white; padding: 40px; border-radius: 20px;'>
            <h1 style='color: #4f46e5;'>Thank You for Your Purchase!</h1>
            <p>Your order <strong>#$order_uuid</strong> has been successfully processed.</p>
            <p><strong>Total Paid:</strong> $$amount</p>
            <p>You can now reveal your voucher codes in your dashboard:</p>
            <a href='" . BASE_URL . "/dashboard.php' style='display: inline-block; background: #4f46e5; color: white; padding: 15px 25px; text-decoration: none; border-radius: 10px; font-weight: bold;'>View My Codes</a>
            <hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>
            <p style='color: #666; font-size: 12px;'>If you did not make this purchase, please contact support immediately.</p>
        </div>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <noreply@' . parse_url(BASE_URL, PHP_URL_HOST) . '>' . "\r\n";

    // In local XAMPP, mail() often needs configuration in php.ini
    // To use PHPMailer, you would include it here instead of mail()
    return mail($customer_email, $subject, $message, $headers);
}
