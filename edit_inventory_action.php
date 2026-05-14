<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY GATE
 * Ensure only an Admin can perform updates.
 */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized access.");
}

if (isset($_POST['submit_edit'])) {
    // 2. DATA CAPTURE
    // Use intval for numeric IDs and quantities to ensure data type safety
    $id = intval($_POST['inventory_id']);
    $blood_type_id = intval($_POST['blood_type_id']);
    $qty = intval($_POST['quantity_ml']);
    $loc = $_POST['storage_location'];
    $status = $_POST['status'];

    /**
     * 3. PREPARED STATEMENT LOGIC
     * This separates the SQL command from the data for maximum security.
     */
    $sql = "UPDATE blood_inventory 
            SET blood_type_id = ?, 
                quantity_ml = ?, 
                storage_location = ?, 
                status = ? 
            WHERE inventory_id = ?";

    $stmt = $conn->prepare($sql);
    
    // "iissi" corresponds to: integer, integer, string, string, integer
    $stmt->bind_param("iissi", $blood_type_id, $qty, $loc, $status, $id);

    if ($stmt->execute()) {
        // Redirect with a success flag
        header("Location: manage_inventory.php?msg=updated");
        exit();
    } else {
        // Log the technical error and show a user-friendly message
        error_log("Update Error: " . $stmt->error);
        die("An error occurred while updating the inventory record.");
    }

    $stmt->close();
} else {
    // If the file is accessed directly without a form submission
    header("Location: manage_inventory.php");
    exit();
}