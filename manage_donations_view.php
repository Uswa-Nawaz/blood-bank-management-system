<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Donation Requests - Admin Panel</title>
    <link rel="stylesheet" href="donor.css">
    <style>
        .admin-container { display: flex; font-family: sans-serif; background-color: #f8f9fa; min-height: 100vh; }
        .sidebar { width: 250px; background-color: #7a1c1c; color: white; padding: 20px; }
        .main-content { flex: 1; padding: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 40px; background: white; border-radius: 6px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #bc1c24; color: white; }
        .action-link-approve { color: #2e7d32; text-decoration: none; font-weight: bold; margin-right: 15px; }
        .action-link-collect { color: #0288d1; text-decoration: none; font-weight: bold; margin-right: 15px; }
        .reject-link { color: #c62828; text-decoration: none; font-weight: bold; }
        .table-heading { color: #333; margin-top: 10px; border-bottom: 2px solid #ddd; padding-bottom: 5px; }
        .badge-approved { background-color: #e8f5e9; color: #2e7d32; padding: 5px 10px; border-radius: 4px; font-weight: bold; font-size: 13px; }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Overview</a>
        <a href="manage_inventory.php">Inventory</a>
        <a href="pending_approvals.php">User Approvals</a>
        <a href="manage_subscriptions.php">Manage Subscriptions</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_requests.php">Blood Requests</a>
        <a href="manage_donations.php" class="active">Donation Requests</a>
        <a href="view_invoices.php">Invoices</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h1>Donation Requests Management</h1>
        
        <?php
        // Separate the mysqli resource rows into two temporary working arrays
        $pending_requests = [];
        $approved_requests = [];
        
        while ($row = mysqli_fetch_assoc($requests_result)) {
            if ($row['status'] === 'Pending') {
                $pending_requests[] = $row;
            } elseif ($row['status'] === 'Approved') {
                $approved_requests[] = $row;
            }
        }
        ?>

        <h3 class="table-heading">1. New Booking Intents (Awaiting Appointment Approval)</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Donor Name</th>
                    <th>Phone</th>
                    <th>Blood Group</th>
                    <th>Target Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pending_requests) > 0): ?>
                    <?php foreach ($pending_requests as $row): ?>
                        <tr>
                            <td><?php echo $row['donationId']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['fullName']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><span style="color: #bc1c24; font-weight: bold;"><?php echo htmlspecialchars($row['bloodGroup']); ?></span></td>
                            <td><?php echo date('d M, Y', strtotime($row['requestedDate'])); ?></td>
                            <td>
                                <a href="approve_donation_action.php?id=<?php echo $row['donationId']; ?>" class="action-link-approve">
                                    Approve Slot
                                </a>
                                <a href="reject_donation_action.php?id=<?php echo $row['donationId']; ?>" onclick="return confirm('Are you sure you want to reject this appointment?');" class="reject-link">         <!-- js -->
                                    Reject
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #888; padding: 25px;">No new booking intents found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>


        <h3 class="table-heading">2. Confirmed Slots (Awaiting Physical Arrival & Extraction)</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Donor Name</th>
                    <th>Phone</th>
                    <th>Blood Group</th>
                    <th>Appointment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($approved_requests) > 0): ?>
                    <?php foreach ($approved_requests as $row): ?>
                        <tr>
                            <td><?php echo $row['donationId']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['fullName']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><span style="color: #bc1c24; font-weight: bold;"><?php echo htmlspecialchars($row['bloodGroup']); ?></span></td>
                            <td><span class="badge-approved">Confirmed / Expected</span></td>
                            <td>
                                <a href="add_inventory.php?req_id=<?php echo $row['donationId']; ?>&donor_id=<?php echo $row['donorId']; ?>&blood_group_id=<?php echo $row['bloodTypeId']; ?>" class="action-link-collect">
                                    Process & Collect Blood
                                </a>
                                <a href="reject_donation_action.php?id=<?php echo $row['donationId']; ?>" onclick="return confirm('Mark as No-Show / Reject?');" class="reject-link">    <!-- js -->
                                    Cancel
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #888; padding: 25px;">No confirmed donors scheduled for extraction today.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>