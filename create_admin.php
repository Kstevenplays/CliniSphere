<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Service.php';

// Admin credentials using username
$username = 'admin';
$email = 'admin'; // Using username as email for authentication
$password = 'admin123';
$firstName = 'Admin';
$lastName = 'User';
$role = 'admin';

// Check if admin already exists
$check_sql = "SELECT id FROM users WHERE email = ? AND role = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ss", $email, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Admin account already exists']);
    exit;
}

// Create admin user
$userService = new UserService($conn);
$userId = $userService->registerUser($email, $password, $firstName, $lastName, '', $role);

if ($userId) {
    echo json_encode([
        'success' => true, 
        'message' => 'Admin account created successfully!',
        'details' => [
            'Username: ' => $username,
            'Password: ' => $password,
            'Access URL: ' => 'http://localhost/CliniSphere/admin/login.php'
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create admin account', 'error' => $conn->error]);
}

$conn->close();
?>
