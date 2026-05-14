<?php
session_start();
include 'db_connect.php';

// 1. SECURITY GATE
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.html");
    exit();
}

// 2. DATA PREPARATION (Business Logic)
// Fetch plans for the dropdown
$plans_query = "SELECT * FROM subscription_plans";
$plans_result = mysqli_query($conn, $plans_query);
$plans_list = [];
while($p = mysqli_fetch_assoc($plans_result)) {
    $plans_list[] = $p;
}

// Fetch pending users
$sql = "SELECT userId, fullName, phone, role, createdAt FROM users WHERE isApproved = 0";
$pending_users = mysqli_query($conn, $sql);

// Sidebar badge count
$pending_count_query = "SELECT COUNT(*) as total FROM users WHERE isApproved = 0";
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, $pending_count_query))['total'];

// 3. LOAD THE VIEW
include 'pending_approvals_view.php';
?>

<!-- This file talks to the database and prepares variables for the display. -->