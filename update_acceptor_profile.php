<?php
session_start();
include 'db_connect.php';

// 1. SECURITY GATE: Ensure user is authorized
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$current_user_id = intval($_SESSION['user_id']);

// 2. CHECK IF FORM WAS SUBMITTED
if (isset($_POST['submit_profile_update_btn'])) {
    
    // Sanitize input values
    $updated_name = mysqli_real_escape_string($conn, $_POST['updated_name']);
    $updated_phone = mysqli_real_escape_string($conn, $_POST['updated_phone']);
    $new_password = $_POST['updated_password'];

    // 3. DETERMINISTIC UPDATE PATHWAYS (Plain Text)
    if (!empty($new_password)) {
        // Option A: User entered a new password. Save it directly as plain text.
        $new_password = mysqli_real_escape_string($conn, $new_password);
        
        $update_query = "UPDATE users SET fullName = ?, phone = ?, password = ? WHERE userId = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssi", $updated_name, $updated_phone, $new_password, $current_user_id);
    } else {
        // Option B: Password field was left blank. Keep the old password.
        $update_query = "UPDATE users SET fullName = ?, phone = ? WHERE userId = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $updated_name, $updated_phone, $current_user_id);
    }

    // 4. EXECUTE & REDIRECT
    if ($stmt->execute()) {
        header("Location: acceptor_profile.php?update_status=success");
        exit();
    } else {
        header("Location: acceptor_profile.php?update_status=error");
        exit();
    }
} else {
    header("Location: acceptor_profile.php");
    exit();
}
?>