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
 * 2. DATA PREPARATION (Using Exact Database Schema Columns)
 */
$history_sql = "SELECT d.donationId, d.requestedDate, b.bloodGroup, d.status 
                FROM donationrequests d
                JOIN bloodtypes b ON d.bloodTypeId = b.bloodTypeId
                WHERE d.donorId = $current_user_id
                ORDER BY d.requestedDate DESC";
                
$history_result = mysqli_query($conn, $history_sql);

/**
 * 3. LOAD THE COMPANION VIEW TEMPLATE
 */
include 'donation_history_view.php';
?>