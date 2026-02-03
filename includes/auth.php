<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}

function checkAdmin()
{
    checkLogin();

    if (
        !isset($_SESSION['role']) ||
        ($_SESSION['role'] !== 'agent' && $_SESSION['role'] !== 'super_admin')
    ) {
        header("HTTP/1.1 403 Forbidden");
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }
}

function checkSuperAdmin()
{
    checkLogin();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
        header("HTTP/1.1 403 Forbidden");
        header("Location: " . BASE_URL . "/admin/dashboard.php");
        exit;
    }
}