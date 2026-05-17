<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized access.");
}

//line 41, 52 in pending_approvals_view.php hidden item here, using method="GET";
if (isset($_GET['id']) && isset($_GET['plan_id'])) {
    $id_to_approve = $_GET['id'];
    $plan_id = $_GET['plan_id'];

    // Updated Logic: Approve the user AND link them to their plan
    $sql = "UPDATE users SET isApproved = 1, plan_id = $plan_id WHERE userId = $id_to_approve";

    if (mysqli_query($conn, $sql)) {
        header("Location: pending_approvals.php?msg=UserApproved");
    } else {
        // Log error for developers and show a clean message
        error_log("Approval Error: " . mysqli_error($conn));
        die("An error occurred while updating the user status.");
    }
} else {
    // If someone tries to access this file without IDs, just send them back
    header("Location: pending_approvals.php");
    exit();
}
exit();