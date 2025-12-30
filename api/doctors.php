<?php
require_once '../includes/Service.php';

header('Content-Type: application/json');

$doctorService = new DoctorService($conn);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        // Get specific doctor
        $doctorId = $_GET['id'];
        $doctor = $doctorService->getDoctorById($doctorId);
        echo json_encode(['success' => true, 'data' => $doctor]);
    } else if (isset($_GET['date']) && isset($_GET['doctor_id'])) {
        // Get available time slots for doctor on specific date
        $doctorId = $_GET['doctor_id'];
        $date = $_GET['date'];
        $slots = $doctorService->getAvailableTimeSlots($doctorId, $date);
        echo json_encode(['success' => true, 'data' => $slots]);
    } else {
        // Get all doctors
        $doctors = $doctorService->getAllDoctors();
        echo json_encode(['success' => true, 'data' => $doctors]);
    }
}
?>
