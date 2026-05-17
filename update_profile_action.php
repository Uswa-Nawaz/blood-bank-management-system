<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY GATE
 */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Donor') {
    exit("Unauthorized access.");
}

$current_user_id = $_SESSION['user_id'];

if (isset($_POST['fullname']) && isset($_POST['phone'])) {
    
    // 2. DATA SANITIZATION
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $password_input = trim($_POST['password']); // Plain text input

    // Input validation checks
    if (empty($fullname) || empty($phone)) {
        die("<script>alert('Error: Name and Phone fields cannot be empty!'); window.history.back();</script>");
    }

    /**
     * 3. PLAIN TEXT UPDATE LOGIC
     * No hashing algorithms applied here. It saves exactly what is typed.
     */
    if (!empty($password_input)) {
        // Option A: Update with new password as a plain string
        $sql = "UPDATE users SET fullName = ?, phone = ?, password = ? WHERE userId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $fullname, $phone, $password_input, $current_user_id);
    } else {
        // Option B: Update details only, leaving current password untouched
        $sql = "UPDATE users SET fullName = ?, phone = ? WHERE userId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $fullname, $phone, $current_user_id);
    }

    // 4. EXECUTE THE UPDATE
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: donor_profile.php?update=success");
        exit();
    } else {
        error_log("Profile Update Failure: " . $stmt->error);
        $stmt->close();
        die("An unexpected database error occurred. Your changes were not saved.");
    }

} else {
    header("Location: donor_profile.php");
    exit();
}
?>