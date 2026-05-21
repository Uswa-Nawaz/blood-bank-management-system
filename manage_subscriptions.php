<?php
session_start();
include 'db_connect.php';

// 1. SECURITY GATE
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.html");
    exit();
}

// 2. FETCH PLANS DATA FOR CARDS
$plans_sql = "SELECT * FROM subscription_plans WHERE plan_name != 'None'";
$plans_data = mysqli_query($conn, $plans_sql);

// 3. FETCH LICENSED HOSPITALS FOR THE TABLE
$hospitals_sql = "SELECT u.userId, u.fullName, u.phone, u.createdAt, p.plan_name, p.monthly_fee, p.unit_limit 
                  FROM users u 
                  INNER JOIN subscription_plans p ON u.plan_id = p.plan_id 
                  WHERE u.role = 'Hospital' AND u.isApproved = 1 
                  ORDER BY u.fullName ASC";
$subscribed_hospitals = mysqli_query($conn, $hospitals_sql);

// Sidebar badge count
$pending_count_query = "SELECT COUNT(*) as total FROM users WHERE isApproved = 0";
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, $pending_count_query))['total'];

// 4. LOAD THE VIEW
include 'manage_subscriptions_view.php';
?>