require_once 'includes/db.php';
require_once 'includes/csrf.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($email && $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->execute([$email, $hashed]);
            header("Location: login.php?msg=registered");
            exit();
        } catch (PDOException $e) {
            $error = "Email already exists.";
        }
    } else {
        $error = "Please fill all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Valora</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-gray-800 p-8 rounded-3xl border border-white/10 shadow-2xl">
        <h2 class="text-3xl font-bold mb-6 text-center">Create Account</h2>
        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-500 p-3 rounded-lg mb-4 text-sm">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full bg-gray-700 border border-gray-600 rounded-xl px-4 py-3 focus:outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Password</label>
                <input type="password" name="password" required class="w-full bg-gray-700 border border-gray-600 rounded-xl px-4 py-3 focus:outline-none focus:border-indigo-500">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold transition">
                Register
            </button>
        </form>
        <p class="mt-6 text-center text-gray-500 text-sm">
            Already have an account? <a href="login.php" class="text-indigo-400 hover:underline">Login</a>
        </p>
    </div>
</body>
</html>
