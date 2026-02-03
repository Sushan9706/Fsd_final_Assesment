<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkSuperAdmin();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'agent'");
$stmt->execute([$id]);
$agent = $stmt->fetch();

if (!$agent) {
    die("Agent not found.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($full_name && $email) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $phone, $id]);

            if (!empty($_POST['password'])) {
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $id]);
            }

            if (!empty($_FILES['image']['name'])) {
                $upload_dir = '../assets/uploads/agents/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $image_name = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);

                $stmt = $pdo->prepare("UPDATE users SET image = ? WHERE id = ?");
                $stmt->execute([$image_name, $id]);
            }

            $success = "Agent updated successfully!";
            header("Refresh: 2; URL=users.php");
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-form-page section-padding">
    <div class="container max-w-600">
        <div class="admin-header">
            <h1>Edit Agent</h1>
            <a href="users.php" class="btn btn-outline">Back to List</a>
        </div>

        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo e($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo e($success); ?></div>
            <?php endif; ?>

            <form action="edit_agent.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" name="full_name" id="full_name" value="<?php echo e($agent['full_name']); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" value="<?php echo e($agent['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="<?php echo e($agent['phone']); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password (Leave blank to keep current)</label>
                    <input type="password" name="password" id="password">
                </div>
                <div class="form-group">
                    <label for="image">Update Photo</label>
                    <input type="file" name="image" id="image" accept="image/*" class="file-input">
                </div>
                <div class="mt-30">
                    <button type="submit" class="btn btn-primary w-full">Update Agent Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>