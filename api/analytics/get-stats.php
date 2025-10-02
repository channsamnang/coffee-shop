<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/auth.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();

// Total sales
$query = "SELECT SUM(total_amount) as total_sales FROM orders WHERE status != 'Cancelled'";
$stmt = $db->prepare($query);
$stmt->execute();
$total_sales = $stmt->fetch()['total_sales'] ?? 0;

// Total orders
$query = "SELECT COUNT(*) as total_orders FROM orders";
$stmt = $db->prepare($query);
$stmt->execute();
$total_orders = $stmt->fetch()['total_orders'];

// Total users
$query = "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'";
$stmt = $db->prepare($query);
$stmt->execute();
$total_users = $stmt->fetch()['total_users'];

// Orders by status
$query = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
$stmt = $db->prepare($query);
$stmt->execute();
$orders_by_status = $stmt->fetchAll();

// Sales by day (last 7 days)
$query = "SELECT DATE(created_at) as date, SUM(total_amount) as sales, COUNT(*) as orders 
          FROM orders 
          WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND status != 'Cancelled'
          GROUP BY DATE(created_at) 
          ORDER BY date";
$stmt = $db->prepare($query);
$stmt->execute();
$daily_sales = $stmt->fetchAll();

// Top selling items
$query = "SELECT ci.name, ci.category, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as revenue
          FROM order_items oi
          JOIN coffee_items ci ON oi.coffee_item_id = ci.id
          JOIN orders o ON oi.order_id = o.id
          WHERE o.status != 'Cancelled'
          GROUP BY ci.id
          ORDER BY total_sold DESC
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$top_items = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'stats' => [
        'total_sales' => $total_sales,
        'total_orders' => $total_orders,
        'total_users' => $total_users,
        'orders_by_status' => $orders_by_status,
        'daily_sales' => $daily_sales,
        'top_items' => $top_items
    ]
]);