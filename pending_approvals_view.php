<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Approvals - Admin</title>
    <link rel="stylesheet" href="donor.css">
</head>
<body>
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">Overview</a>
    <a href="manage_inventory.php">Inventory</a>
    <a href="pending_approvals.php" class="active">User Approvals (<?php echo $pending_count; ?>)</a>
    <a href="manage_subscriptions.php">Manage Subscriptions</a>
    <a href="manage_users.php">Manage Users</a>
    <a href="manage_requests.php">Blood Requests</a>
    <a href="manage_donations.php" class="<?php echo ($current_page == 'manage_donations') ? 'active' : ''; ?>">Donation Requests</a>
    <a href="view_invoices.php">Invoices</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h1>Pending User Approvals</h1>
    <p>The following users have registered but cannot login until approved.</p>

    <table style="margin-top: 20px; ">
        <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Chosen Plan / Fee Structure</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($pending_users) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($pending_users)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['fullName']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                    <td>
                        <form action="approve_user_action.php" method="GET">
                            <input type="hidden" name="id" value="<?php echo $row['userId']; ?>">
                            <input type="hidden" name="plan_id" value="<?php echo $row['plan_id']; ?>">
                            
                            <?php if ($row['role'] == 'Hospital'): ?>
                                <span class="badge-plan requested-plan">
                                    <strong><?php echo htmlspecialchars($row['plan_name']); ?> Plan</strong> (Requested)
                                </span>
                            <?php else: ?>
                                <span class="badge-info">Standard Fee Structure</span>
                            <?php endif; ?>
                    </td>
                    <td>
                            <button type="submit" class="btn-approve">Approve & Save</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center;">No pending approvals.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>