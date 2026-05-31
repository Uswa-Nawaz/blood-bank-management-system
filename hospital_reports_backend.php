<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connect to your database
$conn = mysqli_connect("localhost", "root", "", "bloodsystem");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get the active hospital's ID from session
$hospital_id = $_SESSION['user_id'] ?? 22; // Hardcoded fallback to Mayo Hospital (ID 22) for testing

// 1. Fetch official hospital name from users table
$name_query = "SELECT fullName FROM users WHERE userId = ? LIMIT 1"; 
$stmt = mysqli_prepare($conn, $name_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$name_result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($name_result);
$hospital_display_name = $user_data['fullName'] ?? "Hospital Partner";

// 1. Initialize our clean master structure for the 8 blood types
$report_data = [
    1 => ['name' => 'A+',  'requested' => 0, 'used' => 0],
    2 => ['name' => 'A-',  'requested' => 0, 'used' => 0],
    3 => ['name' => 'B+',  'requested' => 0, 'used' => 0],
    4 => ['name' => 'B-',  'requested' => 0, 'used' => 0],
    5 => ['name' => 'AB+', 'requested' => 0, 'used' => 0],
    6 => ['name' => 'AB-', 'requested' => 0, 'used' => 0],
    7 => ['name' => 'O+',  'requested' => 0, 'used' => 0],
    8 => ['name' => 'O-',  'requested' => 0, 'used' => 0]
];

// Initialize Top Summary Dashboard Counters
$total_requested   = 0;
$total_used        = 0;
$pending_patients  = 0;

// --- QUERY 1: Calculate ALL Bags Requested (Total entries submitted by this hospital) ---
$req_query = "SELECT blood_type_id, SUM(units_requested) as total_bags 
              FROM blood_requests 
              WHERE acceptor_id = ? 
              GROUP BY blood_type_id";
$stmt = mysqli_prepare($conn, $req_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$req_result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($req_result)) {
    $tid = (int)$row['blood_type_id'];
    if (isset($report_data[$tid])) {
        $report_data[$tid]['requested'] = (int)$row['total_bags'];
        $total_requested += (int)$row['total_bags'];
    }
}

// --- QUERY 2: Calculate USED Bags (Only 'Fulfilled' entries) ---
$used_query = "SELECT blood_type_id, SUM(units_requested) as total_bags 
               FROM blood_requests 
               WHERE acceptor_id = ? AND status = 'Fulfilled' 
               GROUP BY blood_type_id";
$stmt2 = mysqli_prepare($conn, $used_query);
mysqli_stmt_bind_param($stmt2, "i", $hospital_id);
mysqli_stmt_execute($stmt2);
$used_result = mysqli_stmt_get_result($stmt2);

while ($row = mysqli_fetch_assoc($used_result)) {
    $tid = (int)$row['blood_type_id'];
    if (isset($report_data[$tid])) {
        $report_data[$tid]['used'] = (int)$row['total_bags'];
        $total_used += (int)$row['total_bags'];
    }
}

// --- QUERY 3: Calculate Pending Patients Count ---
$patient_query = "SELECT COUNT(DISTINCT patient_name) as pending_count 
                  FROM blood_requests 
                  WHERE acceptor_id = ? AND status = 'Pending'";
$stmt3 = mysqli_prepare($conn, $patient_query);
mysqli_stmt_bind_param($stmt3, "i", $hospital_id);
mysqli_stmt_execute($stmt3);
$p_result = mysqli_stmt_get_result($stmt3);
if ($p_row = mysqli_fetch_assoc($p_result)) {
    $pending_patients = (int)$p_row['pending_count'];
}
?>