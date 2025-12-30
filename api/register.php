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
        
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $firstName = $data['first_name'] ?? null;
        $lastName = $data['last_name'] ?? null;
        $phone = $data['phone'] ?? '';
        
        // Validate required fields
        if (!$email || !$password || !$firstName || !$lastName) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        
        $userService = new UserService($conn);
        
        // Check if user already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $conn->error]);
            exit;
        }
        
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
                echo json_encode(['success' => false, 'error' => 'Registration failed: ' . $conn->error]);
            }
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
    }
}
?>
