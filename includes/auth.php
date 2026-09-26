<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isStudent()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: ../auth/login.php");
        exit;
    }
}

function requireAdmin()
{
    requireLogin();

    if (!isAdmin()) {
        header("Location: ../student/dashboard.php");
        exit;
    }
}

function requireStudent()
{
    requireLogin();

    if (!isStudent()) {
        header("Location: ../admin/dashboard.php");
        exit;
    }
}

function logoutUser()
{
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}

?>