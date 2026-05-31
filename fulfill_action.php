<?php
session_start();
include 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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

            // Pull plan information along with the acceptor's ID
            $stmt_user = $conn->prepare("
                SELECT u.role, u.userId, p.plan_name, p.price_per_unit, p.unit_limit 
                FROM blood_requests r
                INNER JOIN users u ON r.acceptor_id = u.userId
                LEFT JOIN subscription_plans p ON u.plan_id = p.plan_id
                WHERE r.request_id = ?
            ");
            $stmt_user->bind_param("i", $req_id);
            $stmt_user->execute();
            $user_meta = $stmt_user->get_result()->fetch_assoc();

            if ($user_meta && $user_meta['role'] === 'Hospital') {
                $acceptor_id = intval($user_meta['userId']);
                $plan_limit = isset($user_meta['unit_limit']) ? intval($user_meta['unit_limit']) : null;
                $price_per_unit = floatval($user_meta['price_per_unit'] ?? 0);
                $plan_name = $user_meta['plan_name'] ?? 'None';
                
                // CRITICAL FIX: Exclude the current request_id from calculations so it isn't double-counted
                $stmt_usage = $conn->prepare("
                    SELECT SUM(r.units_requested) as units_used 
                    FROM blood_requests r 
                    WHERE r.acceptor_id = ? 
                    AND r.request_id != ?
                    AND r.status = 'Fulfilled' 
                    AND MONTH(r.request_date) = MONTH(CURRENT_DATE())
                    AND YEAR(r.request_date) = YEAR(CURRENT_DATE())
                ");
                $stmt_usage->bind_param("ii", $acceptor_id, $req_id);
                $stmt_usage->execute();
                $usage_data = $stmt_usage->get_result()->fetch_assoc();
                
                $current_usage = intval($usage_data['units_used'] ?? 0);

                // Evaluate if this request is entirely free, partially billable, or fully an overage
                if ($plan_name === 'Premium' || $plan_limit === null) {
                    $total_amount = 0.00; 
                } else if (($current_usage + $units_requested) <= $plan_limit) {
                    $total_amount = 0.00; 
                } else {
                    if ($current_usage >= $plan_limit) {
                        $billable_units = $units_requested;
                    } else {
                        $billable_units = ($current_usage + $units_requested) - $plan_limit;
                    }
                    $total_amount = $billable_units * $price_per_unit;
                }
                $processing_fee = 0.00; 
            } else {
                $fee_per_bag    = 500.00; 
                $processing_fee = $fee_per_bag * $units_requested;
                $total_amount   = $processing_fee;
            }

            // Insert the finalized row into invoices schema
            $stmt_inv = $conn->prepare("INSERT INTO invoices (request_id, total_amount, processing_fee, payment_status, invoice_type, invoice_date) VALUES (?, ?, ?, 'Unpaid', 'Request', CURDATE())");
            $stmt_inv->bind_param("idd", $req_id, $total_amount, $processing_fee);
            $stmt_inv->execute();

            $redirect_msg = "fulfilled";
        } else {
            // Request is only partially satisfied, keep it open!
            $stmt_update = $conn->prepare("UPDATE blood_requests SET status = 'Partially Fulfilled' WHERE request_id = ?");
            $stmt_update->bind_param("i", $req_id);
            $stmt_update->execute();

            $redirect_msg = "partial";
        }

        // Commit changes if everything went smoothly
        mysqli_commit($conn);
        
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