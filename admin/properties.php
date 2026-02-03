<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkAdmin();
require_once __DIR__ . '/../includes/header.php';

$agent_id = $_SESSION['user_id'];

// If super admin, show all properties, else show only agent's
if ($_SESSION['role'] === 'super_admin') {
    $stmt = $pdo->query("SELECT p.*, u.full_name as agent_name FROM properties p JOIN users u ON p.agent_id = u.id ORDER BY p.created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT p.*, 'Me' as agent_name FROM properties p WHERE p.agent_id = ? ORDER BY p.created_at DESC");
    $stmt->execute([$agent_id]);
}
$properties = $stmt->fetchAll();
?>

<div class="admin-properties section-padding">
    <div class="container">
        <div class="admin-header">
            <h1>Property Management</h1>
            <a href="add_property.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Property</a>
        </div>

        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Agent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $property): ?>
                        <tr>
                            <td><?php echo e($property['title']); ?></td>
                            <td><?php echo e($property['location']); ?></td>
                            <td><?php echo formatPrice($property['price']); ?></td>
                            <td><?php echo e($property['type']); ?></td>
                            <td><span
                                    class="badge badge-<?php echo $property['status']; ?>"><?php echo ucfirst($property['status']); ?></span>
                            </td>
                            <td><?php echo e($property['agent_name']); ?></td>
                            <td class="actions">
                                <a href="edit_property.php?id=<?php echo $property['id']; ?>" class="action-btn edit"
                                    title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="delete_property.php?id=<?php echo $property['id']; ?>" class="action-btn delete"
                                    title="Delete" onclick="return confirm('Are you sure?')"><i
                                        class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($properties)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No properties found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>