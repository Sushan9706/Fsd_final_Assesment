<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
checkAdmin();

$id = $_GET['id'] ?? 0;
$agent_id = $_SESSION['user_id'];

// Check if property exists and belongs to agent
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$id]);
$property = $stmt->fetch();

if (!$property || ($_SESSION['role'] !== 'super_admin' && $property['agent_id'] != $agent_id)) {
    die("Unauthorized access.");
}

$stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
$stmt->execute([$id]);

header("Location: properties.php");
exit();
?>