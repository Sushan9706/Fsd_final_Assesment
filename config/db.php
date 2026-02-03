<?php
// config/db.php

// ---------- DATABASE CONFIG ----------
$db_host = 'localhost';
$db_name = 'NP03CS4A240103';
$db_user = 'NP03CS4A240103';
$db_pass = 'yRlyy458EP';
$charset = 'utf8mb4';

// $db_host = 'localhost';
// $db_name = 'real_estate_db';
// $db_user = 'root';
// $db_pass = '';
// $charset = 'utf8mb4';

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Don't show detailed error to users in production
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

// ---------- BASE URL CONFIG ----------
// Auto-detect BASE_URL based on current directory structure
// This works on both local development and production servers
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];

    // Get the directory path from the current script
    // This gives us the web path, not the file system path
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);

    // Remove /config if this file is being loaded from there
    $basePath = str_replace('/config', '', $scriptDir);

    // Ensure we're at the project root
    if (basename($basePath) !== 'fsd_final') {
        // We're in a subdirectory, go up to find fsd_final
        $pathParts = explode('/', trim($basePath, '/'));
        $basePathArray = [];
        foreach ($pathParts as $part) {
            $basePathArray[] = $part;
            if ($part === 'fsd_final') {
                break;
            }
        }
        $basePath = '/' . implode('/', $basePathArray);
    }

    define('BASE_URL', $protocol . $host . $basePath);
}
?>