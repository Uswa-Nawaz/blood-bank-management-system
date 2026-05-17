<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY GATE
 * Ensure the client is logged in and verified as a Donor.
 */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Donor') {
    header("Location: login.html");
    exit();
}

$current_user_id = $_SESSION['user_id']; 

/** * 2. DATA PREPARATION (Business Logic Layer)
 */

// A. Fetch User Profile Context and Blood Group String Representation
$user_query = "SELECT u.fullName, b.bloodGroup 
               FROM users u 
               LEFT JOIN bloodtypes b ON u.bloodTypeId = b.bloodTypeId 
               WHERE u.userId = $current_user_id";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// B. Metric 1: Count Total Completed Donations
$count_query = "SELECT COUNT(*) as total FROM donationrequests 
                WHERE donorId = $current_user_id AND status = 'Completed'";
$count_result = mysqli_query($conn, $count_query);
$total_donations = mysqli_fetch_assoc($count_result)['total'] ?? 0;

// C. Metric 2: Count Pending Requests (New addition from your HTML spec)
$pending_query = "SELECT COUNT(*) as total FROM donationrequests 
                  WHERE donorId = $current_user_id AND status = 'Pending'";
$pending_result = mysqli_query($conn, $pending_query);
$pending_requests = mysqli_fetch_assoc($pending_result)['total'] ?? 0;

// D. Table Data: Recent Donation Activity Snapshots (FIXED QUERY)
$history_sql = "SELECT d.requestedDate, d.status 
                FROM donationrequests d
                WHERE d.donorId = $current_user_id
                ORDER BY d.requestedDate DESC 
                LIMIT 5"; 
$history_result = mysqli_query($conn, $history_sql);

/**
 * 3. LOAD THE VIEW TEMPLATE
 */
include 'donor_dashboard_view.php';
?>