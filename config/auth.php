<?php
class Auth {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit();
        }
        return true;
    }

    public function login($email, $password) {
        $query = "SELECT id, email, password, role FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        
        if ($user = $stmt->fetch()) {
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                return [
                    'success' => true,
                    'user' => $user
                ];
            }
        }
        
        return ['success' => false];
    }

    public function register($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                (full_name, username, email, phone, password, role) 
                VALUES (?, ?, ?, ?, ?, 'user')";
        
        $stmt = $this->conn->prepare($query);
        
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        try {
            $stmt->execute([
                $data['full_name'],
                $data['username'],
                $data['email'],
                $data['phone'],
                $password_hash
            ]);
            
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false];
        }
    }

    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch() ? true : false;
    }

    public function usernameExists($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$username]);
        return $stmt->fetch() ? true : false;
    }

    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        session_start();
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Please log in to continue']);
            exit();
        }
    }

    public function requireAdmin() {
        if (!$this->isAdmin()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Admin access required']);
            exit();
        }
    }
}
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