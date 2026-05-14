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
 * 2. DATA PREPARATION (Business Logic)
 */

// Count for sidebar badge (Pending Users)
$pending_count_query = "SELECT COUNT(*) as total FROM users WHERE isApproved = 0";
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, $pending_count_query))['total'];

// Logic for Category Cards (Only Approved Donors)
$donor_count_query = "SELECT COUNT(*) as total FROM users WHERE role = 'Donor' AND isApproved = 1";
$donor_count = mysqli_fetch_assoc(mysqli_query($conn, $donor_count_query))['total'];

// Logic for Category Cards (Only Approved Acceptors/Hospitals)
$acceptor_count_query = "SELECT COUNT(*) as total FROM users WHERE (role = 'Acceptor' OR role = 'Hospital' OR role = 'Individual') AND isApproved = 1";
$acceptor_count = mysqli_fetch_assoc(mysqli_query($conn, $acceptor_count_query))['total'];

// Logic for the Table (Fetch verified users and join blood types)
$users_sql = "SELECT u.userId, u.fullName, u.phone, u.role, b.bloodGroup 
              FROM users u 
              LEFT JOIN bloodtypes b ON u.bloodTypeId = b.bloodTypeId 
              WHERE u.isApproved = 1 AND u.role != 'Admin'
              ORDER BY u.fullName ASC";
$users_result = mysqli_query($conn, $users_sql);

/**
 * 3. LOAD THE VIEW
 */
include 'manage_users_view.php';
?>