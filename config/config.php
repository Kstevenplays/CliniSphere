<?php
// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SENDER_EMAIL', 'noreply@clinisphere.com');
define('SENDER_NAME', 'CliniSphere Clinic');

// Application Settings
define('APP_NAME', 'CliniSphere');
define('APP_URL', 'http://localhost/CliniSphere');
define('TIMEZONE', 'UTC');

// Session timeout (in minutes)
define('SESSION_TIMEOUT', 30);

// Appointment settings
define('APPOINTMENT_SLOT_DURATION', 30); // in minutes
define('ADVANCE_BOOKING_DAYS', 60); // days in advance
define('MIN_BOOKING_HOURS', 2); // minimum hours before appointment

// Pagination
define('ITEMS_PER_PAGE', 10);

date_default_timezone_set(TIMEZONE);

// Session management
session_start();
if (isset($_SESSION['user_id'])) {
    $last_activity = isset($_SESSION['last_activity']) ? $_SESSION['last_activity'] : time();
    if (time() - $last_activity > (SESSION_TIMEOUT * 60)) {
        session_destroy();
        header('Location: ' . APP_URL . '/login.php?expired=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}
?>
