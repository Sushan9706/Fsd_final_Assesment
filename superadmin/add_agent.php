<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkSuperAdmin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($full_name && $email && $password) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $image_name = 'default_user.png';

            if (!empty($_FILES['image']['name'])) {
                $upload_dir = '../assets/uploads/agents/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $image_name = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
            }

            $stmt = $pdo->prepare("INSERT INTO users (role, full_name, email, phone, password, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute(['agent', $full_name, $email, $phone, $hashed_password, $image_name]);
            $success = "Agent added successfully!";
            header("Refresh: 2; URL=users.php");
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-form-page section-padding">
    <div class="container max-w-600">
        <div class="admin-header">
            <h1>Add New Agent</h1>
            <a href="users.php" class="btn btn-outline">Back to List</a>
        </div>

        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo e($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo e($success); ?></div>
            <?php endif; ?>

            <form action="add_agent.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="full_name">Full Name *</label>
                    <input type="text" name="full_name" id="full_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone">
                </div>
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="form-group">
                    <label for="image">Agent Photo</label>
                    <input type="file" name="image" id="image" accept="image/*" class="file-input">
                </div>
                <div class="mt-30">
                    <button type="submit" class="btn btn-primary w-full">Create Agent Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>