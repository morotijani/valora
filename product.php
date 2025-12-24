<?php
require_once 'includes/db.php';
session_start();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $product['name']; ?> - Valora</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen">

    <nav class="border-b border-white/10 py-4 px-6 flex justify-between items-center">
        <a href="index.php" class="text-xl font-bold text-indigo-400">VALORA</a>
        <a href="index.php" class="text-gray-400 hover:text-white transition">Back to Catalog</a>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-20">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="h-80 bg-gradient-to-br from-indigo-500 to-cyan-500 rounded-3xl flex items-center justify-center text-6xl font-black shadow-2xl">
                <?php echo $product['brand']; ?>
            </div>
            <div>
                <h1 class="text-4xl font-extrabold mb-2"><?php echo $product['name']; ?></h1>
                <p class="text-indigo-400 text-2xl font-bold mb-6">$<?php echo number_format($product['price'], 2); ?></p>
                <div class="text-gray-400 mb-8 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
                
                <form action="actions/purchase.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-bold text-lg shadow-lg hover:shadow-indigo-500/20 transition-all">
                        Buy Now with Flutterwave
                    </button>
                </form>
                <p class="mt-4 text-xs text-center text-gray-500 uppercase tracking-widest">Secure Checkout • Instant Delivery</p>
            </div>
        </div>
    </main>

</body>
</html>
