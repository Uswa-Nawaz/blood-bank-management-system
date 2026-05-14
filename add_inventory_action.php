<?php
session_start();
include 'db_connect.php';

// 1. SECURITY GATE
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized access.");
}

if (isset($_POST['submit_add'])) {

    // Capture inputs
    $blood_type_id = intval($_POST['blood_type_id']);
    $component = $_POST['component_type'];
    $qty = intval($_POST['quantity_ml']);
    $loc = $_POST['storage_location'];
    $expiry = $_POST['expiry_date'];
    $donor_input = trim($_POST['donor_ref']);
    $today = date('Y-m-d');

    // 2. HARD LIMIT CHECKS (Safety Gate)
    if ($qty < 100 || $qty > 1000) {
        die("<script>alert('Error: Quantity must be between 100ml and 1000ml!'); window.history.back();</script>");
    }
    if ($expiry < $today) {
        die("<script>alert('Error: Expiry date cannot be in the past!'); window.history.back();</script>");
    }

    // 3. DONOR VERIFICATION
    $donor_final_ref = "Walk-in"; // Default

    if (!empty($donor_input) && is_numeric($donor_input)) {
        // Use a prepared statement for the check
        $stmt_check = $conn->prepare("SELECT fullName FROM users WHERE userId = ? AND role = 'Donor' AND isApproved = 1");
        $stmt_check->bind_param("i", $donor_input);
        $stmt_check->execute();
        $user_result = $stmt_check->get_result();

        if ($user_result->num_rows == 0) {
            die("<script>alert('Error: ID #$donor_input is not a verified Donor.'); window.history.back();</script>");
        } else {
            $user_data = $user_result->fetch_assoc();
            $donor_final_ref = $user_data['fullName'] . " (ID: $donor_input)";
        }
        $stmt_check->close();
    } elseif (!empty($donor_input)) {
        $donor_final_ref = $donor_input; // If it's a name instead of an ID
    }

    // 4. EXECUTE INSERT (Prepared Statement)
    $sql = "INSERT INTO blood_inventory (blood_type_id, quantity_ml, component_type, storage_location, expiry_date, collection_date, donor_ref, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Available')";
    
    $stmt_insert = $conn->prepare($sql);
    $stmt_insert->bind_param("iisssss", $blood_type_id, $qty, $component, $loc, $expiry, $today, $donor_final_ref);

    if ($stmt_insert->execute()) {
        header("Location: manage_inventory.php?success=1");
    } else {
        error_log("Inventory Error: " . $stmt_insert->error);
        die("A database error occurred. Please try again.");
    }
    
    $stmt_insert->close();
} else {
    header("Location: manage_inventory.php");
}
exit();