<?php
require_once '../includes/Service.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $email = $data['email'];
    $password = $data['password'];
    
    $userService = new UserService($conn);
    $user = $userService->authenticateUser($email, $password);
    
    if ($user) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
        
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
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
