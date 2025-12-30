<?php
// Test database connection and table structure
require_once __DIR__ . '/config/database.php';

echo "<h1>CliniSphere Database Connection Test</h1>";

// Check connection
if ($conn->connect_error) {
    echo "<p style='color: red;'>Connection Error: " . $conn->connect_error . "</p>";
    exit;
} else {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
}

// Check if database exists
$db_check = $conn->select_db(DB_NAME);
if ($db_check) {
    echo "<p style='color: green;'>✓ Database '" . DB_NAME . "' selected</p>";
} else {
    echo "<p style='color: red;'>✗ Failed to select database</p>";
    exit;
}

// Check tables
$tables = ['users', 'doctors', 'time_slots', 'appointments', 'email_logs', 'audit_log'];
echo "<h2>Table Status:</h2>";
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Table '$table' exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Table '$table' missing</p>";
    }
}

// Test UserService
echo "<h2>Testing UserService:</h2>";
require_once __DIR__ . '/includes/Service.php';

try {
    $userService = new UserService($conn);
    echo "<p style='color: green;'>✓ UserService loaded successfully</p>";
    
    // Count existing users
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    echo "<p>Current users in database: " . $row['count'] . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ UserService Error: " . $e->getMessage() . "</p>";
}

$conn->close();
?>
