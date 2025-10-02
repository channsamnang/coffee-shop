<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Require user to be logged in
$auth->requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['items']) || empty($data['items']) || !isset($data['delivery_address'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

try {
    $db->beginTransaction();

    // Create order
    $query = "INSERT INTO orders (user_id, total_amount, delivery_address, status, created_at) VALUES (?, ?, ?, 'Pending', NOW())";
    $stmt = $db->prepare($query);
    
    $total_amount = 0;
    foreach ($data['items'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    $stmt->execute([
        $_SESSION['user_id'],
        $total_amount,
        $data['delivery_address']
    ]);
    
    $order_id = $db->lastInsertId();

    // Add order items
    $query = "INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);

    foreach ($data['items'] as $item) {
        $stmt->execute([
            $order_id,
            $item['id'],
            $item['quantity'],
            $item['price']
        ]);
    }

    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Order created successfully',
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create order']);
}
?>