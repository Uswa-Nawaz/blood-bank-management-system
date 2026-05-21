<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscription Management - Admin</title>
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
        <a href="manage_requests.php">Blood Requests</a>
        <a href="view_invoices.php">Invoices</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h1>Subscription Framework & Tiers</h1>
        <p>Overview of current corporate subscription parameters and active hospital licenses.</p>

        <div class="card-container" style="display: flex; gap: 20px; margin-bottom: 30px;">
            <?php while($plan = mysqli_fetch_assoc($plans_data)): ?>
                <div class="card" style="flex: 1; border: 1px solid #ddd; padding: 20px; border-radius: 8px; background: #fff;">
                    <h3 style="color: #bc1c24; margin-top: 0;"><?php echo htmlspecialchars($plan['plan_name']); ?> Tier</h3>
                    <hr style="border: 0; border-top: 1px solid #eee;">
                    <p><strong>Monthly Premium:</strong> Rs. <?php echo number_format($plan['monthly_fee'], 2); ?></p>
                    <p><strong>Included Volume:</strong> <?php echo ($plan['unit_limit'] === null) ? 'Unlimited Bags' : $plan['unit_limit'] . ' Units'; ?></p>
                    <p><strong>OverUsage Charge:</strong> Rs. <?php echo number_format($plan['price_per_unit'], 2); ?> / unit</p>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="table-container">
            <h3>Subscribed Hospital Partners</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hospital Name</th>
                        <th>Contact Number</th>
                        <th>Active Tier</th>
                        <th>Monthly Base Fee</th>
                        <th>Volume Limit</th>
                        <th>Member Since</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($subscribed_hospitals) > 0): ?>
                        <?php while($hospital = mysqli_fetch_assoc($subscribed_hospitals)): ?>
                        <tr>
                            <td>#<?php echo $hospital['userId']; ?></td>
                            <td><strong><?php echo htmlspecialchars($hospital['fullName']); ?></strong></td>
                            <td><?php echo htmlspecialchars($hospital['phone']); ?></td>
                            <td>
                                <span class="badge-plan text-uppercase">
                                    <?php echo htmlspecialchars($hospital['plan_name']); ?>
                                </span>
                            </td>
                            <td>Rs. <?php echo number_format($hospital['monthly_fee'], 2); ?></td>
                            <td><?php echo ($hospital['unit_limit'] === null) ? 'Unlimited' : $hospital['unit_limit'] . ' Units'; ?></td>
                            <td><?php echo date('d M, Y', strtotime($hospital['createdAt'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;">No hospitals have been approved or registered yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>