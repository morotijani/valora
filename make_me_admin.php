<?php
require_once 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in first! <a href='login.php'>Login here</a>");
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['user_email'] ?? 'Your account';

try {
    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
    $stmt->execute([$user_id]);
    
    // Refresh session role
    $_SESSION['user_role'] = 'admin';
    
    echo "<div style='font-family: sans-serif; padding: 40px; text-align: center;'>";
    echo "<h1 style='color: green;'>Success!</h1>";
    echo "<p>User <strong>$email</strong> is now an Admin.</p>";
    echo "<a href='admin/index.php' style='background: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Panel</a>";
    echo "</div>";
} catch (Exception $e) {
    die("Failed to update role: " . $e->getMessage());
}
