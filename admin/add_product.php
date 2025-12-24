require_once '../includes/db.php';
require_once '../includes/auth_check.php';
require_once '../includes/csrf.php';
session_start();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = $_POST['name'] ?? '';
    $brand = $_POST['brand'] ?? '';
    $price = $_POST['price'] ?? 0;
    $desc = $_POST['description'] ?? '';
    
    $stmt = $pdo->prepare("INSERT INTO products (name, brand, price, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $brand, $price, $desc]);
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - Valora</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-10">
    <div class="max-w-2xl mx-auto bg-white p-10 rounded-3xl shadow-sm border border-gray-100">
        <a href="index.php" class="text-indigo-500 font-bold mb-6 block">← Back to List</a>
        <h1 class="text-3xl font-bold mb-8">Add New Card Product</h1>
        
        <form method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Display Name</label>
                    <input type="text" name="name" required class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Brand (e.g. Netflix)</label>
                    <input type="text" name="brand" required class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Selling Price ($)</label>
                <input type="number" step="0.01" name="price" required class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:outline-none focus:border-indigo-500"></textarea>
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-bold text-lg transition shadow-lg shadow-indigo-100">
                Save Product
            </button>
        </form>
    </div>
</body>
</html>
