<?php
require_once '../includes/Service.php';

header('Content-Type: application/json');
session_start();

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $doctorId = $data['doctor_id'];
    $slotDate = $data['slot_date'];
    $slotTime = $data['slot_time'];
    
    $sql = "INSERT INTO time_slots (doctor_id, slot_date, slot_time, is_available) 
            VALUES (?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE is_available = 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $doctorId, $slotDate, $slotTime);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Time slot added successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add time slot']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $doctorId = $_GET['doctor_id'] ?? null;
    $date = $_GET['date'] ?? null;
    
    if ($doctorId && $date) {
        $sql = "SELECT id, slot_time, is_available FROM time_slots 
                WHERE doctor_id = ? AND slot_date = ? 
                ORDER BY slot_time";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $doctorId, $date);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $slots = $result->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $slots]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    }
}
?>
