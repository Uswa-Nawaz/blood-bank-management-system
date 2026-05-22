<?php
session_start();
include 'db_connect.php';

// 1. SECURITY FILTER: Must be logged in as a Donor
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Donor') {
    header("Location: login.html");
    exit();
}

// 2. CHECK PARAMETER IN URL
if (isset($_GET['id'])) {
    $donation_id = intval($_GET['id']);
    $current_user_id = $_SESSION['user_id'];

    // 3. SECURE DELETION QUERY
    // Enforces ownership and matching state conditions simultaneously
    $delete_sql = "DELETE FROM donationrequests 
                   WHERE donationId = ? 
                   AND donorId = ? 
                   AND status = 'Pending'";

    $stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($stmt, "ii", $donation_id, $current_user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect back to dashboard with a success flag
        header("Location: donor_dashboard.php?msg=cancelled");
        exit();
    } else {
        echo "Error cancelling request: " . mysqli_error($conn);
    }
} else {
    // If someone accesses this file directly without an ID, send them back
    header("Location: donor_dashboard.php");
    exit();
}
?>