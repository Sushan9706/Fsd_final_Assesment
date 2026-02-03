<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

checkAdmin();

$id = $_POST['id'] ?? 0;
if (!$id) {
    header('Location: enquiries.php');
    exit;
}

// Verify permission: only super_admin or the agent assigned to the enquiry can delete
$check = $pdo->prepare("SELECT agent_id FROM enquiries WHERE id = ?");
$check->execute([$id]);
$row = $check->fetch();
if (!$row) {
    header('Location: enquiries.php');
    exit;
}
if ($_SESSION['role'] !== 'super_admin' && $row['agent_id'] != $_SESSION['user_id']) {
    die('Unauthorized access.');
}

$del = $pdo->prepare("DELETE FROM enquiries WHERE id = ?");
$del->execute([$id]);

header('Location: enquiries.php');
exit;