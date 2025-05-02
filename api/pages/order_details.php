
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Redirect if not logged in
requireLogin();

$currentUser = getCurrentUser();
$orders = [];

try {
    $stmt = $conn->prepare("
        SELECT o.id, o.link, o.quantity, o.price, o.status, o.created_at,
               s.name as service_name, c.name as category_name
        FROM orders o
        JOIN services s ON o.service_id = s.id
        JOIN categories c ON s.category_id = c.id
        WHERE o.user_id = :userId
        ORDER BY o.created_at DESC
    ");
    $stmt->bindParam(':userId', $_SESSION['user_id']);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = 'Error loading orders: ' . $e->getMessage();
}

$pageTitle = 'Order Details';
require_once '../includes/header.php';
?>

<div class="page-title">
    <h1>Order Details</h1>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="card">
    <?php if (count($orders) > 0): ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service</th>
                        <th>Category</th>
                        <th>Link</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['category_name']); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($order['link']); ?>" target="_blank">
                                    <?php echo htmlspecialchars(substr($order['link'], 0, 30) . (strlen($order['link']) > 30 ? '...' : '')); ?>
                                </a>
                            </td>
                            <td><?php echo number_format($order['quantity']); ?></td>
                            <td>$<?php echo number_format($order['price'], 2); ?></td>
                            <td class="status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-orders">
            <p>You don't have any orders yet.</p>
            <a href="order.php" class="btn">Place an Order</a>
        </div>
    <?php endif; ?>
</div>

<style>
    .no-orders {
        text-align: center;
        padding: 30px 0;
    }
    
    .no-orders p {
        margin-bottom: 20px;
        font-size: 18px;
        color: #666;
    }
</style>

<?php
require_once '../includes/footer.php';
?>