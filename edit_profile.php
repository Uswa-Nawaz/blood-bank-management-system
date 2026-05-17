<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY GATE
 */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Donor') {
    header("Location: login.html");
    exit();
}

$current_user_id = $_SESSION['user_id'];

/**
 * 2. DATA RETRIEVAL
 */
$query = "SELECT fullName, phone FROM users WHERE userId = $current_user_id";
$result = mysqli_query($conn, $query);
$user_data = mysqli_fetch_assoc($result);

/**
 * 3. LOAD THE EDIT VIEW
 */
include 'edit_profile_view.php';
?>