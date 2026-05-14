<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY GATE
 */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.html");
    exit();
}

/**
 * 2. DATA PREPARATION
 * Joining 'blood_requests' with 'users' (for names) and 'bloodtypes' (for groups)
 */
$sql = "SELECT r.*, u.fullName, u.phone, b.bloodGroup 
        FROM blood_requests r
        JOIN users u ON r.acceptor_id = u.userId
        JOIN bloodtypes b ON r.blood_type_id = b.bloodTypeId
        ORDER BY r.is_urgent DESC, r.request_date ASC";

$result = mysqli_query($conn, $sql); 

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}

// Sidebar logic: Count users waiting for approval
$pending_count_query = "SELECT COUNT(*) as total FROM users WHERE isApproved = 0";
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, $pending_count_query))['total'];

/**
 * 3. LOAD THE VIEW
 */
include 'manage_requests_view.php';
?>