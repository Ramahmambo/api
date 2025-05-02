<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Redirect if not logged in
requireLogin();

$currentUser = getCurrentUser();
$userBalance = $currentUser['balance'];
$message = '';
$alertType = '';

// Get all categories
$categories = [];
try {
    $stmt = $conn->query("SELECT id, name FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $message = 'Error loading categories: ' . $e->getMessage();
    $alertType = 'danger';
}

// Process order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = $_POST['category'] ?? '';
    $serviceId = $_POST['service'] ?? '';
    $link = $_POST['link'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    
    if (empty($serviceId) || empty($link) || $quantity <= 0 || $price <= 0) {
        $message = 'All fields are required and quantity must be greater than 0';
        $alertType = 'danger';
    } elseif ($price > $userBalance) {
        $message = 'Insufficient balance';
        $alertType = 'danger';
    } else {
        try {
            // Start transaction
            $conn->beginTransaction();
            
            // Insert order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, service_id, link, quantity, price) 
                VALUES (:userId, :serviceId, :link, :quantity, :price)");
            $stmt->bindParam(':userId', $_SESSION['user_id']);
            $stmt->bindParam(':serviceId', $serviceId);
            $stmt->bindParam(':link', $link);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':price', $price);
            $stmt->execute();
            
            // Update user balance
            $newBalance = $userBalance - $price;
            $stmt = $conn->prepare("UPDATE users SET balance = :balance WHERE id = :userId");
            $stmt->bindParam(':balance', $newBalance);
            $stmt->bindParam(':userId', $_SESSION['user_id']);
            $stmt->execute();
            
            // Commit transaction
            $conn->commit();
            
            $message = 'Order placed successfully';
            $alertType = 'success';
            $userBalance = $newBalance; // Update balance for display
        } catch(PDOException $e) {
            $conn->rollBack();
            $message = 'Error placing order: ' . $e->getMessage();
            $alertType = 'danger';
        }
    }
}

$pageTitle = 'Place an Order';
$pageScripts = ['../assets/js/order.js'];
require_once '../includes/header.php';
?>

<div class="page-title">
    <h1>Place an Order</h1>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $alertType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="order-form-container">
    <form id="orderForm" method="POST" action="">
        <div class="form-group">
            <label for="category" class="form-label">Category</label>
            <select id="category" name="category" class="form-select" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="service" class="form-label">Service</label>
            <select id="service" name="service" class="form-select" required disabled>
                <option value="">Select Category First</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="link" class="form-label">Link</label>
            <input type="text" id="link" name="link" class="form-control" placeholder="https://" required>
        </div>
        
        <div class="form-group">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="100" required>
            <small class="form-text text-muted">Minimum quantity: <span id="minQuantity">100</span></small>
        </div>
        
        <div class="order-summary">
            <div class="row">
                <div class="col">
                    <p><strong>Price:</strong></p>
                </div>
                <div class="col">
                    <div class="price-display">$<span id="priceDisplay">0.00</span></div>
                    <input type="hidden" id="price" name="price" value="0">
                </div>
            </div>
        </div>
        
        <div class="form-group mt-4">
            <button type="submit" class="btn form-btn" id="orderBtn" disabled>Place Order</button>
        </div>
    </form>
</div>

<style>
    .mt-4 {
        margin-top: 20px;
    }
    
    .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .col {
        flex: 1;
    }
    
    .form-text {
        color: #666;
        font-size: 14px;
        margin-top: 5px;
        display: block;
    }
</style>

<?php
require_once '../includes/footer.php';
?>