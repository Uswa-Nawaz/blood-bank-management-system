<?php
session_start();
include 'db_connect.php';

// 1. SECURITY GATE: Ensure user is authorized
$allowed_roles = ['Acceptor', 'Hospital', 'Individual', 'Donor']; // Added donor if they cross-use roles
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowed_roles)) {
    header("Location: login.html");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// 2. LOGIC: Fetch user profile metrics dynamically using true schema columns
$user_blood_group = "Not Set";

$profile_query = "SELECT bloodTypeId FROM users WHERE userId = $current_user_id LIMIT 1";
$profile_result = mysqli_query($conn, $profile_query);

if ($profile_result && $profile_row = mysqli_fetch_assoc($profile_result)) {
    $blood_id = $profile_row['bloodTypeId'];
    
    // Updated array map to perfectly align with your blood_types database table auto-increments
    $blood_map = [
        1 => "A+", 
        2 => "A-", 
        3 => "B+", 
        4 => "B-", 
        5 => "AB+", 
        6 => "AB-", 
        7 => "O+", 
        8 => "O-"
    ];
    $user_blood_group = $blood_map[$blood_id] ?? "Unknown";
}

// 3. LOGIC: Fetch Summary Counts
// Active (Pending) Requests
$active_query = "SELECT COUNT(*) as total FROM blood_requests 
                 WHERE acceptor_id = $current_user_id AND status = 'Pending'";
$active_result = mysqli_query($conn, $active_query);
$active_count = mysqli_fetch_assoc($active_result)['total'] ?? 0;

// Approved / Fulfilled Requests
$fulfilled_query = "SELECT COUNT(*) as total FROM blood_requests 
                    WHERE acceptor_id = $current_user_id AND status = 'Fulfilled'";
$fulfilled_result = mysqli_query($conn, $fulfilled_query);
$fulfilled_count = mysqli_fetch_assoc($fulfilled_result)['total'] ?? 0;

// Pending Payments (Corrected column matching to user_id to match invoices schema)
$payment_query = "SELECT SUM(total_amount) as total_unpaid 
                  FROM invoices 
                  WHERE user_id = $current_user_id AND payment_status = 'Unpaid'";
$payment_result = mysqli_query($conn, $payment_query);
$unpaid_amount = mysqli_fetch_assoc($payment_result)['total_unpaid'] ?? 0.00;

// 4. LOGIC: Fetch Recent Blood Request Records (Updated to include Patient and Hospital)
$history_sql = "SELECT request_id, patient_name, blood_type_id, units_requested, hospital_name, is_urgent, request_date, status 
                FROM blood_requests 
                WHERE acceptor_id = $current_user_id 
                ORDER BY request_date DESC 
                LIMIT 5";
$history_result = mysqli_query($conn, $history_sql);

// Load the presentation layout view safely
include 'acceptor_dashboard.view.php';
?>