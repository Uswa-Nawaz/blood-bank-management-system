<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="donor.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Overview</a>
        <a href="manage_inventory.php">Inventory</a>
        <a href="pending_approvals.php">User Approvals (<?php echo $pending_count; ?>)</a>
        <a href="manage_subscriptions.php">Manage Subscriptions</a>
        <a href="manage_users.php" class="active">Manage Users</a>
        <a href="manage_requests.php">Blood Requests</a>
        <a href="manage_donations.php" class="<?php echo ($current_page == 'manage_donations') ? 'active' : ''; ?>">Donation Requests</a>
        <a href="view_invoices.php">Invoices</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h1>User Management</h1>

        <div class="card-container">
            <div class="card">
                <h3>Accepted Donors</h3>
                <p class="stat-number"><?php echo $donor_count; ?></p>
            </div>
            <div class="card">
                <h3>Accepted Acceptors</h3>
                <p class="stat-number"><?php echo $acceptor_count; ?></p>
            </div>
        </div>

        <h3>Verified User List</h3>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Blood Group</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($users_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($users_result)): ?>
                        <tr>
                            <td><?php echo $row['userId']; ?></td>
                            <td><?php echo $row['fullName']; ?></td>
                            <td><?php echo $row['role']; ?></td>
                            <td><?php echo $row['bloodGroup'] ?? 'N/A'; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan='5' style='text-align:center;'>No verified users found yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>