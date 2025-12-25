<?php
require_once 'includes/db.php';
session_start();

// Fetch products
$category_filter = $_GET['cat'] ?? 'all';
$query = "SELECT * FROM products WHERE is_active = 1";
$params = [];

if ($category_filter !== 'all') {
    $query .= " AND category = ?";
    $params[] = $category_filter;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Premium Gift Cards</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-nav {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">

    <!-- Navigation -->
    <nav class="glass-nav sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="index.php" class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-cyan-400">
                        VALORA
                    </a>
                </div>
                <div class="flex items-center space-x-6">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="text-gray-300 hover:text-white text-sm font-medium">My Orders</a>
                        <a href="logout.php" class="text-gray-300 hover:text-white text-sm font-medium">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-300 hover:text-white text-sm font-medium">Login</a>
                        <a href="register.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-full text-sm font-bold transition">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <header class="py-16 text-center">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-5xl md:text-6xl font-extrabold mb-6 tracking-tight">
                Digital Assets <br>
                <span class="text-indigo-400">Instantly Delivered.</span>
            </h1>
            <p class="text-xl text-gray-400 mb-10 leading-relaxed">
                Purchase secure gift cards, virtual credit cards, and prepaid vouchers with ease.
            </p>
            
            <!-- Category Filters -->
            <div class="flex justify-center space-x-2">
                <a href="index.php?cat=all" class="px-6 py-2 rounded-full text-sm font-bold transition <?php echo $category_filter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'; ?>">All</a>
                <a href="index.php?cat=gift_card" class="px-6 py-2 rounded-full text-sm font-bold transition <?php echo $category_filter === 'gift_card' ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'; ?>">Gift Cards</a>
                <a href="index.php?cat=credit_card" class="px-6 py-2 rounded-full text-sm font-bold transition <?php echo $category_filter === 'credit_card' ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'; ?>">Credit Cards</a>
            </div>
        </div>
    </header>

    <!-- Products -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($products as $product): ?>
                <div class="bg-gray-800/50 rounded-3xl border border-white/5 p-4 hover:border-indigo-500/50 transition-all group">
                    <div class="h-40 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl flex items-center justify-center text-4xl font-bold shadow-lg mb-4 group-hover:scale-[1.02] transition-transform">
                        <?php echo htmlspecialchars($product['brand']); ?>
                    </div>
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="font-bold text-lg"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="text-xs text-gray-500 uppercase tracking-widest"><?php echo htmlspecialchars($product['brand']); ?></p>
                        </div>
                        <span class="text-indigo-400 font-bold">$<?php echo number_format($product['price'], 2); ?></span>
                    </div>
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="block w-full text-center bg-gray-700 hover:bg-indigo-600 text-white py-2 rounded-xl text-sm font-bold transition mt-4">
                        Buy Now
                    </a>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($products)): ?>
                <div class="col-span-full py-20 text-center text-gray-500 italic">
                    No products available yet. Admin needs to add cards!
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="border-t border-white/5 py-10 text-center text-gray-600 text-sm">
        &copy; <?php echo date('Y'); ?> Valora. All rights reserved.
    </footer>

</body>
</html>
