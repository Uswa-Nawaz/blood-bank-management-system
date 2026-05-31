<?php
session_start();
include 'db_connect.php';

// 1. SECURITY & ID VALIDATION
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.html");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view_invoices.php");
    exit();
}

$invoice_id = intval($_GET['id']);

// 2. DATA RETRIEVAL (Supports both Request and Subscription types seamlessly)
$sql = "SELECT inv.*, 
               b.bloodGroup, 
               r.units_requested, 
               r.is_urgent,
               IFNULL(u_req.fullName, u_sub.fullName) AS fullName,
               IFNULL(u_req.phone, u_sub.phone) AS phone
        FROM invoices inv
        LEFT JOIN blood_requests r ON inv.request_id = r.request_id
        LEFT JOIN users u_req ON r.acceptor_id = u_req.userId
        LEFT JOIN users u_sub ON inv.user_id = u_sub.userId
        LEFT JOIN bloodtypes b ON r.blood_type_id = b.bloodTypeId
        WHERE inv.invoice_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$inv_data = $stmt->get_result()->fetch_assoc();

if (!$inv_data) {
    die("Invoice record not found.");
}

// 3. CALCULATION LOGIC
$processing_fee = isset($inv_data['processing_fee']) ? floatval($inv_data['processing_fee']) : 0;
$total_amount = floatval($inv_data['total_amount']);
$final_total = $total_amount;

// 4. LOAD THE VIEW
include 'print_invoice_view.php';
?>