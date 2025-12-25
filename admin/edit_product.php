<?php
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/csrf.php';
requireAdmin();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found.");
}

$cc_data = null;
if ($product['category'] === 'credit_card') {
    require_once '../includes/encryption.php';
    $stmt = $pdo->prepare("SELECT code_encrypted FROM voucher_codes WHERE product_id = ? LIMIT 1");
    $stmt->execute([$id]);
    $encoded = $stmt->fetchColumn();
    if ($encoded) {
        $cc_data = json_decode(decryptCode($encoded), true);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = $_POST['name'] ?? '';
    $brand = $_POST['brand'] ?? '';
    $category = $_POST['category'] ?? 'gift_card';
    $price = $_POST['price'] ?? 0;
    $desc = $_POST['description'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, brand = ?, category = ?, price = ?, description = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$name, $brand, $category, $price, $desc, $is_active, $id]);

        if ($category === 'credit_card') {
            require_once '../includes/encryption.php';
            $new_cc_data = [
                'number' => $_POST['cc_number'] ?? '',
                'cvv' => $_POST['cc_cvv'] ?? '',
                'expiry' => $_POST['cc_expiry'] ?? '',
                'name' => $_POST['cc_name'] ?? '',
                'balance' => $_POST['cc_balance'] ?? '',
                'country' => $_POST['cc_country'] ?? ''
            ];
            $code_json = json_encode($new_cc_data);
            $hash = hash('sha256', $new_cc_data['number']);
            $encrypted = encryptCode($code_json);
            
            // Check if code exists, if so update, else insert
            $stmt = $pdo->prepare("SELECT id FROM voucher_codes WHERE product_id = ? LIMIT 1");
            $stmt->execute([$id]);
            $vid = $stmt->fetchColumn();
            
            if ($vid) {
                $stmt = $pdo->prepare("UPDATE voucher_codes SET code_encrypted = ?, code_hash = ? WHERE id = ?");
                $stmt->execute([$encrypted, $hash, $vid]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO voucher_codes (product_id, code_encrypted, code_hash, status) VALUES (?, ?, ?, 'available')");
                $stmt->execute([$id, $encrypted, $hash]);
            }
        }
        
        $pdo->commit();
        header("Location: index.php?msg=updated");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error updating product: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product - Valora</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-10">
    <div class="max-w-2xl mx-auto bg-white p-10 rounded-3xl shadow-sm border border-gray-100">
        <a href="index.php" class="text-indigo-500 font-bold mb-6 block">← Back to List</a>
        <h1 class="text-3xl font-bold mb-8">Edit Card Product</h1>
        
        <form method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Display Name</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Brand (e.g. Netflix)</label>
                    <input type="text" name="brand" required value="<?php echo htmlspecialchars($product['brand']); ?>" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                    <select name="category" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                        <option value="gift_card" <?php echo $product['category'] === 'gift_card' ? 'selected' : ''; ?>>Gift Card</option>
                        <option value="credit_card" <?php echo $product['category'] === 'credit_card' ? 'selected' : ''; ?>>Credit Card</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Selling Price ($)</label>
                    <input type="number" step="0.01" name="price" required value="<?php echo $product['price']; ?>" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <!-- Credit Card Details (Conditional) -->
            <div id="cc_fields" class="<?php echo $product['category'] === 'credit_card' ? '' : 'hidden'; ?> space-y-6 pt-6 border-t border-gray-100">
                <h3 class="font-bold text-indigo-600 uppercase tracking-widest text-xs">Credit Card Details</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Card Number</label>
                        <input type="text" name="cc_number" value="<?php echo htmlspecialchars($cc_data['number'] ?? ''); ?>" placeholder="4111 2222 3333 4444" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Cardholder Name</label>
                        <input type="text" name="cc_name" value="<?php echo htmlspecialchars($cc_data['name'] ?? ''); ?>" placeholder="JOHN DOE" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Expiry</label>
                        <input type="text" name="cc_expiry" value="<?php echo htmlspecialchars($cc_data['expiry'] ?? ''); ?>" placeholder="12/26" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">CVV</label>
                        <input type="text" name="cc_cvv" value="<?php echo htmlspecialchars($cc_data['cvv'] ?? ''); ?>" placeholder="123" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Balance ($)</label>
                        <input type="text" name="cc_balance" value="<?php echo htmlspecialchars($cc_data['balance'] ?? ''); ?>" placeholder="500" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Country</label>
                    <input type="text" name="cc_country" value="<?php echo htmlspecialchars($cc_data['country'] ?? ''); ?>" placeholder="USA" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <input type="checkbox" name="is_active" id="is_active" <?php echo $product['is_active'] ? 'checked' : ''; ?> class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                <label for="is_active" class="text-sm font-bold text-gray-700">Product is Active (Visible to customers)</label>
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-bold text-lg transition shadow-lg shadow-indigo-100">
                Update Product
            </button>
        </form>
    </div>

    <script>
        const categorySelect = document.querySelector('select[name="category"]');
        const ccFields = document.getElementById('cc_fields');
        
        categorySelect.addEventListener('change', function() {
            if (this.value === 'credit_card') {
                ccFields.classList.remove('hidden');
                ccFields.querySelectorAll('input').forEach(i => i.required = true);
            } else {
                ccFields.classList.add('hidden');
                ccFields.querySelectorAll('input').forEach(i => i.required = false);
            }
        });
        
        // Initial check
        if (categorySelect.value === 'credit_card') {
            ccFields.querySelectorAll('input').forEach(i => i.required = true);
        }
    </script>
</body>
</html>
