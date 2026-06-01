<?php
session_start();
include 'db_connect.php';

// Security Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.html");
    exit();
}

/**
 * FETCH INVOICES WITH FULL RELATIONAL CONTEXT
 */
$sql = "SELECT 
            inv.*, 
            b.bloodGroup, 
            r.units_requested,
            IFNULL(u_sub.fullName, u_req.fullName) AS display_user_name
        FROM invoices inv
        LEFT JOIN users u_sub ON inv.user_id = u_sub.userId
        LEFT JOIN blood_requests r ON inv.request_id = r.request_id
        LEFT JOIN users u_req ON r.acceptor_id = u_req.userId
        LEFT JOIN bloodtypes b ON r.blood_type_id = b.bloodTypeId
        ORDER BY inv.invoice_date DESC";
$result = mysqli_query($conn, $sql);

// Summary Stats
$stats_sql = "SELECT SUM(total_amount) as revenue, COUNT(*) as count FROM invoices WHERE payment_status = 'Paid'";
$stats = mysqli_fetch_assoc(mysqli_query($conn, $stats_sql));

$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE isApproved = 0"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoices - Admin Panel</title>
    <link rel="stylesheet" href="donor.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Overview</a>
        <a href="manage_inventory.php">Inventory</a>
        <a href="pending_approvals.php">User Approvals (<?php echo $pending_count; ?>)</a>
        <a href="manage_subscriptions.php">Manage Subscriptions</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_requests.php">Blood Requests</a>
        <a href="manage_donations.php">Donation Requests</a>
        <a href="view_invoices.php" class="active">Invoices</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h1>Financial Records</h1>

        <div style="display: flex; gap: 20px; margin-bottom: 30px;">
            <div class="stat-card" style="background: #e3f2fd; border-left: 5px solid #2196f3; padding: 20px; flex: 1;">
                <h3 style="margin: 0; color: #1976d2;">Total Revenue</h3>
                <p style="font-size: 24px; font-weight: bold; margin: 10px 0;">Rs. <?php echo number_format($stats['revenue'] ?? 0, 2); ?></p>
            </div>
            <div class="stat-card" style="background: #e8f5e9; border-left: 5px solid #4caf50; padding: 20px; flex: 1;">
                <h3 style="margin: 0; color: #388e3c;">Processed Invoices</h3>
                <p style="font-size: 24px; font-weight: bold; margin: 10px 0;"><?php echo $stats['count']; ?></p>
            </div>
        </div>

        <div class="table-container">
            <h3>Recent Transactions</h3>
            <table>
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>User</th>
                        <th>Details</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>#<?php echo $row['invoice_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['display_user_name'] ?? 'System User'); ?></strong></td>
                            <td>
                                <?php if($row['invoice_type'] === 'Subscription'): ?>
                                    <span style="color: #666; font-style: italic;">Monthly Subscription</span>
                                <?php elseif($row['bloodGroup']): ?>
                                    <span class="badge-blood"><?php echo $row['bloodGroup']; ?></span> 
                                    (<?php echo $row['units_requested']; ?> units)
                                <?php else: ?>
                                    <span style="color: #888;">Service/Other</span>
                                <?php endif; ?>
                            </td>
                            <td>Rs. <?php echo number_format($row['total_amount'], 2); ?></td>
                            <td>
                                <?php if (floatval($row['total_amount']) == 0.00): ?>
                                    <span class="status-pill settled" style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 4px; font-weight: 600;">Settled</span>
                                <?php else: ?>
                                    <span class="status-pill <?php echo strtolower($row['payment_status']); ?>">
                                        <?php echo $row['payment_status']; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d M, Y', strtotime($row['invoice_date'])); ?></td>
                            <td>
                                <a href="print_invoice.php?id=<?php echo $row['invoice_id']; ?>">View</a> 
                                
                                <?php if (floatval($row['total_amount']) > 0 && strtolower($row['payment_status']) !== 'paid'): ?>
                                    | <a href="mark_paid.php?id=<?php echo $row['invoice_id']; ?>" style="color: blue;">Mark Paid</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align: center;">No invoices found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>