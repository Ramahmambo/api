
<?php
// Database connection settings
$host = 'localhost';
$dbname = 'milan_booster';
$username = 'root'; // Change in production
$password = ''; // Change in production

// Create database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get user balance
function getUserBalance($userId) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT balance FROM users WHERE id = :userId");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['balance'];
        }
        
        return 0;
    } catch(PDOException $e) {
        return 0;
    }
}

// Function to update user balance
function updateUserBalance($userId, $newBalance) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("UPDATE users SET balance = :balance WHERE id = :userId");
        $stmt->bindParam(':balance', $newBalance);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        
        return true;
    } catch(PDOException $e) {
        return false;
    }
}