<?php
session_start();
include 'db_connect.php';

// 1. SECURITY GATE
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized access.");
}

// 2. CHECK TARGET SELECTION
if (isset($_GET['id'])) {
    $donation_id = intval($_GET['id']);

    // 3. EXECUTE UPDATE (Prepared Statement)
    // Flips status to 'Rejected' and leaves quantity collection unfulfilled
    $sql = "UPDATE donationrequests SET status = 'Rejected' WHERE donationId = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $donation_id);

    if ($stmt->execute()) {
        // Redirect right back to dashboard list upon operational success
        header("Location: manage_donations.php?success=rejected");
    } else {
        error_log("Rejection Error: " . $stmt->error);
        die("A database error occurred. Please try again.");
    }

    $stmt->close();
} else {
    header("Location: manage_donations.php");
}
exit();
?>