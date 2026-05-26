<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY & PARAMETER CHECK
 */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.html");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_requests.php");
    exit();
}

$req_id = intval($_GET['id']); // Secure the ID

/**
 * 2. DATA RETRIEVAL
 * Fetch request details along with Blood Group and Patient Name
 */
// Changed u.fullName to r.patient_name, and kept u.fullName as 'requestorName' if admin needs to know who submitted it
$req_query = "SELECT r.*, b.bloodGroup, r.patient_name, u.fullName as requestorName 
              FROM blood_requests r 
              JOIN bloodtypes b ON r.blood_type_id = b.bloodTypeId 
              JOIN users u ON r.acceptor_id = u.userId
              WHERE r.request_id = ?";
$stmt_req = $conn->prepare($req_query);
$stmt_req->bind_param("i", $req_id);
$stmt_req->execute();
$req_data = $stmt_req->get_result()->fetch_assoc();

if (!$req_data) { exit("Request not found."); }

/**
 * 3. INVENTORY MATCHING (FEFO Logic)
 * Find 'Available' bags of the same blood type, sorted by oldest expiry
 */
$target_group_id = $req_data['blood_type_id'];
$inventory_sql = "SELECT * FROM blood_inventory 
                  WHERE blood_type_id = ? 
                  AND status = 'Available'
                  ORDER BY expiry_date ASC"; 
$stmt_inv = $conn->prepare($inventory_sql);
$stmt_inv->bind_param("i", $target_group_id);
$stmt_inv->execute();
$inventory_res = $stmt_inv->get_result();

// Sidebar badge logic
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE isApproved = 0"))['total'];

/**
 * 4. LOAD THE VIEW
 */
include 'process_request_view.php';
?>