
<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $user['password'])) {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['is_admin'] = $user['is_admin'];
                    
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Invalid password';
                }
            } else {
                $error = 'User not found';
            }
        } catch(PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Login';
require_once '../includes/header.php';
?>

<div class="form-container">
    <h2 class="text-center mb-4">Login to Milan Booster</h2>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn form-btn">Login</button>
        </div>
        
        <p class="form-text">
            Don't have an account? <a href="register.php">Register</a>
        </p>
    </form>
</div>

<style>
    .form-container {
        max-width: 400px;
    }
    
    .text-center {
        text-align: center;
    }
    
    .mb-4 {
        margin-bottom: 20px;
    }
</style>

<?php
require_once '../includes/footer.php';
?>