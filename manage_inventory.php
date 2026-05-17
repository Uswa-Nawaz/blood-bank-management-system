<?php
session_start();
include 'db_connect.php';

// 1. SECURITY GATE
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.html");
    exit();
}

// 2. SUMMARY LOGIC (Aggregates)
$total_sql = "SELECT SUM(quantity_ml) as total FROM blood_inventory";
$total_units = mysqli_fetch_assoc(mysqli_query($conn, $total_sql))['total'] ?? 0;

$reserved_sql = "SELECT COUNT(*) as total FROM blood_inventory WHERE status = 'Reserved'";
$reserved_count = mysqli_fetch_assoc(mysqli_query($conn, $reserved_sql))['total'];

$avail_sql = "SELECT COUNT(*) as total FROM blood_inventory WHERE status = 'Available'";
$avail_count = mysqli_fetch_assoc(mysqli_query($conn, $avail_sql))['total'];

$expiry_sql = "SELECT COUNT(*) as total FROM blood_inventory WHERE status = 'Expired' OR expiry_date < CURDATE()";
$expired_count = mysqli_fetch_assoc(mysqli_query($conn, $expiry_sql))['total'];

// Sidebar badge count
$pending_query = "SELECT COUNT(*) as total FROM users WHERE isApproved = 0";
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, $pending_query))['total'];

// 3. LOW STOCK ALERTS
$low_stock_query = "SELECT t.bloodGroup, SUM(i.quantity_ml) as total_qty 
                    FROM blood_inventory i 
                    JOIN bloodtypes t ON i.blood_type_id = t.bloodTypeId 
                    GROUP BY i.blood_type_id HAVING total_qty < 1000";
$low_stock_result = mysqli_query($conn, $low_stock_query);

// 4. MAIN TABLE DATA
$inventory_sql = "SELECT i.*, t.bloodGroup FROM blood_inventory i 
                  JOIN bloodtypes t ON i.blood_type_id = t.bloodTypeId
                 ORDER BY i.inventory_id ASC";
$inventory_items = mysqli_query($conn, $inventory_sql);

// 5. LOAD THE VIEW
include 'manage_inventory_view.php';
?>