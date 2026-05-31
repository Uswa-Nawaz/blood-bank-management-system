<?php
// Database Configuration Constant, define
// Using 'define' not variables, so it is not accidently changed in any other file 
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'bloodbank_app');
define('DB_PASSWORD', 'StrongPass123!');
define('DB_NAME', 'bloodsystem');

// this variable is used on top of every file
//$conn variable is used in every sql query
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Error Handling Logic, ifmysqli_connect() fails
if (!$conn)
{

    error_log("Database connection failed: " . mysqli_connect_error());
    die("CRITICAL ERROR: Unable to connect to the blood bank database. Please contact the administrator.");
}


//This ensures that special characters (like symbols or non-English names) 
// are stored and retrieved correctly without turning into weird symbols.
mysqli_set_charset($conn, "utf8mb4");

//No closing tag needed for PHP-only files, it prevents from sending accidental 
//sending whitespace or blank line to the browser

