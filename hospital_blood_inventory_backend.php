<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connect.php';

// Authentication Guard
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Hospital') {
    header("Location: login.html"); 
    exit();
}

$hospital_id = $_SESSION['user_id'];

// 1. Fetch official hospital name from users table
$name_query = "SELECT fullName FROM users WHERE userId = ? LIMIT 1"; 
$stmt = mysqli_prepare($conn, $name_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$name_result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($name_result);
$hospital_display_name = $user_data['fullName'] ?? "Hospital Partner";

// 2. Initialize our default inventory structures utilizing your bloodTypeId tracking keys
// We hardcode the standard required baselines directly into the array map
$inventory = [
    1 => ['name' => 'A+',  'long' => 'A Positive',  'count' => 0, 'min' => 20],
    2 => ['name' => 'A-',  'long' => 'A Negative',  'count' => 0, 'min' => 10],
    3 => ['name' => 'B+',  'long' => 'B Positive',  'count' => 0, 'min' => 20],
    4 => ['name' => 'B-',  'long' => 'B Negative',  'count' => 0, 'min' => 10],
    5 => ['name' => 'AB+', 'long' => 'AB Positive', 'count' => 0, 'min' => 10],
    6 => ['name' => 'AB-', 'long' => 'AB Negative', 'count' => 0, 'min' => 5],
    7 => ['name' => 'O+',  'long' => 'O Positive',  'count' => 0, 'min' => 30],
    8 => ['name' => 'O-',  'long' => 'O Negative',  'count' => 0, 'min' => 15]
];

// 3. Fetch aggregated bags sum from blood_requests table
$stock_query = "SELECT blood_type_id, SUM(units_requested) AS total_units 
                FROM blood_requests 
                WHERE acceptor_id = ? AND status IN ('Approved', 'Fulfilled')
                GROUP BY blood_type_id";
$stmt = mysqli_prepare($conn, $stock_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$stock_res = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($stock_res)) {
    $type_id = (int)$row['blood_type_id'];
    if (isset($inventory[$type_id])) {
        $inventory[$type_id]['count'] = (int)$row['total_units'];
    }
}

// 4. Calculate dashboard summary card values metrics dynamically
$total_combined_bags = 0;
$low_stock_groups_count = 0;
$critical_messages = [];

foreach ($inventory as $id => $data) {
    $total_combined_bags += $data['count'];
    if ($data['count'] < $data['min']) {
        $low_stock_groups_count++;
        if ($data['count'] <= ($data['min'] * 0.3)) {
            $critical_messages[] = $data['name'];
        }
    }
}

// 5. Fetch total emergency counts for the topbar layout indicator badge
$emergency_query = "SELECT COUNT(*) AS emergency_count 
                    FROM blood_requests 
                    WHERE acceptor_id = ? AND status = 'Pending' AND is_urgent = 1";
$stmt = mysqli_prepare($conn, $emergency_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$emergency_result = mysqli_stmt_get_result($stmt);
$emergency_cases = mysqli_fetch_assoc($emergency_result)['emergency_count'] ?? 0;