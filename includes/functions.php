<?php

// Escape output safely
function e($string)
{
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

// Redirect helper
function redirect($url)
{
    // Allow both absolute and BASE_URL-relative redirects
    if (!preg_match('/^https?:\/\//', $url)) {
        if (defined('BASE_URL')) {
            $url = rtrim(BASE_URL, '/') . '/' . ltrim($url, '/');
        }
    }

    header("Location: $url");
    exit();
}

// Format currency
function formatPrice($price)
{
    return "NRP " . number_format((float)$price, 2);
}

// Auth helpers
function isLoggedIn()
{
    return !empty($_SESSION['user_id']);
}

function hasRole($role)
{
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Get current user from DB
function getCurrentUser($pdo)
{
    if (!isLoggedIn()) {
        return null;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
