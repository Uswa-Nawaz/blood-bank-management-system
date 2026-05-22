<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Donor') {
    header("Location: login.html");
    exit();
}

if (isset($_POST['submit_donation'])) {
    $donor_id = $_SESSION['user_id'];
    $blood_type_id = intval($_POST['blood_type_id']);
    $quantity_ml = intval($_POST['quantity_ml']);
    
    // Grab user's chosen calendar date and escape it
    $chosen_date = mysqli_real_escape_string($conn, $_POST['appointment_date']);
    
    $status = 'Pending';
    $actual_collected = 0; 

    // Explicitly pass the chosen calendar date into requestedDate
    $insert_sql = "INSERT INTO donationrequests (donorId, bloodTypeId, requestedDate, status, actualQuantityCollected) 
                   VALUES (?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($stmt, "iisss", $donor_id, $blood_type_id, $chosen_date, $status, $actual_collected);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: donor_dashboard.php?msg=request_submitted");
        exit();
    } else {
        echo "Error saving booking request: " . mysqli_error($conn);
    }
} else {
    header("Location: donor_dashboard.php");
    exit();
}
?>