<?php
require_once '../includes/db.php';
require_once '../includes/auth_check.php';
session_start();
requireAdmin();

$stmt = $pdo->query("SELECT p.*, (SELECT COUNT(*) FROM voucher_codes v WHERE v.product_id = p.id AND v.status = 'available') as stock FROM products p");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Valora</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white min-h-screen p-6">
            <h1 class="text-2xl font-black text-indigo-400 mb-10">VALORA ADMIN</h1>
            <nav class="space-y-2">
                <a href="index.php" class="block py-2 px-4 bg-indigo-600 rounded-xl font-bold">Products</a>
                <a href="#" class="block py-2 px-4 text-gray-400 hover:text-white transition">Orders</a>
                <a href="../index.php" class="block py-2 px-4 text-gray-400 hover:text-white transition">View Site</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-10">
            <div class="flex justify-between items-center mb-10">
                <h2 class="text-3xl font-bold">Manage Products</h2>
                <a href="add_product.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold transition shadow-lg shadow-indigo-200">
                    + Add New Card
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 font-bold text-sm text-gray-500 uppercase tracking-widest">Product</th>
                            <th class="px-6 py-4 font-bold text-sm text-gray-500 uppercase tracking-widest">Brand</th>
                            <th class="px-6 py-4 font-bold text-sm text-gray-500 uppercase tracking-widest">Price</th>
                            <th class="px-6 py-4 font-bold text-sm text-gray-500 uppercase tracking-widest">Stock</th>
                            <th class="px-6 py-4 font-bold text-sm text-gray-500 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 font-bold text-sm text-gray-500 uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($products as $product): ?>
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <div class="text-xs text-gray-400">ID: <?php echo $product['id']; ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($product['brand']); ?></td>
                                <td class="px-6 py-4 font-bold text-indigo-600">$<?php echo number_format($product['price'], 2); ?></td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-gray-100 rounded-full text-xs font-bold <?php echo $product['stock'] < 5 ? 'text-red-500' : 'text-gray-600'; ?>">
                                        <?php echo $product['stock']; ?> available
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($product['is_active']): ?>
                                        <span class="text-green-500 text-xs font-bold uppercase tracking-widest">Active</span>
                                    <?php else: ?>
                                        <span class="text-red-400 text-xs font-bold uppercase tracking-widest">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="add_codes.php?id=<?php echo $product['id']; ?>" class="text-indigo-400 hover:text-indigo-600 text-sm font-bold">Add Codes</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>
