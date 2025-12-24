<?php
function isAdmin() {
    return isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'super_admin');
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: ../login.php");
        exit();
    }
}
