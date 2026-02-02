<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: /public/login.php");
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
        header("Location: /public/index.php");
        exit;
    }
}

function checkSuperAdmin()
{
    checkLogin();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
        header("HTTP/1.1 403 Forbidden");
        header("Location: /admin/dashboard.php");
        exit;
    }
}
