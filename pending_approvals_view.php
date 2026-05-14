<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Approvals - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">Overview</a>
    <a href="manage_inventory.php">Inventory</a>
    <a href="pending_approvals.php" class="active">User Approvals (<?php echo $pending_count; ?>)</a>
    <a href="manage_users.php">Manage Users</a>
    <a href="manage_requests.php">Blood Requests</a>
    <a href="view_invoices.php">Invoices</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h1>Pending User Approvals</h1>
    <p>The following users have registered but cannot login until approved.</p>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Assign Plan</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($pending_users) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($pending_users)): ?>
                <tr>
                    <td><?php echo $row['fullName']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <td>
                        <form action="approve_user_action.php" method="GET">
                            <input type="hidden" name="id" value="<?php echo $row['userId']; ?>">
                            
                            <?php if ($row['role'] == 'Hospital'): ?>
                                <select name="plan_id" class="form-select">
                                    <?php foreach($plans_list as $plan): ?>
                                        <?php if($plan['plan_name'] != 'None'): ?>
                                            <option value="<?php echo $plan['plan_id']; ?>"><?php echo $plan['plan_name']; ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <span class="badge-info">Standard Fee</span>
                                <input type="hidden" name="plan_id" value="1">
                            <?php endif; ?>
                    </td>
                    <td>
                            <button type="submit" class="btn-approve">Approve & Assign</button>
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