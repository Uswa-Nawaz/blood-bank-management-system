<?php
session_start();
include 'db_connect.php';

// 1. SECURITY GATE
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized access.");
}

// 2. CHECK TARGET PARAMETER
if (isset($_GET['id'])) {
    $donation_id = intval($_GET['id']);

    // 3. TRANSITION STATUS TO 'APPROVED'
    $sql = "UPDATE donationrequests SET status = 'Approved' WHERE donationId = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $donation_id);

    if ($stmt->execute()) {
        // Bounce smoothly back to dashboard list layout
        header("Location: manage_donations.php?success=slot_approved");
    } else {
        error_log("Approval Error: " . $stmt->error);
        die("A database error occurred during confirmation. Please try again.");
    }

    $stmt->close();
} else {
    header("Location: manage_donations.php");
}
exit();
?>