<?php
session_start();
include 'db_connect.php'; 

// SECURITY GATE
//Is there no role saved in the session locker at all?
//Is the logged-in user's role NOT equal to 'Admin'?
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin')
{
    header("Location: login.html");
    exit();
}

// DATA PREPARATION
$inventory_query = "SELECT SUM(quantity_ml) as total_ml FROM blood_inventory";
$inventory_result = mysqli_query($conn, $inventory_query);
$total_inventory = mysqli_fetch_assoc($inventory_result)['total_ml'] ?? 0;

$pending_query = "SELECT COUNT(*) as total FROM users WHERE isApproved = 0";
$pending_result = mysqli_query($conn, $pending_query);
$pending_count = mysqli_fetch_assoc($pending_result)['total'];

$urgent_query = "SELECT COUNT(*) as total FROM blood_requests WHERE is_urgent = 1 AND status = 'Pending'";
$urgent_result = mysqli_query($conn, $urgent_query);
$urgent_count = mysqli_fetch_assoc($urgent_result)['total'] ?? 0;

$sql = "SELECT i.inventory_id, i.quantity_ml, i.expiry_date, i.status, t.bloodGroup 
        FROM blood_inventory i 
        JOIN bloodtypes t 
        ON i.blood_type_id = t.bloodTypeId
        ORDER BY i.inventory_id ASC";  //so the table is orderby the inventory id
$inventory_list = mysqli_query($conn, $sql);

// 3. LOAD THE VIEW
// This is where separate HTML file added
include 'admin_view.php'; 
?>