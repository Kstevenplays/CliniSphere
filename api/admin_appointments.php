<?php
require_once '../includes/Service.php';

header('Content-Type: application/json');
session_start();

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$appointmentService = new AppointmentService($conn);
$emailService = new EmailService($conn);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $appointments = $appointmentService->getPendingAppointments();
    echo json_encode(['success' => true, 'data' => $appointments]);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $appointmentId = $data['appointment_id'];
    $action = $data['action']; // 'approve' or 'reject'
    $notes = $data['notes'] ?? '';
    
    $appointment = $appointmentService->getAppointmentById($appointmentId);
    
    if ($action === 'approve') {
        if ($appointmentService->approveAppointment($appointmentId, $notes)) {
            // Send approval email
            $emailService->sendApprovalEmail(
                $appointment['patient_email'],
                $appointment['patient_name'],
                $appointment['appointment_date'],
                $appointment['appointment_time'],
                $appointmentId
            );
            
            // Log audit
            $adminId = $_SESSION['user_id'];
            $audit_sql = "INSERT INTO audit_log (admin_id, appointment_id, action, action_details) 
                         VALUES (?, ?, 'approved', ?)";
            $stmt = $conn->prepare($audit_sql);
            $stmt->bind_param("iis", $adminId, $appointmentId, $notes);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Appointment approved']);
        }
    } else if ($action === 'reject') {
        if ($appointmentService->rejectAppointment($appointmentId, $notes)) {
            // Send rejection email
            $emailService->sendRejectionEmail(
                $appointment['patient_email'],
                $appointment['patient_name'],
                $notes,
                $appointmentId
            );
            
            // Log audit
            $adminId = $_SESSION['user_id'];
            $audit_sql = "INSERT INTO audit_log (admin_id, appointment_id, action, action_details) 
                         VALUES (?, ?, 'rejected', ?)";
            $stmt = $conn->prepare($audit_sql);
            $stmt->bind_param("iis", $adminId, $appointmentId, $notes);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Appointment rejected']);
        }
    }
}
?>
