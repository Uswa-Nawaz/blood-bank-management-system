<?php
session_start();
include 'db_connect.php';

// 1. SECURITY GATE: Ensure user is authorized
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// 2. LOGIC: Fetch current user's profile details from the database (Email field removed)
$user_query = "SELECT userId, fullName, phone, role, bloodTypeId FROM users WHERE userId = $current_user_id LIMIT 1";
$user_result = mysqli_query($conn, $user_query);

if ($user_result && mysqli_num_rows($user_result) > 0) {
    $user_data = mysqli_fetch_assoc($user_result);
    
    // Convert bloodTypeId to a readable text format string
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
    $display_blood_group = $blood_map[$user_data['bloodTypeId']] ?? "Not Set";
} else {
    exit("User profile records not found.");
}

// 3. PRESENTATION LAYER: Load the visual design layout safely
include 'acceptor_profile.view.php';
?>