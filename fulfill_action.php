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
     */
    mysqli_begin_transaction($conn);

    try {
        // Query 1: Mark the specific Blood Bag as 'Reserved'
        $stmt1 = $conn->prepare("UPDATE blood_inventory SET status = 'Reserved' WHERE inventory_id = ?");
        $stmt1->bind_param("i", $bag_id);
        $stmt1->execute();

        // Query 2: Log this assignment into your junction table
        $stmt_log = $conn->prepare("INSERT INTO fulfillment_transactions (request_id, inventory_id, dispatch_date) VALUES (?, ?, NOW())");
        $stmt_log->bind_param("ii", $req_id, $bag_id);
        $stmt_log->execute();

        // Query 3: Fetch total units requested and current status
        $stmt_req = $conn->prepare("SELECT units_requested FROM blood_requests WHERE request_id = ?");
        $stmt_req->bind_param("i", $req_id);
        $stmt_req->execute();
        $res_req = $stmt_req->get_result();
        $row_req = $res_req->fetch_assoc();
        
        $units_requested = ($row_req) ? intval($row_req['units_requested']) : 1;

        // Query 4: Count how many bags have been assigned to this request in total
        $stmt_count = $conn->prepare("SELECT COUNT(*) as assigned_count FROM fulfillment_transactions WHERE request_id = ?");
        $stmt_count->bind_param("i", $req_id);
        $stmt_count->execute();
        $res_count = $stmt_count->get_result();
        $row_count = $res_count->fetch_assoc();
        
        $assigned_count = intval($row_count['assigned_count']);

        // Query 5: Evaluate state constraints
        if ($assigned_count >= $units_requested) {
            // Request is completely satisfied!
            $stmt_update = $conn->prepare("UPDATE blood_requests SET status = 'Fulfilled' WHERE request_id = ?");
            $stmt_update->bind_param("i", $req_id);
            $stmt_update->execute();

            // Setup and issue the Invoice ONLY when fully completed
            $fee_per_bag = 500.00; 
            $processing_fee = $fee_per_bag * $units_requested;
            $total_amount = $processing_fee;

            $stmt_inv = $conn->prepare("INSERT INTO invoices (request_id, total_amount, processing_fee, payment_status, invoice_type) VALUES (?, ?, ?, 'Unpaid', 'Request')");
            $stmt_inv->bind_param("idd", $req_id, $total_amount, $processing_fee);
            $stmt_inv->execute();

            $redirect_msg = "fulfilled";
        } else {
            // Request is only partially satisfied, keep it open for more assignments!
            $stmt_update = $conn->prepare("UPDATE blood_requests SET status = 'Partially Fulfilled' WHERE request_id = ?");
            $stmt_update->bind_param("i", $req_id);
            $stmt_update->execute();

            $redirect_msg = "partial";
        }

        // Commit changes if everything went smoothly
        mysqli_commit($conn);
        
        // Redirect back to the processing panel view for that specific request to continue assigning remaining units
        header("Location: process_request.php?id=" . $req_id . "&msg=" . $redirect_msg);
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log("Fulfillment Error: " . $e->getMessage());
        die("System Error: Could not process fulfillment. Please try again.");
    }

} else {
    header("Location: manage_requests.php");
    exit();
}
?>