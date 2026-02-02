<?php
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header("Location: $url");
    exit();
}

function formatPrice($price)
{
    return "NRP " . number_format($price, 2);
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function hasRole($role)
{
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function getCurrentUser($pdo)
{
    if (!isLoggedIn())
        return null;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}