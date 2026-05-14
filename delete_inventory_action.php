<?php
session_start();
include 'db_connect.php';

// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized.");
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // LOGIC: Delete the specific blood unit
    $sql = "DELETE FROM blood_inventory WHERE inventory_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_inventory.php?msg=deleted");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>