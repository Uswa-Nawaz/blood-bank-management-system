<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Blood Requests - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Overview</a>
        <a href="manage_inventory.php">Inventory</a>
        <a href="pending_approvals.php">User Approvals (<?php echo $pending_count; ?>)</a>
        <a href="manage_subscriptions.php" class="active">Manage Subscriptions</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_requests.php" class="active">Blood Requests</a>
        <a href="manage_donations.php" class="<?php echo ($current_page == 'manage_donations') ? 'active' : ''; ?>">Donation Requests</a>
        <a href="view_invoices.php">Invoices</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h1>Blood Requests Management</h1>

        <div class="table-container">
            <h3>Active Requests</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient/Hospital</th>
                        <th>Group</th>
                        <th>Amount</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $urgency_class = ($row['is_urgent']) ? 'status-urgent' : 'status-normal';
                            $status_class = strtolower($row['status']);
                        ?>
                        <tr>
                            <td><?php echo $row['request_id']; ?></td>
                            <td>
                                <strong><?php echo $row['fullName']; ?></strong><br>
                                <small class="text-muted"><?php echo $row['phone']; ?></small>
                            </td>
                            <td><span class="blood-group-tag"><?php echo $row['bloodGroup']; ?></span></td>
                            <td><?php echo $row['units_requested']; ?> Units</td>
                            <td>
                                <span class="urgency-badge <?php echo $urgency_class; ?>">
                                    <?php echo ($row['is_urgent']) ? '⚠ URGENT' : 'Normal'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-pill <?php echo $status_class; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="process_request.php?id=<?php echo $row['request_id']; ?>" class="btn-process">
                                    Process
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;">No active blood requests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>