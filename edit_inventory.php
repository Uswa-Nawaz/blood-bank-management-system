<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY GATE
 */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.html");
    exit();
}

/**
 * 2. DATA RETRIEVAL (The "Brain")
 */
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Using a prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM blood_inventory WHERE inventory_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) { 
        exit("Record not found."); 
    }
} else {
    header("Location: manage_inventory.php");
    exit();
}

// Data for dropdowns
$blood_types = mysqli_query($conn, "SELECT * FROM bloodtypes");

// Sidebar badge
$pending_count_query = "SELECT COUNT(*) as total FROM users WHERE isApproved = 0";
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, $pending_count_query))['total'];

/**
 * 3. LOAD THE VIEW
 */
include 'edit_inventory_view.php';
?>