<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY GATE
 * Checks if the user is an Acceptor, Hospital, or Individual.
 */
$allowed_roles = ['Acceptor', 'Hospital', 'Individual'];
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowed_roles)) {
    header("Location: login.html");
    exit();
}

$current_user_id = $_SESSION['user_id'];

/** 
 * 2. BUSINESS LOGIC (Fetching Acceptor Stats)
 */

// Count Active (Pending) Requests
$active_query = "SELECT COUNT(*) as total FROM blood_requests 
                 WHERE acceptor_id = $current_user_id AND status = 'Pending'";
$active_result = mysqli_query($conn, $active_query);
$active_count = mysqli_fetch_assoc($active_result)['total'];

// Count Fulfilled Requests
$fulfilled_query = "SELECT COUNT(*) as total FROM blood_requests 
                    WHERE acceptor_id = $current_user_id AND status = 'Fulfilled'";
$fulfilled_result = mysqli_query($conn, $fulfilled_query);
$fulfilled_count = mysqli_fetch_assoc($fulfilled_result)['total'];

// Calculate Pending Payments (Unpaid Invoices)
$payment_query = "SELECT SUM(i.total_amount) as total_unpaid 
                  FROM invoices i 
                  JOIN blood_requests r ON i.request_id = r.request_id 
                  WHERE r.acceptor_id = $current_user_id AND i.payment_status = 'Unpaid'";
$payment_result = mysqli_query($conn, $payment_query);
$unpaid_amount = mysqli_fetch_assoc($payment_result)['total_unpaid'] ?? 0.00;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceptor Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Acceptor Portal</h2>
        <a href="#">Request Blood</a>
        <a href="#">My Requests</a>
        <a href="#">Billing/Invoices</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h1>Welcome, <?php echo $_SESSION['user_name']; ?></h1>
        
        <div class="card-container">
            <div class="card">
                <h3>Active Requests</h3>
                <p><?php echo $active_count; ?></p>
            </div>
            <div class="card">
                <h3>Fulfilled</h3>
                <p><?php echo $fulfilled_count; ?></p>
            </div>
            <div class="card">
                <h3>Pending Payment</h3>
                <p>$<?php echo number_format($unpaid_amount, 2); ?></p>
            </div>
        </div>

        <h3>Your Blood Requests</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Units</th>
                    <th>Urgency</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetching history from blood_requests table
                $history_sql = "SELECT request_date, units_requested, is_urgent, status 
                                FROM blood_requests 
                                WHERE acceptor_id = $current_user_id 
                                ORDER BY request_date DESC";
                
                $history_result = mysqli_query($conn, $history_sql);

                if (mysqli_num_rows($history_result) > 0) {
                    while($row = mysqli_fetch_assoc($history_result)) {
                        $urgency_label = ($row['is_urgent'] == 1) ? "Yes (Urgent)" : "Normal";
                        
                        echo "<tr>";
                        echo "<td>" . $row['request_date'] . "</td>";
                        echo "<td>" . $row['units_requested'] . "</td>";
                        echo "<td>" . $urgency_label . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center;'>No requests found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>