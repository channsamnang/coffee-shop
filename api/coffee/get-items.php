<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM coffee_items WHERE status = 'active' ORDER BY category, name";
$stmt = $db->prepare($query);
$stmt->execute();

$items = $stmt->fetchAll();

echo json_encode([
    'success' => true,
    'items' => $items
]);
?>