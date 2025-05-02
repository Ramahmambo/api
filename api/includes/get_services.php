<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : null;

if (!$categoryId) {
    echo json_encode(['error' => 'Category ID is required']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT id, name, price_per_1000, min_quantity, max_quantity
        FROM services
        WHERE category_id = :categoryId
        ORDER BY name
    ");
    $stmt->bindParam(':categoryId', $categoryId);
    $stmt->execute();
    
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($services);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}