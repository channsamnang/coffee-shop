<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['full_name']) || !isset($data['email']) || !isset($data['password']) || !isset($data['username'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

try {
    // Check if email already exists
    if ($auth->emailExists($data['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit();
    }

    // Check if username already exists
    if ($auth->usernameExists($data['username'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username already taken']);
        exit();
    }

    $result = $auth->register([
        'full_name' => $data['full_name'],
        'username' => $data['username'],
        'email' => $data['email'],
        'phone' => $data['phone'] ?? '',
        'password' => $data['password']
    ]);

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>