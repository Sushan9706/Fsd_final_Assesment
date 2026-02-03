<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

checkAdmin();

$id = $_POST['id'] ?? 0;
$property_id = $_POST['property_id'] ?? 0;

if (!$id || !$property_id) {
    header("Location: edit_property.php?id=" . intval($property_id));
    exit;
}

// Verify ownership or super admin
$stmt = $pdo->prepare("SELECT p.agent_id, pi.image_path FROM property_images pi JOIN properties p ON pi.property_id = p.id WHERE pi.id = ?");
$stmt->execute([$id]);
$img = $stmt->fetch();
if (!$img) {
    header("Location: edit_property.php?id=" . intval($property_id));
    exit;
}

if ($_SESSION['role'] !== 'super_admin' && $img['agent_id'] != $_SESSION['user_id']) {
    die("Unauthorized access.");
}

$path = __DIR__ . '/../assets/uploads/properties/' . $img['image_path'];
if (file_exists($path)) {
    @unlink($path);
}

$del = $pdo->prepare("DELETE FROM property_images WHERE id = ?");
$del->execute([$id]);

header("Location: edit_property.php?id=" . intval($property_id) . "&img_deleted=1");
exit;