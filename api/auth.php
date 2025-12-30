<?php
require_once __DIR__ . '/../includes/Service.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
            exit;
        }
        
        // Accept either username or email
        $username = $data['username'] ?? $data['email'] ?? null;
        $password = $data['password'] ?? null;
        
        if (!$username || !$password) {
            echo json_encode(['success' => false, 'error' => 'Missing username/email or password']);
            exit;
        }
        
        $userService = new UserService($conn);
        $user = $userService->authenticateUser($username, $password);
        
        if ($user) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['last_activity'] = time();
            
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid username/email or password']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    session_start();
    if (isset($_SESSION['user_id'])) {
        echo json_encode(['success' => true, 'user' => [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'],
            'name' => $_SESSION['name']
        ]]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Not logged in']);
    }
}
?>
