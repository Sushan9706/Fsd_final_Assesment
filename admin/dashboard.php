<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
checkAdmin();
require_once __DIR__ . '/../includes/header.php';

$agent_id = $_SESSION['user_id'];

$total_agent_properties = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE agent_id = ?");
$total_agent_properties->execute([$agent_id]);
$total_agent_count = $total_agent_properties->fetchColumn();

$total_properties = $pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn();
$sold_properties = $pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'sold'")->fetchColumn();
$booked_properties = $pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'booked'")->fetchColumn();
$available_properties = $pdo->query("SELECT COUNT(*) FROM properties WHERE status = 'available'")->fetchColumn();
?>

<div class="admin-dashboard section-padding">
    <div class="container">
        <div class="admin-header">
            <div>
                <h1>Welcome, <?php echo e($_SESSION['full_name']); ?></h1>
                <p><?php echo $_SESSION['role'] === 'super_admin' ? 'Super Admin Dashboard' : 'Agent Dashboard'; ?></p>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-home"></i></div>
                <div class="stat-info">
                    <h3>My Properties</h3>
                    <p class="stat-number"><?php echo $total_agent_count; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-building"></i></div>
                <div class="stat-info">
                    <h3>Total (All)</h3>
                    <p class="stat-number"><?php echo $total_properties; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon danger"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <h3>Sold</h3>
                    <p class="stat-number"><?php echo $sold_properties; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon accent"><i class="fas fa-bookmark"></i></div>
                <div class="stat-info">
                    <h3>Booked</h3>
                    <p class="stat-number"><?php echo $booked_properties; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon success"><i class="fas fa-check"></i></div>
                <div class="stat-info">
                    <h3>Available</h3>
                    <p class="stat-number"><?php echo $available_properties; ?></p>
                </div>
            </div>
        </div>

        <div class="dashboard-actions">
            <?php if ($_SESSION['role'] === 'super_admin'): ?>
                <a href="properties.php" class="btn btn-outline"><i class="fas fa-building"></i> Manage All Properties</a>
            <?php else: ?>
                <a href="add_property.php" class="btn btn-outline"><i class="fas fa-plus"></i> Add New Property</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>