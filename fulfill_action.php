<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY GATE
 */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized access.");
}

if (isset($_GET['req_id']) && isset($_GET['bag_id'])) {
    $req_id = intval($_GET['req_id']);
    $bag_id = intval($_GET['bag_id']);

    /**
     * 2. ATOMIC TRANSACTION START
     * We use transactions so that either all updates happen, or none at all.
     */
    mysqli_begin_transaction($conn);

    try {
        // Query 1: Mark the specific Blood Bag as 'Reserved'
        $stmt1 = $conn->prepare("UPDATE blood_inventory SET status = 'Reserved' WHERE inventory_id = ?");
        $stmt1->bind_param("i", $bag_id);
        $stmt1->execute();

        // Query 2: Update the Blood Request status to 'Fulfilled'
        $stmt2 = $conn->prepare("UPDATE blood_requests SET status = 'Fulfilled' WHERE request_id = ?");
        $stmt2->bind_param("i", $req_id);
        $stmt2->execute();

        // Query 3: Create an Invoice record linked to this request
        // Note: You can set a default processing fee here if needed
        $stmt3 = $conn->prepare("INSERT INTO invoices (request_id, total_amount, payment_status) VALUES (?, 0.00, 'Paid')");
        $stmt3->bind_param("i", $req_id);
        $stmt3->execute();

        // If we reached here without errors, save all changes permanently
        mysqli_commit($conn);
        
        header("Location: manage_requests.php?msg=fulfilled");
        exit();

    } catch (Exception $e) {
        // If anything fails, undo all changes made during this block
        mysqli_rollback($conn);
        error_log("Fulfillment Error: " . $e->getMessage());
        die("System Error: Could not process fulfillment. Please try again.");
    }

} else {
    header("Location: manage_requests.php");
    exit();
}