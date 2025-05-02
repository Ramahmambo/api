<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Redirect if not admin
requireAdmin();

$message = '';
$alertType = '';

// Handle adding funds to user
if (isset($_POST['add_funds'])) {
    $username = $_POST['username'] ?? '';
    $amount = (float)($_POST['amount'] ?? 0);
    
    if (empty($username) || $amount <= 0) {
        $message = 'Valid username and amount are required';
        $alertType = 'danger';
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, balance FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $newBalance = $user['balance'] + $amount;
                
                $updateStmt = $conn->prepare("UPDATE users SET balance = :balance WHERE id = :id");
                $updateStmt->bindParam(':balance', $newBalance);
                $updateStmt->bindParam(':id', $user['id']);
                $updateStmt->execute();
                
                $message = 'Funds added successfully';
                $alertType = 'success';
            } else {
                $message = 'User not found';
                $alertType = 'danger';
            }
        } catch(PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $alertType = 'danger';
        }
    }
}

// Handle adding new service
if (isset($_POST['add_service'])) {
    $categoryId = $_POST['category_id'] ?? '';
    $serviceName = $_POST['service_name'] ?? '';
    $price = (float)($_POST['price'] ?? 0);
    $minQuantity = (int)($_POST['min_quantity'] ?? 100);
    $maxQuantity = (int)($_POST['max_quantity'] ?? 10000);
    
    if (empty($categoryId) || empty($serviceName) || $price <= 0) {
        $message = 'All fields are required and price must be greater than 0';
        $alertType = 'danger';
    } else {
        try {
            $stmt = $conn->prepare("
                INSERT INTO services (category_id, name, price_per_1000, min_quantity, max_quantity) 
                VALUES (:categoryId, :name, :price, :minQty, :maxQty)
            ");
            $stmt->bindParam(':categoryId', $categoryId);
            $stmt->bindParam(':name', $serviceName);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':minQty', $minQuantity);
            $stmt->bindParam(':maxQty', $maxQuantity);
            $stmt->execute();
            
            $message = 'Service added successfully';
            $alertType = 'success';
        } catch(PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $alertType = 'danger';
        }
    }
}

// Handle updating service price
if (isset($_POST['update_service'])) {
    $serviceId = $_POST['service_id'] ?? '';
    $newPrice = (float)($_POST['new_price'] ?? 0);
    
    if (empty($serviceId) || $newPrice <= 0) {
        $message = 'Valid service and price are required';
        $alertType = 'danger';
    } else {
        try {
            $stmt = $conn->prepare("UPDATE services SET price_per_1000 = :price WHERE id = :id");
            $stmt->bindParam(':price', $newPrice);
            $stmt->bindParam(':id', $serviceId);
            $stmt->execute();
            
            $message = 'Service price updated successfully';
            $alertType = 'success';
        } catch(PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $alertType = 'danger';
        }
    }
}

// Handle adding new user
if (isset($_POST['add_user'])) {
    $username = $_POST['new_username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;
    
    if (empty($username) || empty($email) || empty($password)) {
        $message = 'All fields are required';
        $alertType = 'danger';
    } else {
        try {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                $message = 'Username or email already exists';
                $alertType = 'danger';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("
                    INSERT INTO users (username, email, password, is_admin) 
                    VALUES (:username, :email, :password, :isAdmin)
                ");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':isAdmin', $isAdmin);
                $stmt->execute();
                
                $message = 'User added successfully';
                $alertType = 'success';
            }
        } catch(PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $alertType = 'danger';
        }
    }
}

// Handle deleting a user
if (isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'] ?? '';
    
    if (empty($userId)) {
        $message = 'User ID is required';
        $alertType = 'danger';
    } elseif ($userId == $_SESSION['user_id']) {
        $message = 'You cannot delete your own account';
        $alertType = 'danger';
    } else {
        try {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            $message = 'User deleted successfully';
            $alertType = 'success';
        } catch(PDOException $e) {
            $message = 'Error: ' . $e->getMessage();
            $alertType = 'danger';
        }
    }
}

// Get all categories for forms
$categories = [];
try {
    $stmt = $conn->query("SELECT id, name FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Handle error
}

// Get all services for forms
$services = [];
try {
    $stmt = $conn->query("
        SELECT s.id, s.name, s.price_per_1000, c.name as category_name
        FROM services s
        JOIN categories c ON s.category_id = c.id
        ORDER BY c.name, s.name
    ");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Handle error
}

// Get all users
$users = [];
try {
    $stmt = $conn->query("
        SELECT id, username, email, balance, is_admin, created_at
        FROM users
        ORDER BY username
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Handle error
}

// Get all orders
$orders = [];
try {
    $stmt = $conn->query("
        SELECT o.id, o.link, o.quantity, o.price, o.status, o.created_at,
               s.name as service_name, c.name as category_name, u.username
        FROM orders o
        JOIN services s ON o.service_id = s.id
        JOIN categories c ON s.category_id = c.id
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT 100
    ");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Handle error
}

$pageTitle = 'Admin Dashboard';
$pageScripts = ['../assets/js/admin.js'];
require_once '../includes/header.php';
?>

<div class="page-title">
    <h1>Admin Dashboard</h1>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $alertType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="admin-tabs">
    <div class="admin-tab active" data-tab="manage-users">Manage Users</div>
    <div class="admin-tab" data-tab="manage-services">Manage Services</div>
    <div class="admin-tab" data-tab="view-orders">View Orders</div>
</div>

<div class="admin-panel active" id="manage-users">
    <div class="admin-section">
        <h3 class="admin-section-title">Add Funds to User</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="amount" class="form-label">Amount ($)</label>
                <input type="number" id="amount" name="amount" class="form-control" min="0.01" step="0.01" required>
            </div>
            <div class="form-group">
                <button type="submit" name="add_funds" class="btn">Add Funds</button>
            </div>
        </form>
    </div>
    
    <div class="admin-section">
        <h3 class="admin-section-title">Add New User</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label for="new_username" class="form-label">Username</label>
                <input type="text" id="new_username" name="new_username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" id="is_admin" name="is_admin" class="form-check-input">
                <label for="is_admin" class="form-check-label">Make Admin</label>
            </div>
            <div class="form-group">
                <button type="submit" name="add_user" class="btn">Add User</button>
            </div>
        </form>
    </div>
    
    <div class="admin-section">
        <h3 class="admin-section-title">Manage Users</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Balance</th>
                        <th>Admin</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>$<?php echo number_format($user['balance'], 2); ?></td>
                            <td><?php echo $user['is_admin'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">Current User</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="admin-panel" id="manage-services">
    <div class="admin-section">
        <h3 class="admin-section-title">Add New Service</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label for="category_id" class="form-label">Category</label>
                <select id="category_id" name="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="service_name" class="form-label">Service Name</label>
                <input type="text" id="service_name" name="service_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="price" class="form-label">Price per 1000</label>
                <input type="number" id="price" name="price" class="form-control" min="0.01" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="min_quantity" class="form-label">Minimum Quantity</label>
                <input type="number" id="min_quantity" name="min_quantity" class="form-control" value="100" required>
            </div>
            <div class="form-group">
                <label for="max_quantity" class="form-label">Maximum Quantity</label>
                <input type="number" id="max_quantity" name="max_quantity" class="form-control" value="10000" required>
            </div>
            <div class="form-group">
                <button type="submit" name="add_service" class="btn">Add Service</button>
            </div>
        </form>
    </div>
    
    <div class="admin-section">
        <h3 class="admin-section-title">Update Service Price</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label for="service_id" class="form-label">Service</label>
                <select id="service_id" name="service_id" class="form-select" required>
                    <option value="">Select Service</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?php echo $service['id']; ?>">
                            <?php echo htmlspecialchars($service['category_name'] . ' - ' . $service['name']); ?> 
                            (Current price: $<?php echo number_format($service['price_per_1000'], 2); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="new_price" class="form-label">New Price per 1000</label>
                <input type="number" id="new_price" name="new_price" class="form-control" min="0.01" step="0.01" required>
            </div>
            <div class="form-group">
                <button type="submit" name="update_service" class="btn">Update Price</button>
            </div>
        </form>
    </div>
    
    <div class="admin-section">
        <h3 class="admin-section-title">Service List</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Service Name</th>
                        <th>Price per 1000</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?php echo $service['id']; ?></td>
                            <td><?php echo htmlspecialchars($service['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($service['name']); ?></td>
                            <td>$<?php echo number_format($service['price_per_1000'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="admin-panel" id="view-orders">
    <div class="admin-section">
        <h3 class="admin-section-title">Recent Orders</h3>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Service</th>
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
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td><?php echo htmlspecialchars($order['category_name'] . ' - ' . $order['service_name']); ?></td>
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
    </div>
</div>

<style>
    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
    }
    
    .btn-danger {
        background-color: var(--danger-color);
    }
    
    .btn-danger:hover {
        background-color: #c82333;
    }
    
    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .form-check-input {
        margin-right: 10px;
    }
    
    .text-muted {
        color: #6c757d;
    }
</style>

<?php
require_once '../includes/footer.php';
?>