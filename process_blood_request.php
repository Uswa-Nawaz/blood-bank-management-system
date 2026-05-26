<?php
session_start();
include 'db_connect.php';

// 1. SECURITY GATE: Ensure user is authorized to request blood
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// 2. CHECK IF FORM WAS SUBMITTED
if (isset($_POST['submit_request_btn'])) {
    
    // Sanitize input fields
    $acceptor_id = intval($_SESSION['user_id']);
    $patient_name = mysqli_real_escape_string($conn, $_POST['patient_name']);
    $blood_type_id = intval($_POST['blood_group_id']);
    $units_requested = intval($_POST['units_requested']);
    $hospital_name = mysqli_real_escape_string($conn, $_POST['hospital_name']);
    $is_urgent = intval($_POST['is_urgent']);
    $status = 'Pending'; // Default status for new requests

    // 3. DATABASE INSERTION
    // Adjust column names here if your blood_requests table uses different casing
    $insert_query = "INSERT INTO blood_requests (acceptor_id, patient_name, blood_type_id, units_requested, hospital_name, is_urgent, status, request_date) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                     
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("isiisis", $acceptor_id, $patient_name, $blood_type_id, $units_requested, $hospital_name, $is_urgent, $status);

    if ($stmt->execute()) {
        // Redirect back to dashboard with a success message
        header("Location: acceptor_dashboard.php?success=1");
        exit();
    } else {
        // Handle insertion error safely
        exit("Error submitting request: " . $conn->error);
    }
} else {
    // If someone tries to access this file directly without submitting form
    header("Location: acceptor_dashboard.php");
    exit();
}
?>