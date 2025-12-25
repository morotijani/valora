<?php
require_once 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT o.*, p.name as product_name, p.category FROM orders o JOIN voucher_codes v ON v.order_id = o.id JOIN products p ON v.product_id = p.id WHERE o.user_id = ? ORDER BY o.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - Valora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        async function revealCode(orderId, btn) {
            btn.innerText = 'Revealing...';
            btn.disabled = true;
            try {
                const response = await fetch(`actions/reveal.php?order_id=${orderId}`);
                const data = await response.json();
                if (data.code) {
                    try {
                        const card = JSON.parse(data.code);
                        // It's a Credit Card
                        btn.parentElement.innerHTML = `
                            <div class="bg-gradient-to-br from-gray-700 to-gray-900 p-6 rounded-2xl border border-white/10 shadow-xl w-72 text-left">
                                <div class="flex justify-between items-start mb-4">
                                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">${card.country} • ${card.balance}$</span>
                                    <div class="h-8 w-12 bg-white/5 rounded-md flex items-center justify-center text-[10px] font-bold">CARD</div>
                                </div>
                                <div class="text-lg font-mono tracking-[0.2em] mb-4 text-white">${card.number}</div>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <div class="text-[8px] uppercase text-gray-500 font-bold mb-1">Card Holder</div>
                                        <div class="text-xs font-bold">${card.name}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[8px] uppercase text-gray-500 font-bold mb-1">Expiry / CVV</div>
                                        <div class="text-xs font-bold font-mono">${card.expiry} • ${card.cvv}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    } catch (e) {
                        // It's a simple Gift Card code
                        btn.parentElement.innerHTML = `<code class="bg-black/40 px-3 py-1 rounded text-cyan-400 font-mono text-lg border border-cyan-500/30">${data.code}</code>`;
                    }
                } else {
                    alert(data.error || 'Failed to reveal.');
                    btn.innerText = 'Click to Reveal';
                    btn.disabled = false;
                }
            } catch (e) {
                alert('Connection error.');
                btn.innerText = 'Click to Reveal';
                btn.disabled = false;
            }
        }
    </script>
</head>
<body class="bg-gray-900 text-white min-h-screen">

    <nav class="border-b border-white/10 py-4 px-6 flex justify-between items-center bg-gray-800/50">
        <a href="index.php" class="text-xl font-bold text-indigo-400 tracking-tighter">VALORA</a>
        <div class="flex items-center space-x-6">
            <a href="index.php" class="text-sm font-medium text-gray-400 hover:text-white transition">Catalog</a>
            <a href="logout.php" class="text-sm font-medium text-red-400 hover:text-red-300 transition">Logout</a>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-8">Purchase History</h1>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
            <div class="bg-green-500/10 border border-green-500/50 text-green-500 p-4 rounded-2xl mb-8 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Payment successful! Your voucher is ready below.
            </div>
        <?php endif; ?>

        <div class="space-y-6">
            <?php foreach ($orders as $order): ?>
                <div class="bg-gray-800/80 border border-white/5 rounded-3xl p-6 flex flex-wrap items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-3 mb-1">
                            <span class="text-xs uppercase tracking-widest text-gray-500 font-bold">#<?php echo substr($order['uuid'], 0, 8); ?></span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase <?php echo $order['status'] === 'paid' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400'; ?>">
                                <?php echo $order['status']; ?>
                            </span>
                        </div>
                        <h2 class="text-xl font-bold"><?php echo htmlspecialchars($order['product_name']); ?></h2>
                        <p class="text-sm text-gray-400 mt-1"><?php echo date('M d, Y • H:i', strtotime($order['created_at'])); ?></p>
                    </div>
                    
                    <div class="mt-4 sm:mt-0 text-right">
                        <?php if ($order['status'] === 'paid'): ?>
                            <div class="flex flex-col items-end">
                                <button onclick="revealCode(<?php echo $order['id']; ?>, this)" class="bg-indigo-600/20 hover:bg-indigo-600 text-indigo-400 hover:text-white border border-indigo-500/50 px-6 py-2 rounded-xl text-sm font-bold transition">
                                    Click to Reveal
                                </button>
                                <p class="text-[10px] text-gray-500 mt-2 uppercase tracking-wide">Secure Decryption</p>
                            </div>
                        <?php else: ?>
                            <a href="order_verify_mock.php?uuid=<?php echo $order['uuid']; ?>" class="text-yellow-400 hover:underline font-bold">Complete Payment</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($orders)): ?>
                <div class="text-center py-20 bg-gray-800/30 rounded-3xl border border-dashed border-white/10">
                    <p class="text-gray-500">You haven't made any purchases yet.</p>
                    <a href="index.php" class="text-indigo-400 font-bold mt-2 block hover:underline">Browse Catalog</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
