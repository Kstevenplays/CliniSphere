<?php
// Initialize Database - Run this file once to create all tables
require_once __DIR__ . '/config/database.php';

echo "✓ Database initialization complete!\n";
echo "✓ All tables have been created successfully.\n";
echo "\nDatabase: " . DB_NAME . "\n";
echo "Tables created:\n";
echo "  - users\n";
echo "  - doctors\n";
echo "  - time_slots\n";
echo "  - appointments\n";
echo "  - email_logs\n";
echo "  - audit_log\n";

$conn->close();
?>
