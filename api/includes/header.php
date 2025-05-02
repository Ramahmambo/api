
<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$currentUser = getCurrentUser();
$userBalance = $currentUser ? $currentUser['balance'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milan Booster - We provide safe services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="../pages/index.php">Milan Booster</a>
                </div>
                <nav class="nav-menu">
                    <ul>
                        <li><a href="../pages/index.php">Home</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="../pages/order.php">Order</a></li>
                            <li><a href="../pages/order_details.php">Order Details</a></li>
                            <li><a href="../pages/contact.php">Contact</a></li>
                            <?php if (isAdmin()): ?>
                                <li><a href="../pages/admin.php">Admin</a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="user-actions">
                    <?php if (isLoggedIn()): ?>
                        <div class="user-balance">
                            Balance: $<?php echo number_format($userBalance, 2); ?>
                        </div>
                        <div class="user-menu">
                            <span><?php echo htmlspecialchars($currentUser['username']); ?></span>
                            <a href="../pages/logout.php" class="btn btn-logout">Logout</a>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <a href="../pages/login.php" class="btn btn-login">Login</a>
                            <a href="../pages/register.php" class="btn btn-signup">Sign Up</a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="mobile-menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>
    <main class="main-content">
        <div class="container">