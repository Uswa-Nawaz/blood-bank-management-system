<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY GATE
 */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Donor') {
    header("Location: login.html");
    exit();
}

$current_user_id = $_SESSION['user_id'];

/**
 * 2. BUSINESS LOGIC (Fetch Single User Profile Context)
 * We join with bloodtypes to get the readable string (e.g., 'A+')
 */
$profile_query = "SELECT u.fullName, u.phone, b.bloodGroup 
                  FROM users u 
                  LEFT JOIN bloodtypes b ON u.bloodTypeId = b.bloodTypeId 
                  WHERE u.userId = $current_user_id";

$profile_result = mysqli_query($conn, $profile_query);
$donor = mysqli_fetch_assoc($profile_result);

if (!$donor) {
    die("Error: Profile data could not be retrieved.");
}

/**
 * 3. LOAD THE VIEW
 */
include 'donor_profile_view.php';
?>