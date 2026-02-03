<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkAdmin();

// Fetch enquiries with property title and agent name
if (!empty($_SESSION['role']) && $_SESSION['role'] === 'super_admin') {
    $stmt = $pdo->query("SELECT e.*, p.title AS property_title, u.full_name AS agent_name
                         FROM enquiries e
                         LEFT JOIN properties p ON e.property_id = p.id
                         LEFT JOIN users u ON e.agent_id = u.id
                         ORDER BY e.created_at DESC");
    $enquiries = $stmt->fetchAll();
} else {
    // Agents only see enquiries for their properties (agent_id on enquiries)
    $stmt = $pdo->prepare("SELECT e.*, p.title AS property_title, u.full_name AS agent_name
                         FROM enquiries e
                         LEFT JOIN properties p ON e.property_id = p.id
                         LEFT JOIN users u ON e.agent_id = u.id
                         WHERE e.agent_id = ?
                         ORDER BY e.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $enquiries = $stmt->fetchAll();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="section-padding">
    <div class="container max-w-1000">
        <div class="admin-header">
            <h1>Enquiries Received</h1>
            <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
        </div>

        <?php if (empty($enquiries)): ?>
            <div class="info-card">No enquiries found.</div>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Agent</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Message</th>
                            <th>Received</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enquiries as $enq): ?>
                            <tr>
                                <td><?php echo e($enq['property_title'] ?? '—'); ?></td>
                                <td><?php echo e($enq['agent_name'] ?? '—'); ?></td>
                                <td><?php echo e($enq['viewer_name']); ?></td>
                                <td><?php echo e($enq['viewer_email']); ?></td>
                                <td><?php echo e($enq['viewer_phone']); ?></td>
                                <td style="max-width:320px; white-space:pre-wrap; word-break:break-word"><?php echo e($enq['message']); ?></td>
                                <td><?php echo e($enq['created_at']); ?></td>
                                <td class="actions">
                                    <form action="delete_enquiry.php" method="POST" onsubmit="return confirm('Delete this enquiry?');" style="display:inline-block">
                                        <input type="hidden" name="id" value="<?php echo $enq['id']; ?>">
                                        <button type="submit" class="action-btn delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>