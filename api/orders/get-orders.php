<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Require user to be logged in
$auth->requireLogin();

$is_admin = $auth->isAdmin();
$user_id = $_SESSION['user_id'];

$query = "SELECT o.*, 
          GROUP_CONCAT(
            JSON_OBJECT(
                'id', oi.item_id,
                'name', ci.name,
                'quantity', oi.quantity,
                'price', oi.price
            )
          ) as items
          FROM orders o
          LEFT JOIN order_items oi ON o.id = oi.order_id
          LEFT JOIN coffee_items ci ON oi.item_id = ci.id ";

// If not admin, only show user's orders
if (!$is_admin) {
    $query .= "WHERE o.user_id = ? ";
}

$query .= "GROUP BY o.id ORDER BY o.created_at DESC";

$stmt = $db->prepare($query);

if (!$is_admin) {
    $stmt->execute([$user_id]);
} else {
    $stmt->execute();
}

$orders = $stmt->fetchAll();

// Parse the items JSON string for each order
foreach ($orders as &$order) {
    $order['items'] = array_map(function($item) {
        return json_decode($item, true);
    }, explode(',', $order['items']));
}

echo json_encode([
    'success' => true,
    'orders' => $orders
]);
?>