<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkAdmin();

$id = $_GET['id'] ?? 0;
$agent_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$id]);
$property = $stmt->fetch();

// Fetch existing images for this property
$stmt = $pdo->prepare("SELECT * FROM property_images WHERE property_id = ?");
$stmt->execute([$id]);
$images = $stmt->fetchAll();

if (!$property || ($_SESSION['role'] !== 'super_admin' && $property['agent_id'] != $agent_id)) {
    die("Unauthorized access.");
} 

$error = '';
$success = '';

// Show messages from image delete and update
if (isset($_GET['img_deleted'])) {
    $success = 'Image deleted successfully.';
}
if (isset($_GET['updated'])) {
    $success = 'Property updated successfully.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $price = $_POST['price'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $type = $_POST['type'] ?? '';
    $status = $_POST['status'] ?? 'available';

    // Image upload settings (same as add_property)
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize    = 2 * 1024 * 1024;
    $upload_dir = __DIR__ . '/../assets/uploads/properties/';

    if ($title && $price && $location && $type) {
        $stmt = $pdo->prepare("UPDATE properties SET title = ?, price = ?, location = ?, description = ?, type = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$title, $price, $location, $description, $type, $status, $id])) {
            // Handle image replacement when new images are uploaded (replaces existing images)
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $uploadedFiles = [];
            $uploadErrors = [];

            if (!empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['name'] as $key => $name) {
                    if (count($uploadedFiles) >= 3) break; // max 3 images
                    $tmp_name = $_FILES['images']['tmp_name'][$key];
                    $size     = $_FILES['images']['size'][$key];
                    $ext      = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                    if (!in_array($ext, $allowedExt)) {
                        $uploadErrors[] = htmlspecialchars($name) . ' - invalid file type.';
                        continue;
                    }
                    if ($size > $maxSize) {
                        $uploadErrors[] = htmlspecialchars($name) . ' - file too large.';
                        continue;
                    }

                    $new_name = uniqid('prop_', true) . '.' . $ext;
                    $target   = $upload_dir . $new_name;

                    if (move_uploaded_file($tmp_name, $target)) {
                        $uploadedFiles[] = $new_name;
                    } else {
                        $uploadErrors[] = htmlspecialchars($name) . ' - failed to move uploaded file.';
                    }
                }

                // If at least one new file uploaded successfully, replace existing images
                if (!empty($uploadedFiles)) {
                    // fetch old images
                    $oldStmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id = ?");
                    $oldStmt->execute([$id]);
                    $oldImgs = $oldStmt->fetchAll();

                    // delete old files from disk
                    foreach ($oldImgs as $old) {
                        $oldPath = $upload_dir . $old['image_path'];
                        if (file_exists($oldPath)) @unlink($oldPath);
                    }

                    // remove old DB records
                    $del = $pdo->prepare("DELETE FROM property_images WHERE property_id = ?");
                    $del->execute([$id]);

                    // insert uploaded files into DB
                    $stmtImg = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                    foreach ($uploadedFiles as $f) {
                        $stmtImg->execute([$id, $f]);
                    }
                }
            }

            // Refresh images list so changes are visible
            $stmt = $pdo->prepare("SELECT * FROM property_images WHERE property_id = ?");
            $stmt->execute([$id]);
            $images = $stmt->fetchAll();

            $success = "Property updated successfully!";
            if (!empty($uploadErrors)) {
                $error = implode('<br>', $uploadErrors);
            }
        } else {
            $error = "Update failed.";
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-form-page section-padding">
    <div class="container max-w-800">
        <div class="admin-header">
            <h1>Edit Property</h1>
            <a href="properties.php" class="btn btn-outline">Back to List</a>
        </div>

        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo e($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo e($success); ?></div>
            <?php endif; ?>

            <form action="edit_property.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Property Title *</label>
                    <input type="text" name="title" id="title" value="<?php echo e($property['title']); ?>" required>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="price">Price (NRP) *</label>
                        <input type="number" name="price" id="price" value="<?php echo e($property['price']); ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location *</label>
                        <input type="text" name="location" id="location" value="<?php echo e($property['location']); ?>"
                            required>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="type">Property Type *</label>
                        <select name="type" id="type" required>
                            <option value="Apartment" <?php echo $property['type'] == 'Apartment' ? 'selected' : ''; ?>>
                                Apartment</option>
                            <option value="House" <?php echo $property['type'] == 'House' ? 'selected' : ''; ?>>House
                            </option>
                            <option value="Land" <?php echo $property['type'] == 'Land' ? 'selected' : ''; ?>>Land
                            </option>
                            <option value="Commercial" <?php echo $property['type'] == 'Commercial' ? 'selected' : ''; ?>>
                                Commercial</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <div class="radio-group">
                            <label><input type="radio" name="status" value="available" <?php echo $property['status'] == 'available' ? 'checked' : ''; ?>> Available</label>
                            <label><input type="radio" name="status" value="booked" <?php echo $property['status'] == 'booked' ? 'checked' : ''; ?>> Booked</label>
                            <label><input type="radio" name="status" value="sold" <?php echo $property['status'] == 'sold' ? 'checked' : ''; ?>> Sold</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description"
                        rows="5"><?php echo e($property['description']); ?></textarea>
                </div>



                <div class="form-group">
                    <label for="images">Upload Images (max 3)</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="file-input">
                    <div id="images-preview" class="images-preview"></div>
                </div>

                <div class="mt-30">
                    <button type="submit" class="btn btn-primary w-full">Update Property</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>