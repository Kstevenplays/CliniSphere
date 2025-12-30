<?php
require_once '../includes/Service.php';

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$appointmentService = new AppointmentService($conn);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $patientId = $_SESSION['user_id'];
    $appointments = $appointmentService->getPatientAppointments($patientId);
    echo json_encode(['success' => true, 'data' => $appointments]);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $patientId = $_SESSION['user_id'];
    $doctorId = $data['doctor_id'];
    $appointmentDate = $data['appointment_date'];
    $appointmentTime = $data['appointment_time'];
    $reason = $data['reason'] ?? '';
    
    $result = $appointmentService->createAppointment($patientId, $doctorId, $appointmentDate, $appointmentTime, $reason);
    
    if ($result['success']) {
        // Send confirmation email
        $appointment = $appointmentService->getAppointmentById($result['appointment_id']);
        $emailService = new EmailService($conn);
        
        $doctorName = $appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name'];
        $emailService->sendConfirmationEmail(
            $appointment['patient_email'],
            $appointment['patient_name'],
            $doctorName,
            $appointment['appointment_date'],
            $appointment['appointment_time'],
            $result['appointment_id']
        );
    }
    
    echo json_encode($result);
}
?>
