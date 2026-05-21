<?php
session_start();
include 'db_connect.php';

// 1. SECURITY GATE
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.html");
    exit();
}

// 2. DATA PREPARATION (Business Logic)
// Fetch all pending users along with their chosen subscription plan name
$sql = "SELECT u.userId, u.fullName, u.phone, u.role, u.createdAt, u.plan_id, p.plan_name 
        FROM users u
        INNER JOIN subscription_plans p ON u.plan_id = p.plan_id 
        WHERE u.isApproved = 0";
$pending_users = mysqli_query($conn, $sql);

// Sidebar badge count
$pending_count_query = "SELECT COUNT(*) as total FROM users WHERE isApproved = 0";
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, $pending_count_query))['total'];

// 3. LOAD THE VIEW
include 'pending_approvals_view.php';
?>