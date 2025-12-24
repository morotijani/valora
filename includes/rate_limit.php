<?php
// Simple file-base or DB-based rate limiter
// For simplicity, we'll use a table 'rate_limits'

function checkRateLimit($key, $limit = 5, $window = 300) {
    global $pdo;
    
    // Cleanup old attempts
    $pdo->prepare("DELETE FROM rate_limits WHERE expires_at < NOW()")->execute();
    
    // Count attempts
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM rate_limits WHERE action_key = ?");
    $stmt->execute([$key]);
    $count = $stmt->fetchColumn();
    
    if ($count >= $limit) {
        return false;
    }
    
    // Record attempt
    $stmt = $pdo->prepare("INSERT INTO rate_limits (action_key, expires_at) VALUES (?, DATE_ADD(NOW(), INTERVAL ? SECOND))");
    $stmt->execute([$key, $window]);
    
    return true;
}
