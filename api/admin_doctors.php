<?php
require_once '../includes/Service.php';

header('Content-Type: application/json');
session_start();

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$doctorService = new DoctorService($conn);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $doctors = $doctorService->getAllDoctors();
    echo json_encode(['success' => true, 'data' => $doctors]);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Create user first
    $userService = new UserService($conn);
    $userId = $userService->registerUser(
        $data['email'],
        isset($data['password']) ? $data['password'] : 'temp' . rand(1000, 9999),
        $data['first_name'],
        $data['last_name'],
        $data['phone'] ?? '',
        'doctor'
    );
    
    if ($userId) {
        // Create doctor record
        $sql = "INSERT INTO doctors (user_id, specialization, bio) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $specialization = $data['specialization'] ?? '';
        $bio = $data['bio'] ?? '';
        $stmt->bind_param("iss", $userId, $specialization, $bio);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Doctor added successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to add doctor']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to create user account']);
    }
}
?>
