require_once '../includes/db.php';
require_once '../includes/csrf.php';
session_start();

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
    
    // Mock Flutterwave Redirect
    // In real life: redirect to Flutterwave checkout URL here
    header("Location: ../order_verify_mock.php?uuid=" . $uuid);
    exit();
}
