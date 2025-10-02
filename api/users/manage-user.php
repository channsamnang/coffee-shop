<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

// Require admin access
$auth->requireAdmin();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Get all users
        $query = "SELECT id, full_name, username, email, phone, role, status, created_at 
                 FROM users ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'users' => $stmt->fetchAll()
        ]);
        break;

    case 'PUT':
        // Update user status or role
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['user_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID required']);
            exit();
        }

        $updates = [];
        $params = [];

        if (isset($data['status'])) {
            if (!in_array($data['status'], ['active', 'blocked'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                exit();
            }
            $updates[] = "status = ?";
            $params[] = $data['status'];
        }

        if (isset($data['role'])) {
            if (!in_array($data['role'], ['user', 'admin'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid role']);
                exit();
            }
            $updates[] = "role = ?";
            $params[] = $data['role'];
        }

        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No fields to update']);
            exit();
        }

        $params[] = $data['user_id'];
        $query = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = $db->prepare($query);
        
        try {
            $stmt->execute($params);
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update user']);
        }
        break;

    case 'DELETE':
        // Delete user (soft delete by setting status to 'deleted')
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['user_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID required']);
            exit();
        }

        $query = "UPDATE users SET status = 'deleted' WHERE id = ?";
        $stmt = $db->prepare($query);
        
        try {
            $stmt->execute([$data['user_id']]);
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>