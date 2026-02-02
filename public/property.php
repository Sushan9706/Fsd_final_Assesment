<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT p.*, u.full_name, u.email as agent_email, u.phone as agent_phone, u.image as agent_image 
                       FROM properties p 
                       JOIN users u ON p.agent_id = u.id 
                       WHERE p.id = ?");
$stmt->execute([$id]);
$property = $stmt->fetch();

if (!$property) {
    echo '<div class="container"><p>Property not found.</p></div>';
    include __DIR__ . '/../includes/footer.php';
    exit();
}

// Get images
$stmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id = ?");
$stmt->execute([$id]);
$images = $stmt->fetchAll();
?>

<div class="property-details-page section-padding">
    <div class="container">
        <div class="property-header">
            <h1><?php echo e($property['title']); ?></h1>
            <div class="property-meta">
                <span><i class="fas fa-map-marker-alt"></i>
                    <?php echo e($property['location']); ?>
                </span>
                <span class="badge badge-type">
                    <?php echo e($property['type']); ?>
                </span>
                <span class="badge badge-status <?php echo 'badge-' . $property['status']; ?>">
                    <?php echo e(ucfirst($property['status'])); ?>
                </span>
            </div>
        </div>

        <div class="property-main">
            <div class="property-gallery">
                <?php if (empty($images)): ?>
                    <img src="/fsd_final/assets/uploads/properties/default_property.png" alt="Property">
                <?php else: ?>
                    <div class="main-image">
                        <img src="/fsd_final/assets/uploads/properties/<?php echo e($images[0]['image_path']); ?>"
                            alt="Property">
                    </div>
                    <?php if (count($images) > 1): ?>
                        <div class="thumbnail-grid">
                            <?php foreach (array_slice($images, 1) as $img): ?>
                                <img src="/fsd_final/assets/uploads/properties/<?php echo e($img['image_path']); ?>" alt="Property">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="property-info">
                <div class="info-card">
                    <h2>Description</h2>
                    <p><?php echo nl2br(e($property['description'])); ?></p>
                </div>

                <div class="info-card">
                    <h2>Details</h2>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="label">Price:</span>
                            <span class="value"><?php echo formatPrice($property['price']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Type:</span>
                            <span class="value"><?php echo e($property['type']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Status:</span>
                            <span class="value"><?php echo e(ucfirst($property['status'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="property-sidebar">
            <div class="agent-card">
                <h3>Contact Agent</h3>
                <div class="agent-info">
                    <img src="/fsd_final/assets/uploads/agents/<?php echo e($property['agent_image']); ?>"
                        onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($property['full_name']); ?>'"
                        alt="Agent">
                    <div>
                        <h4><?php echo e($property['full_name']); ?></h4>
                        <p><i class="fas fa-phone"></i> <?php echo e($property['agent_phone']); ?></p>
                        <p><i class="fas fa-envelope"></i> <?php echo e($property['agent_email']); ?></p>
                    </div>
                </div>

                <form id="enquiry-form" class="enquiry-form">
                    <input type="hidden" name="property_id" value="<?php echo $id; ?>">
                    <input type="hidden" name="agent_id" value="<?php echo $property['agent_id']; ?>">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="phone" placeholder="Your Phone">
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="I am interested in this property..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Send Enquiry</button>
                    <div id="enquiry-status"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('enquiry-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const statusDiv = document.getElementById('enquiry-status');
        const form = e.target;
        const formData = new FormData(form);

        statusDiv.innerHTML = 'Sending...';

        try {
            const response = await fetch('/fsd_final/ajax/send_enquiry.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.status === 'success') {
                statusDiv.innerHTML = '<p class="alert alert-success">' + data.message + '</p>';
                form.reset();
            } else {
                statusDiv.innerHTML = '<p class="alert alert-danger">' + data.message + '</p>';
            }
        } catch (error) {
            statusDiv.innerHTML = '<p class="alert alert-danger">Error sending enquiry.</p>';
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
