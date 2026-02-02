<?php
require_once __DIR__ . '/../config/db.php';

try {
    // Select all image paths
    $stmt = $pdo->query("SELECT id, image_path FROM property_images");
    $images = $stmt->fetchAll();

    $count = 0;
    foreach ($images as $img) {
        $path = $img['image_path'];
        // If path contains 'assets/uploads/properties/', remove it
        if (strpos($path, 'assets/uploads/properties/') === 0) {
            $new_path = str_replace('assets/uploads/properties/', '', $path);
            $update = $pdo->prepare("UPDATE property_images SET image_path = ? WHERE id = ?");
            $update->execute([$new_path, $img['id']]);
            $count++;
        }
    }

    echo "Successfully cleaned up $count image paths.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>