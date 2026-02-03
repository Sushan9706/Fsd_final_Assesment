<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$location = $_GET['location'] ?? '';
$type = $_GET['type'] ?? '';
$max_price = $_GET['max_price'] ?? '';

$query = "SELECT p.*, pi.image_path as main_image 
          FROM properties p 
          LEFT JOIN property_images pi ON p.id = pi.property_id 
          WHERE 1=1";
$params = [];

if ($location) {
    $query .= " AND p.location LIKE ?";
    $params[] = "%$location%";
}

if ($type) {
    $query .= " AND p.type = ?";
    $params[] = $type;
}

if ($max_price) {
    $query .= " AND p.price <= ?";
    $params[] = $max_price;
}

$query .= " GROUP BY p.id ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$properties = $stmt->fetchAll();

if (empty($properties)) {
    echo '<p class="no-results">No properties found matching your criteria.</p>';
} else {
    foreach ($properties as $property) {
        $image = !empty($property['main_image']) ? 'assets/uploads/properties/' . $property['main_image'] : 'assets/uploads/properties/default_property.png';
        ?>
        <div class="property-card">
            <div class="property-image">
                <img src="<?php echo BASE_URL; ?>/<?php echo e($image); ?>" alt="<?php echo e($property['title']); ?>">
                <div class="property-badges">
                    <span class="badge badge-type">
                        <?php echo e($property['type']); ?>
                    </span>
                    <span class="badge badge-status <?php echo 'badge-' . $property['status']; ?>">
                        <?php echo e(ucfirst($property['status'])); ?>
                    </span>
                </div>
            </div>
            <div class="property-content">
                <h3 class="property-title">
                    <?php echo e($property['title']); ?>
                </h3>
                <p class="property-location"><i class="fas fa-map-marker-alt"></i>
                    <?php echo e($property['location']); ?>
                </p>
                <div class="property-price">
                    <?php echo formatPrice($property['price']); ?>
                </div>
                <a href="property.php?id=<?php echo $property['id']; ?>" class="btn btn-outline w-full text-center">View
                    Details</a>
            </div>
        </div>
        <?php
    }
}
?>