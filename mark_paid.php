<?php
session_start();
include 'db_connect.php';

if (isset($_GET['id']) && $_SESSION['user_role'] === 'Admin') {
    $id = $_GET['id'];
    $sql = "UPDATE invoices SET payment_status = 'Paid' WHERE invoice_id = $id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: view_invoices.php?msg=PaidSuccess");
    }
}
exit();
?>