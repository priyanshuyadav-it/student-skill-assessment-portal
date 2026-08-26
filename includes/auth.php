<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: dashboard.php');
        exit;
    }
}
?>