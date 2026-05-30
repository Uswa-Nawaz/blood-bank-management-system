<?php

// Temporary debug line: Remove or comment this out before submitting your project!
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Start session tracking to identify the logged-in user
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include your reliable database connection file
require_once 'db_connect.php';

// Authentication Guard: Ensure the user is logged in and is actually a Hospital account
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Hospital') {
    header("Location: login.html"); 
    exit();
}

$hospital_id = $_SESSION['user_id'];

// --- 1. METRICS CALCULATIONS ---

// Count total bags currently in this hospital's local possession based on approved request totals
$bags_query = "SELECT SUM(units_requested) AS total_bags 
               FROM blood_requests 
               WHERE acceptor_id = ? AND status IN ('Approved', 'Fulfilled')";
$stmt = mysqli_prepare($conn, $bags_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$bags_result = mysqli_stmt_get_result($stmt);
$total_bags = mysqli_fetch_assoc($bags_result)['total_bags'] ?? 0;

// Fetch the official, registered hospital name directly from the user profile records
$name_query = "SELECT * FROM users WHERE userId = ? LIMIT 1"; 
$stmt = mysqli_prepare($conn, $name_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$name_result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($name_result);

// Automatically detect your column name so you don't get empty values
if (isset($user_data['username'])) {
    $hospital_display_name = $user_data['username'];
} elseif (isset($user_data['fullName'])) {
    $hospital_display_name = $user_data['fullName'];
} elseif (isset($user_data['name'])) {
    $hospital_display_name = $user_data['name'];
} else {
    // Fallback if none of the typical columns are found
    $hospital_display_name = "Hospital Facility"; 
}

// Calculate active cases based on pending patient entries
$patients_query = "SELECT COUNT(DISTINCT patient_name) AS active_patients 
                   FROM blood_requests 
                   WHERE acceptor_id = ? AND status IN ('Pending', 'Partially Fulfilled')";
$stmt = mysqli_prepare($conn, $patients_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$patients_result = mysqli_stmt_get_result($stmt);
$active_patients = mysqli_fetch_assoc($patients_result)['active_patients'] ?? 0;

// Count pending requests waiting on central bank approval
$pending_query = "SELECT COUNT(*) AS pending_count 
                  FROM blood_requests 
                  WHERE acceptor_id = ? AND status = 'Pending'";
$stmt = mysqli_prepare($conn, $pending_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$pending_result = mysqli_stmt_get_result($stmt);
$pending_requests = mysqli_fetch_assoc($pending_result)['pending_count'] ?? 0;

// Count emergency cases marked high priority using your true boolean column: is_urgent
$emergency_query = "SELECT COUNT(*) AS emergency_count 
                    FROM blood_requests 
                    WHERE acceptor_id = ? AND status = 'Pending' AND is_urgent = 1";
$stmt = mysqli_prepare($conn, $emergency_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$emergency_result = mysqli_stmt_get_result($stmt);
$emergency_cases = mysqli_fetch_assoc($emergency_result)['emergency_count'] ?? 0;


// --- 2. DYNAMIC STOCK LEVEL MATRIX ---
// Mapping your blood_type_id integers to their layout presentation properties
$stock_levels = [
    1 => ['name' => 'A+',  'count' => 0, 'pct' => 0, 'class' => 'fill-low', 'badge' => 'badge-low', 'txt' => 'Critical'],
    2 => ['name' => 'A-',  'count' => 0, 'pct' => 0, 'class' => 'fill-low', 'badge' => 'badge-low', 'txt' => 'Critical'],
    3 => ['name' => 'B+',  'count' => 0, 'pct' => 0, 'class' => 'fill-low', 'badge' => 'badge-low', 'txt' => 'Critical'],
    4 => ['name' => 'B-',  'count' => 0, 'pct' => 0, 'class' => 'fill-low', 'badge' => 'badge-low', 'txt' => 'Critical'],
    5 => ['name' => 'O+',  'count' => 0, 'pct' => 0, 'class' => 'fill-low', 'badge' => 'badge-low', 'txt' => 'Critical'],
    6 => ['name' => 'O-',  'count' => 0, 'pct' => 0, 'class' => 'fill-low', 'badge' => 'badge-low', 'txt' => 'Critical'],
    7 => ['name' => 'AB+', 'count' => 0, 'pct' => 0, 'class' => 'fill-low', 'badge' => 'badge-low', 'txt' => 'Critical'],
    8 => ['name' => 'AB-', 'count' => 0, 'pct' => 0, 'class' => 'fill-low', 'badge' => 'badge-low', 'txt' => 'Critical']
];

// Query metrics grouped directly by blood_type_id
$stock_query = "SELECT blood_type_id, SUM(units_requested) AS group_count 
                FROM blood_requests 
                WHERE acceptor_id = ? AND status IN ('Approved', 'Fulfilled')
                GROUP BY blood_type_id";
$stmt = mysqli_prepare($conn, $stock_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$stock_res = mysqli_stmt_get_result($stmt);

while($row = mysqli_fetch_assoc($stock_res)) {
    $tid = (int)$row['blood_type_id'];
    if(isset($stock_levels[$tid])) {
        $cnt = (int)$row['group_count'];
        $stock_levels[$tid]['count'] = $cnt;
        
        // Dynamically compute progress bar widths based on available units
        if($cnt >= 15) {
            $stock_levels[$tid]['pct'] = 100;
            $stock_levels[$tid]['class'] = 'fill-good';
            $stock_levels[$tid]['badge'] = 'badge-stable';
            $stock_levels[$tid]['txt'] = 'Adequate';
        } elseif($cnt >= 5) {
            $stock_levels[$tid]['pct'] = 60;
            $stock_levels[$tid]['class'] = 'fill-moderate';
            $stock_levels[$tid]['badge'] = 'badge-moderate';
            $stock_levels[$tid]['txt'] = 'Moderate';
        } else {
            $stock_levels[$tid]['pct'] = ($cnt > 0) ? 25 : 3; 
            $stock_levels[$tid]['class'] = 'fill-low';
            $stock_levels[$tid]['badge'] = 'badge-critical';
            $stock_levels[$tid]['txt'] = 'Critical';
        }
    }
}

// Compile layout notifications dynamically
$critical_warnings = [];
foreach($stock_levels as $id => $data) {
    if($data['txt'] === 'Critical') {
        $critical_warnings[] = "<strong>{$data['name']}</strong> blood group has dropped below minimum threshold ({$data['count']} bags remaining).";
    }
}

// --- 3. RECENT ACTIVITY LOGS ---
$recent_query = "SELECT request_id, patient_name, blood_type_id, units_requested, status 
                 FROM blood_requests 
                 WHERE acceptor_id = ? 
                 ORDER BY request_id DESC LIMIT 4";
$stmt = mysqli_prepare($conn, $recent_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$recent_result = mysqli_stmt_get_result($stmt);