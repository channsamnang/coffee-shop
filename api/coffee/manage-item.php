<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Require admin access
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create new item
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['name']) || !isset($data['price']) || !isset($data['category'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }

    $query = "INSERT INTO coffee_items (name, description, price, category, status) VALUES (?, ?, ?, ?, 'active')";
    $stmt = $db->prepare($query);
    
    try {
        $stmt->execute([
            $data['name'],
            $data['description'] ?? '',
            $data['price'],
            $data['category']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Item created successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create item']);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Update existing item
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Item ID required']);
        exit();
    }

    $updates = [];
    $params = [];

    if (isset($data['name'])) {
        $updates[] = "name = ?";
        $params[] = $data['name'];
    }
    if (isset($data['description'])) {
        $updates[] = "description = ?";
        $params[] = $data['description'];
    }
    if (isset($data['price'])) {
        $updates[] = "price = ?";
        $params[] = $data['price'];
    }
    if (isset($data['category'])) {
        $updates[] = "category = ?";
        $params[] = $data['category'];
    }
    if (isset($data['status'])) {
        $updates[] = "status = ?";
        $params[] = $data['status'];
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        exit();
    }

    $params[] = $data['id'];
    $query = "UPDATE coffee_items SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $db->prepare($query);
    
    try {
        $stmt->execute($params);
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update item']);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Delete item
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Item ID required']);
        exit();
    }

    $query = "UPDATE coffee_items SET status = 'deleted' WHERE id = ?";
    $stmt = $db->prepare($query);
    
    try {
        $stmt->execute([$data['id']]);
        echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete item']);
    }

} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>