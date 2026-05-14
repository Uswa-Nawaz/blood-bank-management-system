<?php
session_start();
include 'db_connect.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    // Update the status in the database
    $sql = "UPDATE blood_requests SET status = '$status' WHERE request_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_requests.php?msg=StatusUpdated");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>