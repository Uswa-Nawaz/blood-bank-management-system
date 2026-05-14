<?php
// Database Configuration Constants
// Using 'define' makes these values global and immutable during the script execution.
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'bloodsystem');

/**
 * Attempt to connect to the MySQL database.
 * We use mysqli_connect which is the standard procedural way to interact with MySQL in PHP.
 */

// this variable is used on top of every file
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Error Handling Logic
if (!$conn) {
    /**
     * If connection fails, we log the error.
     * In a production environment, you wouldn't show mysqli_connect_error() to users
     * for security reasons, but for a semester project/viva, it's vital for debugging.
     */
    error_log("Database connection failed: " . mysqli_connect_error());
    die("CRITICAL ERROR: Unable to connect to the blood bank database. Please contact the administrator.");
}

/**
 * Set Charset to UTF-8.
 * This ensures that special characters (like symbols or non-English names) 
 * are stored and retrieved correctly without turning into weird symbols.
 */
mysqli_set_charset($conn, "utf8mb4");

// No closing tag needed for PHP-only files