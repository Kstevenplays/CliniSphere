<?php
require_once 'config/database.php';
require_once 'config/config.php';

class EmailService {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Send email using PHP mail function
     * For production, use PHPMailer or similar library
     */
    public function send($to, $subject, $body, $type = 'confirmation', $appointmentId = null) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: " . SENDER_NAME . " <" . SENDER_EMAIL . ">" . "\r\n";
        
        $result = mail($to, $subject, $body, $headers);
        
        // Log email
        $log_sql = "INSERT INTO email_logs (appointment_id, recipient_email, email_type, status) 
                   VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($log_sql);
        $status = $result ? 'sent' : 'failed';
        $stmt->bind_param("isss", $appointmentId, $to, $type, $status);
        $stmt->execute();
        
        return $result;
    }
    
    public function sendConfirmationEmail($patientEmail, $patientName, $doctorName, $appointmentDate, $appointmentTime, $appointmentId) {
        $subject = "Appointment Confirmation - CliniSphere";
        $body = "
        <html>
        <head></head>
        <body style='font-family: Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                <h2 style='color: #2c3e50;'>Appointment Confirmation</h2>
                <p>Dear {$patientName},</p>
                <p>Thank you for scheduling an appointment with us. Your appointment details are below:</p>
                
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Doctor:</strong> {$doctorName}</p>
                    <p><strong>Date:</strong> " . date('F d, Y', strtotime($appointmentDate)) . "</p>
                    <p><strong>Time:</strong> {$appointmentTime}</p>
                    <p><strong>Appointment ID:</strong> #{$appointmentId}</p>
                </div>
                
                <p>Your appointment is pending admin approval. You will receive another email once it has been approved.</p>
                <p>If you need to cancel or reschedule, please contact us as soon as possible.</p>
                
                <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                <p style='font-size: 12px; color: #666;'>
                    This is an automated email. Please do not reply to this address.<br>
                    CliniSphere Clinic - Your Health, Our Priority
                </p>
            </div>
        </body>
        </html>";
        
        return $this->send($patientEmail, $subject, $body, 'confirmation', $appointmentId);
    }
    
    public function sendApprovalEmail($patientEmail, $patientName, $appointmentDate, $appointmentTime, $appointmentId) {
        $subject = "Appointment Approved - CliniSphere";
        $body = "
        <html>
        <head></head>
        <body style='font-family: Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                <h2 style='color: #27ae60;'>Appointment Approved ✓</h2>
                <p>Dear {$patientName},</p>
                <p>Good news! Your appointment has been approved and is confirmed.</p>
                
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Date:</strong> " . date('F d, Y', strtotime($appointmentDate)) . "</p>
                    <p><strong>Time:</strong> {$appointmentTime}</p>
                    <p><strong>Appointment ID:</strong> #{$appointmentId}</p>
                </div>
                
                <p>Please arrive 10 minutes before your scheduled appointment time.</p>
                
                <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                <p style='font-size: 12px; color: #666;'>
                    This is an automated email. Please do not reply to this address.<br>
                    CliniSphere Clinic - Your Health, Our Priority
                </p>
            </div>
        </body>
        </html>";
        
        return $this->send($patientEmail, $subject, $body, 'approval', $appointmentId);
    }
    
    public function sendRejectionEmail($patientEmail, $patientName, $reason = '', $appointmentId = null) {
        $subject = "Appointment Status Update - CliniSphere";
        $body = "
        <html>
        <head></head>
        <body style='font-family: Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                <h2 style='color: #e74c3c;'>Appointment Status</h2>
                <p>Dear {$patientName},</p>
                <p>Unfortunately, your appointment request could not be approved at this time.</p>
                
                " . ($reason ? "<div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Reason:</strong> {$reason}</p>
                </div>" : "") . "
                
                <p>Please feel free to schedule another appointment or contact us for more information.</p>
                
                <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                <p style='font-size: 12px; color: #666;'>
                    This is an automated email. Please do not reply to this address.<br>
                    CliniSphere Clinic - Your Health, Our Priority
                </p>
            </div>
        </body>
        </html>";
        
        return $this->send($patientEmail, $subject, $body, 'rejection', $appointmentId);
    }
    
    public function sendAppointmentReminder($patientEmail, $patientName, $appointmentDate, $appointmentTime, $doctorName) {
        $subject = "Appointment Reminder - CliniSphere";
        $body = "
        <html>
        <head></head>
        <body style='font-family: Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                <h2 style='color: #3498db;'>Appointment Reminder</h2>
                <p>Hi {$patientName},</p>
                <p>This is a reminder about your upcoming appointment with Dr. {$doctorName}:</p>
                
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Date:</strong> " . date('F d, Y', strtotime($appointmentDate)) . "</p>
                    <p><strong>Time:</strong> {$appointmentTime}</p>
                </div>
                
                <p>Please arrive 10 minutes early and bring any required documents.</p>
                
                <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                <p style='font-size: 12px; color: #666;'>
                    This is an automated email. Please do not reply to this address.<br>
                    CliniSphere Clinic - Your Health, Our Priority
                </p>
            </div>
        </body>
        </html>";
        
        return $this->send($patientEmail, $subject, $body, 'reminder');
    }
}

class UserService {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function registerUser($email, $password, $firstName, $lastName, $phone, $role = 'patient') {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO users (email, password, first_name, last_name, phone, role) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssss", $email, $hashedPassword, $firstName, $lastName, $phone, $role);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    public function authenticateUser($email, $password) {
        $sql = "SELECT id, email, password, role, first_name, last_name FROM users WHERE email = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            }
        }
        return false;
    }
    
    public function getUserById($userId) {
        $sql = "SELECT id, email, first_name, last_name, phone, role FROM users WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
}

class AppointmentService {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function createAppointment($patientId, $doctorId, $appointmentDate, $appointmentTime, $reason) {
        // Check if slot is available
        $check_sql = "SELECT id FROM time_slots 
                      WHERE doctor_id = ? AND slot_date = ? AND slot_time = ? AND is_available = 1";
        $stmt = $this->conn->prepare($check_sql);
        $stmt->bind_param("iss", $doctorId, $appointmentDate, $appointmentTime);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows === 0) {
            return ['success' => false, 'error' => 'Selected time slot is not available'];
        }
        
        // Create appointment
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason_for_visit) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisss", $patientId, $doctorId, $appointmentDate, $appointmentTime, $reason);
        
        if ($stmt->execute()) {
            $appointmentId = $this->conn->insert_id;
            
            // Mark slot as unavailable
            $update_slot = "UPDATE time_slots SET is_available = 0 WHERE doctor_id = ? AND slot_date = ? AND slot_time = ?";
            $stmt2 = $this->conn->prepare($update_slot);
            $stmt2->bind_param("iss", $doctorId, $appointmentDate, $appointmentTime);
            $stmt2->execute();
            
            return ['success' => true, 'appointment_id' => $appointmentId];
        }
        
        return ['success' => false, 'error' => 'Failed to create appointment'];
    }
    
    public function getAppointmentById($appointmentId) {
        $sql = "SELECT a.*, u.email as patient_email, u.first_name as patient_name, 
                       d.id as doctor_id, u2.first_name as doctor_first_name, u2.last_name as doctor_last_name
                FROM appointments a
                JOIN users u ON a.patient_id = u.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users u2 ON d.user_id = u2.id
                WHERE a.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $appointmentId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getPatientAppointments($patientId) {
        $sql = "SELECT a.*, d.id as doctor_id, u.first_name as doctor_first_name, u.last_name as doctor_last_name, 
                       doc.specialization
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users u ON d.user_id = u.id
                JOIN users uu ON a.patient_id = uu.id
                LEFT JOIN doctors doc ON d.id = doc.id
                WHERE a.patient_id = ?
                ORDER BY a.appointment_date DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getPendingAppointments() {
        $sql = "SELECT a.*, u.first_name as patient_first_name, u.last_name as patient_last_name, u.email as patient_email,
                       u2.first_name as doctor_first_name, u2.last_name as doctor_last_name
                FROM appointments a
                JOIN users u ON a.patient_id = u.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users u2 ON d.user_id = u2.id
                WHERE a.status = 'pending'
                ORDER BY a.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function approveAppointment($appointmentId, $notes = '') {
        $sql = "UPDATE appointments SET status = 'approved', notes = ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $notes, $appointmentId);
        
        return $stmt->execute();
    }
    
    public function rejectAppointment($appointmentId, $notes = '') {
        $sql = "UPDATE appointments SET status = 'rejected', notes = ? WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $notes, $appointmentId);
        
        if ($stmt->execute()) {
            // Make time slot available again
            $appointment = $this->getAppointmentById($appointmentId);
            $restore_sql = "UPDATE time_slots SET is_available = 1 
                           WHERE doctor_id = ? AND slot_date = ? AND slot_time = ?";
            $stmt2 = $this->conn->prepare($restore_sql);
            $stmt2->bind_param("iss", $appointment['doctor_id'], $appointment['appointment_date'], $appointment['appointment_time']);
            $stmt2->execute();
            
            return true;
        }
        
        return false;
    }
}

class DoctorService {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function getAllDoctors() {
        $sql = "SELECT d.id, u.first_name, u.last_name, u.email, d.specialization, d.bio, d.profile_image, d.availability_status
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                WHERE d.availability_status = 1
                ORDER BY u.first_name";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getDoctorById($doctorId) {
        $sql = "SELECT d.id, d.user_id, u.first_name, u.last_name, u.email, u.phone, d.specialization, d.bio, d.profile_image
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                WHERE d.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getAvailableTimeSlots($doctorId, $date) {
        $sql = "SELECT slot_time FROM time_slots 
                WHERE doctor_id = ? AND slot_date = ? AND is_available = 1
                ORDER BY slot_time";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $doctorId, $date);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $slots = [];
        
        while ($row = $result->fetch_assoc()) {
            $slots[] = $row['slot_time'];
        }
        
        return $slots;
    }
}
?>
