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

        // Query 2.5: Get units_requested from blood_requests to calculate dynamic cost
        $stmt_units = $conn->prepare("SELECT units_requested FROM blood_requests WHERE request_id = ?");
        $stmt_units->bind_param("i", $req_id);
        $stmt_units->execute();
        $res_units = $stmt_units->get_result();
        $row_units = $res_units->fetch_assoc();
        
        $units = ($row_units) ? intval($row_units['units_requested']) : 1;
        
        // Setup billing calculations
        $fee_per_bag = 500.00; 
        $processing_fee = $fee_per_bag * $units;
        $total_amount = $processing_fee; // Adjust if adding additional flat base fees

        // Query 3: Create an Invoice record linked to this request with clean parameters
        $stmt3 = $conn->prepare("INSERT INTO invoices (request_id, total_amount, processing_fee, payment_status, invoice_type) VALUES (?, ?, ?, 'Unpaid', 'Request')");
        $stmt3->bind_param("idd", $req_id, $total_amount, $processing_fee);
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
?>