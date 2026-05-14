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
 */
// Fetch all blood groups for the dropdown
$blood_types = mysqli_query($conn, "SELECT * FROM bloodtypes");

// Pending count for sidebar badge
$pending_count_query = "SELECT COUNT(*) as total FROM users WHERE isApproved = 0";
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, $pending_count_query))['total'];

// Current date for the 'min' attribute in the date picker
$today = date('Y-m-d');

/**
 * 3. LOAD THE VIEW
 */
include 'add_inventory_view.php';
?>