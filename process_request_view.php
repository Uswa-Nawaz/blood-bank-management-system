<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Process Request - Admin</title>
    <link rel="stylesheet" href="donor.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Overview</a>
        <a href="manage_inventory.php">Inventory</a>
        <a href="pending_approvals.php">User Approvals (<?php echo $pending_count; ?>)</a>
        <a href="manage_subscriptions.php" >Manage Subscriptions</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_requests.php" class="active">Blood Requests</a>
        <a href="manage_donations.php" class="<?php echo ($current_page == 'manage_donations') ? 'active' : ''; ?>">Donation Requests</a>
        <a href="view_invoices.php">Invoices</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="content-header">
            <h1>Process Request #<?php echo $req_id; ?></h1>
            <a href="manage_requests.php" class="link-back">← Back to Requests</a>
        </div>

        <div class="info-card">
            <div class="info-row">
                <span><strong>Patient:</strong> <?php echo htmlspecialchars($req_data['patient_name']); ?></span>
                <span><strong>Group:</strong> <b class="text-danger"><?php echo $req_data['bloodGroup']; ?></b></span>
            </div>
            <div class="info-row">
                <span><strong>Units Needed:</strong> <?php echo $req_data['units_requested']; ?></span>
                <span><strong>Status:</strong> <span class="badge-status"><?php echo $req_data['status']; ?></span></span>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-panel">
            <h3>Quick Status Update</h3>
            <div class="btn-group">
                <a href="update_request_status.php?id=<?php echo $req_id; ?>&status=Rejected" 
                   class="btn-danger" onclick="return confirm('Reject this request?')">Reject Request</a>
                
                <a href="update_request_status.php?id=<?php echo $req_id; ?>&status=Pending" 
                   class="btn-secondary">Keep Pending</a>
            </div>
            <p class="helper-text">* To fulfill, assign a specific bag from the inventory list below.</p>
        </div>

        <h3>Matching Available Inventory (FEFO Basis)</h3>
        <div class="table-container" margin-top="20px">
            <table>
                <thead>
                    <tr>
                        <th>Bag ID</th>
                        <th>Component</th>
                        <th>Quantity</th>
                        <th>Location</th>
                        <th>Expiry Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($inventory_res->num_rows > 0): ?>
                        <?php while($bag = $inventory_res->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $bag['inventory_id']; ?></td>
                            <td><?php echo $bag['component_type']; ?></td>
                            <td><?php echo $bag['quantity_ml']; ?> ml</td>
                            <td><?php echo $bag['storage_location']; ?></td>
                            <td><strong><?php echo $bag['expiry_date']; ?></strong></td>
                            <td>
                                <a href="fulfill_action.php?req_id=<?php echo $req_id; ?>&bag_id=<?php echo $bag['inventory_id']; ?>" 
                                   class="btn-assign" onclick="return confirm('Assign this bag?')">Assign Bag</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr class="out-of-stock-row">
                            <td colspan="6">
                                <strong>Out of Stock!</strong> No available <?php echo $req_data['bloodGroup']; ?> units found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>