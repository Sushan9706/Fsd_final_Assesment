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

if (!$property || ($_SESSION['role'] !== 'super_admin' && $property['agent_id'] != $agent_id)) {
    die("Unauthorized access.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $price = $_POST['price'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $type = $_POST['type'] ?? '';
    $status = $_POST['status'] ?? 'available';

    if ($title && $price && $location && $type) {
        $stmt = $pdo->prepare("UPDATE properties SET title = ?, price = ?, location = ?, description = ?, type = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$title, $price, $location, $description, $type, $status, $id])) {
            $success = "Property updated successfully!";
            header("Refresh: 2; URL=properties.php");
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

            <form action="edit_property.php?id=<?php echo $id; ?>" method="POST">
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
                <div class="mt-30">
                    <button type="submit" class="btn btn-primary w-full">Update Property</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>