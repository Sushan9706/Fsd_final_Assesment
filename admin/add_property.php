<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

checkAdmin();

$error = '';
$success = '';

$allowedTypes  = ['Apartment', 'House', 'Land', 'Commercial'];
$allowedStatus = ['available', 'booked', 'sold'];
$allowedExt    = ['jpg', 'jpeg', 'png', 'webp'];
$maxSize       = 2 * 1024 * 1024;
$upload_dir    = __DIR__ . '/../assets/uploads/properties/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $price       = (float) ($_POST['price'] ?? 0);
    $location    = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $type        = $_POST['type'] ?? '';
    $status      = $_POST['status'] ?? 'available';
    $agent_id    = $_SESSION['user_id'];

    if (!$title || !$price || !$location || !$type) {
        $error = "Please fill in all required fields.";
    } elseif ($price <= 0) {
        $error = "Price must be greater than 0.";
    } elseif (!in_array($type, $allowedTypes)) {
        $error = "Invalid property type.";
    } elseif (!in_array($status, $allowedStatus)) {
        $error = "Invalid property status.";
    } elseif (!empty($_FILES['images']['name'][0]) && count($_FILES['images']['name']) > 3) {
        $error = "You can upload a maximum of 3 images.";
    }

    if (!$error) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO properties 
                (agent_id, title, price, location, description, type, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $agent_id,
                $title,
                $price,
                $location,
                $description,
                $type,
                $status
            ]);

            $property_id = $pdo->lastInsertId();

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (!empty($_FILES['images']['name'][0])) {
                foreach ($_FILES['images']['name'] as $key => $name) {
                    $tmp_name = $_FILES['images']['tmp_name'][$key];
                    $size     = $_FILES['images']['size'][$key];
                    $ext      = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                    if (!in_array($ext, $allowedExt)) continue;
                    if ($size > $maxSize) continue;

                    $new_name  = uniqid('prop_', true) . '.' . $ext;
                    $target    = $upload_dir . $new_name;
                    $db_path   = $new_name;

                    if (move_uploaded_file($tmp_name, $target)) {
                        $stmt = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                        $stmt->execute([$property_id, $db_path]);
                    }
                }
            }

            $pdo->commit();
            header("Location: properties.php?success=1");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error adding property. Please try again.";
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-form-page section-padding">
    <div class="container max-w-800">
        <div class="admin-header">
            <h1>Add New Property</h1>
            <a href="properties.php" class="btn btn-outline">Back to List</a>
        </div>

        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error); ?></div>
            <?php endif; ?>

            <form action="add_property.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Property Title *</label>
                    <input type="text" name="title" id="title" value="<?= e($title ?? '') ?>" required>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="price">Price (NRP) *</label>
                        <input type="number" name="price" id="price" value="<?= e($price ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location *</label>
                        <input type="text" name="location" id="location" value="<?= e($location ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group property-type">
                        <label for="type">Property Type *</label>
                        <select name="type" id="type" required>
                            <option value="">Select Type</option>
                            <?php foreach ($allowedTypes as $t): ?>
                                <option value="<?= $t ?>" <?= ($type ?? '') === $t ? 'selected' : '' ?>>
                                    <?= $t ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Status *</label>
                        <div class="radio-group">
                            <?php foreach ($allowedStatus as $s): ?>
                                <label>
                                    <input type="radio" name="status" value="<?= $s ?>"
                                        <?= ($status ?? 'available') === $s ? 'checked' : '' ?>>
                                    <?= ucfirst($s) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="5"><?= e($description ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="images">Property Images (Max 3, JPG/PNG/WebP)</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="file-input">
                </div>

                <div class="mt-30">
                    <button type="submit" class="btn btn-primary w-full">Add Property</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
