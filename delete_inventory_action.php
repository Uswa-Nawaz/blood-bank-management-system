<?php
session_start();
include 'db_connect.php';


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. First, delete any transactions associated with this inventory ID
    $delete_transactions = "DELETE FROM fulfillment_transactions WHERE inventory_id = $id";
    mysqli_query($conn, $delete_transactions);

    // 2. Now you can safely delete the inventory item
    $sql = "DELETE FROM blood_inventory WHERE inventory_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: manage_inventory.php?msg=deleted");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

?>