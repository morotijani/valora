<?php
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/encryption.php';
require_once '../includes/csrf.php';
requireAdmin();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) die("Product not found.");

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    if ($product['category'] === 'credit_card') {
        // Individual CC entry
        $cc_data = [
            'number' => $_POST['cc_number'] ?? '',
            'cvv' => $_POST['cc_cvv'] ?? '',
            'expiry' => $_POST['cc_expiry'] ?? '',
            'name' => $_POST['cc_name'] ?? '',
            'balance' => $_POST['cc_balance'] ?? '',
            'country' => $_POST['cc_country'] ?? ''
        ];
        $code_json = json_encode($cc_data);
        $hash = hash('sha256', $cc_data['number']); // Hash only the number for uniqueness check
        $encrypted = encryptCode($code_json);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO voucher_codes (product_id, code_encrypted, code_hash, status) VALUES (?, ?, ?, 'available')");
            $stmt->execute([$id, $encrypted, $hash]);
            $msg = "Credit Card details added successfully.";
        } catch (PDOException $e) {
            $msg = "Error: This card number might already exist.";
        }
    } else {
        // Bulk Gift Card entry
        $codes_raw = $_POST['codes'] ?? '';
        $codes_list = array_filter(array_map('trim', explode("\n", $codes_raw)));
        $count = 0;
        foreach ($codes_list as $code) {
            $hash = hash('sha256', $code);
            $encrypted = encryptCode($code);
            try {
                $stmt = $pdo->prepare("INSERT INTO voucher_codes (product_id, code_encrypted, code_hash, status) VALUES (?, ?, ?, 'available')");
                $stmt->execute([$id, $encrypted, $hash]);
                $count++;
            } catch (PDOException $e) { }
        }
        $msg = "Successfully added $count codes.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Codes - <?php echo $product['name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-10">
    <div class="max-w-2xl mx-auto bg-white p-10 rounded-3xl shadow-sm border border-gray-100">
        <a href="index.php" class="text-indigo-500 font-bold mb-6 block">← Back to List</a>
        <h1 class="text-3xl font-bold mb-2">Import Codes for <?php echo $product['brand']; ?></h1>
        <p class="text-gray-400 mb-8"><?php echo $product['name']; ?></p>
        
        <?php if ($msg): ?>
            <div class="bg-green-100 border border-green-200 text-green-600 p-4 rounded-2xl mb-6 font-bold">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            
            <?php if ($product['category'] === 'credit_card'): ?>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Card Number</label>
                        <input type="text" name="cc_number" required placeholder="4111 2222 3333 4444" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Cardholder Name</label>
                        <input type="text" name="cc_name" required placeholder="JOHN DOE" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Expiry (MM/YY)</label>
                        <input type="text" name="cc_expiry" required placeholder="12/26" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">CVV</label>
                        <input type="text" name="cc_cvv" required placeholder="123" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Balance ($)</label>
                        <input type="text" name="cc_balance" required placeholder="500" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Country</label>
                        <input type="text" name="cc_country" required placeholder="USA" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
            <?php else: ?>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Paste Codes (One per line)</label>
                    <textarea name="codes" rows="10" required class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500 font-mono text-sm" placeholder="CODE-1234-5678&#10;CODE-9876-5432"></textarea>
                </div>
            <?php endif; ?>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-bold text-lg transition shadow-lg shadow-indigo-100">
                <?php echo $product['category'] === 'credit_card' ? 'Add Credit Card' : 'Import Gift Card Codes'; ?>
            </button>
        </form>
    </div>
</body>
</html>
