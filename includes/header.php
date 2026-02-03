<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate Listing Platform</title>

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">

    <!-- Font Awesome (REQUIRED) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
</head>

<body>

    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>/index.php">
                    Real<span>Estate</span>
                </a>
            </div>

            <nav class="nav-menu">
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>

                    <?php if (!empty($_SESSION['user_id'])): ?>

                        <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'super_admin'): ?>
                            <li>
                                <a href="<?php echo BASE_URL; ?>/superadmin/users.php">
                                    Manage Agents
                                </a>
                            </li>
                        <?php endif; ?>

                        <li>
                            <a href="<?php echo BASE_URL; ?>/admin/dashboard.php">
                                Dashboard
                            </a>
                        </li>

                        <li>
                            <a href="<?php echo BASE_URL; ?>/admin/enquiries.php">
                                Enquiries
                            </a>
                        </li>

                        <li>
                            <a href="<?php echo BASE_URL; ?>/logout.php" class="btn btn-outline">
                                Logout
                            </a>
                        </li>

                    <?php else: ?>

                        <li>
                            <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-primary">
                                Agent Login
                            </a>
                        </li>

                    <?php endif; ?>
                </ul>
            </nav>

            <div class="menu-toggle" id="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <div class="mobile-overlay" id="mobile-overlay"></div>
    <main>