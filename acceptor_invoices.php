<?php
session_start();
include 'db_connect.php';

// 1. AUTHENTICATION GUARD: Verify user session state
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$current_user_id = intval($_SESSION['user_id']);

// 2. DATA LAYER QUERY: Fetch invoices with request data using INNER JOIN
// We link invoices to blood_requests, routing through to map the correct user session ID.
$query = "SELECT 
            i.invoice_id, 
            i.total_amount, 
            i.processing_fee, 
            i.payment_status, 
            i.invoice_date, 
            i.invoice_type,
            r.patient_name,
            r.units_requested
          FROM invoices i
          INNER JOIN blood_requests r ON i.request_id = r.request_id
          WHERE r.acceptor_id = ? 
          ORDER BY i.invoice_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

// 3. LOAD THE SEPARATE VIEW INTERFACE LAYER
include 'acceptor_invoices.view.php';
?>