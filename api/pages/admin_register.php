<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Check if an admin already exists
try {
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE is_admin = 1");
    $adminCount = $stmt->fetchColumn();

    if ($adminCount > 0) {
        // Redirect if admin already exists
        header('Location: login.php');
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$message = '';
$alertType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $message = 'All fields are required.';
        $alertType = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email address.';
        $alertType = 'danger';
    } elseif ($password !== $confirmPassword) {
        $message = 'Passwords do not match.';
        $alertType = 'danger';
    } else {
        try {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
            $stmt->execute([':username' => $username, ':email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                $message = 'Username or email already exists.';
                $alertType = 'danger';
            } else {
                // Create the admin user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("
                    INSERT INTO users (username, email, password, is_admin)
                    VALUES (:username, :email, :password, 1)
                ");
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password' => $hashedPassword
                ]);

                $message = 'Admin account created successfully. You can now log in.';
                $alertType = 'success';
            }
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $alertType = 'danger';
        }
    }
}

$pageTitle = 'Register Admin';
require_once '../includes/header.php';
?>

<div class="form-container">
    <h2>Register First Admin</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $alertType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Register Admin</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>