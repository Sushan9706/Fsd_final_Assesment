<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkSuperAdmin();
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("SELECT * FROM users WHERE role = 'agent' ORDER BY created_at DESC");
$agents = $stmt->fetchAll();
?>

<div class="admin-users section-padding">
    <div class="container">
        <div class="admin-header">
            <h1>Agent Management</h1>
            <a href="add_agent.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Agent</a>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($agents as $agent): ?>
                        <tr>
                            <td><img src="<?php echo BASE_URL; ?>/assets/uploads/agents/<?php echo e($agent['image']); ?>"
                                    onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($agent['full_name']); ?>'"
                                    class="avatar-sm"></td>
                            <td><?php echo e($agent['full_name']); ?></td>
                            <td><?php echo e($agent['email']); ?></td>
                            <td><?php echo e($agent['phone']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($agent['created_at'])); ?></td>
                            <td class="actions">
                                <a href="edit_agent.php?id=<?php echo $agent['id']; ?>" class="action-btn edit"
                                    title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="delete_agent.php?id=<?php echo $agent['id']; ?>" class="action-btn delete"
                                    title="Delete"
                                    onclick="return confirm('Are you sure you want to delete this agent? All their properties will also be deleted.')"><i
                                        class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($agents)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No agents found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>