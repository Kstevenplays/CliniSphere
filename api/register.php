<?php
require_once '../includes/Service.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $email = $data['email'];
    $password = $data['password'];
    $firstName = $data['first_name'];
    $lastName = $data['last_name'];
    $phone = $data['phone'] ?? '';
    
    $userService = new UserService($conn);
    
    // Check if user already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Email already registered']);
    } else {
        $userId = $userService->registerUser($email, $password, $firstName, $lastName, $phone, 'patient');
        
        if ($userId) {
            session_start();
            $_SESSION['user_id'] = $userId;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'patient';
            $_SESSION['name'] = $firstName . ' ' . $lastName;
            
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Registration failed']);
        }
    }
}
?>
