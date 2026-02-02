<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
checkSuperAdmin();

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'agent'");
$stmt->execute([$id]);

header("Location: users.php");
exit();
?>