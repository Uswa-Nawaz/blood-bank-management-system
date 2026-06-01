<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Management - Admin</title>
    <link rel="stylesheet" href="donor.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Overview</a>
        <a href="manage_inventory.php" class="active">Inventory</a>
        <a href="pending_approvals.php">User Approvals (<?php echo $pending_count; ?>)</a>
        <a href="manage_subscriptions.php" >Manage Subscriptions</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_requests.php">Blood Requests</a>
        <a href="manage_donations.php" class="<?php echo ($current_page == 'manage_donations') ? 'active' : ''; ?>">Donation Requests</a>
        <a href="view_invoices.php">Invoices</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h1>Inventory Management</h1>

        <!-- Stats Cards -->
        <div class="card-container">
            <div class="card">
                <h3>Total Inventory</h3>
                <p class="stat-main"><?php echo number_format($total_units); ?> ml</p>
            </div>
            <div class="card">
                <h3>Available</h3>
                <p class="stat-sub"><?php echo $avail_count; ?> Bags</p>
            </div>
            <div class="card">
                <h3>Reserved</h3>
                <p class="stat-sub"><?php echo $reserved_count; ?> Bags</p> 
            </div>
            <div class="card">
                <h3>Expired</h3>
                <p class="stat-err"><?php echo $expired_count; ?> Bags</p>
            </div>
        </div>

        <!-- Alerts Section -->
        <?php if(mysqli_num_rows($low_stock_result) > 0): ?>
        <div class="alert-panel low-stock-warning">
            <strong>⚠ Low Stock Alerts:</strong>
            <?php while($low = mysqli_fetch_assoc($low_stock_result)): ?>
                <span class="alert-item">[<?php echo $low['bloodGroup']; ?>: <?php echo $low['total_qty']; ?>ml]</span>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <div class="action-bar">
            <a href="add_inventory.php" class="btn-primary">+ Add New Blood Unit</a>
        </div>

        <!-- Inventory Table -->
        <div class="table-container">
            <h3>Current Blood Stock</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Group</th>
                        <th>Component</th>
                        <th>Quantity</th>
                        <th>Location</th> 
                        <th>Expiry</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($inventory_items)): 
                        $status_class = strtolower($row['status']); 
                    ?>
                    <tr>
                        <td><?php echo $row['inventory_id']; ?></td>
                        <td><strong><?php echo $row['bloodGroup']; ?></strong></td>
                        <td><?php echo $row['component_type']; ?></td>
                        <td><?php echo $row['quantity_ml']; ?> ml</td>
                        <td><?php echo $row['storage_location']; ?></td> 
                        <td><?php echo $row['expiry_date']; ?></td>
                        <td>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit_inventory.php?id=<?php echo $row['inventory_id']; ?>" class="link-edit">Edit</a>
                            <a href="delete_inventory_action.php?id=<?php echo $row['inventory_id']; ?>" class="link-delete" onclick="return confirm('Delete this record?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>