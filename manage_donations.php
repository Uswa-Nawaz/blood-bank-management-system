<?php
session_start();
include 'db_connect.php';

// Security Gate
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.html");
    exit();
}

// MODIFIED QUERY: Fetches both Pending and Approved requests so we can group them into separate tables
$query = "SELECT dr.donationId, dr.requestedDate, dr.status, dr.donorId, dr.bloodTypeId,
                 u.fullName, u.phone, 
                 b.bloodGroup 
          FROM donationrequests dr
          INNER JOIN users u ON dr.donorId = u.userId
          INNER JOIN bloodtypes b ON dr.bloodTypeId = b.bloodTypeId
          WHERE dr.status IN ('Pending', 'Approved')
          ORDER BY dr.requestedDate ASC";

$requests_result = mysqli_query($conn, $query);

if (!$requests_result) {
    die("Database query failed: " . mysqli_error($conn));
}

// Load the updated view template
include 'manage_donations_view.php';
?>