<?php
session_start();
include 'db_connect.php';

// Only Admin can run this
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    exit("Unauthorized");
}

// 1. Get all approved Hospitals and their plan details
$sql = "SELECT u.userId, u.fullName, p.monthly_fee, p.plan_name 
        FROM users u 
        JOIN subscription_plans p ON u.plan_id = p.plan_id 
        WHERE u.role = 'Hospital' AND u.isApproved = 1";

$result = mysqli_query($conn, $sql);
$current_month = date('Y-m-01'); // First day of current month

while($hospital = mysqli_fetch_assoc($result)) {
    $uid = $hospital['userId'];
    $fee = $hospital['monthly_fee'];
    $desc = "Monthly Subscription: " . $hospital['plan_name'];

    // 2. Prevent duplicate billing for the same month
    $check = "SELECT * FROM invoices WHERE user_id = $uid AND billing_period = '$current_month' AND invoice_type = 'Subscription'";
    $exists = mysqli_query($conn, $check);

    if(mysqli_num_rows($exists) == 0) {
        // 3. Insert Invoice
        $insert = "INSERT INTO invoices (user_id, total_amount, invoice_date, billing_period, invoice_type, payment_status) 
                   VALUES ($uid, $fee, CURDATE(), '$current_month', 'Subscription', 'Unpaid')";
        mysqli_query($conn, $insert);
    }
}

header("Location: view_invoices.php?msg=SubscriptionsGenerated");
?>