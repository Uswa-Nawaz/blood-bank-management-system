<!-- This file is tasbiha would work on.
It contains the 
1. HTML 
2. small PHP tags to echo the variables, created in the Controller. -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - BloodConnect</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php" class="active">Overview</a>
        <a href="manage_inventory.php">Inventory</a>
        <a href="pending_approvals.php">User Approvals (<?php echo $pending_count; ?>)</a>
        <a href="manage_subscriptions.php" class="active">Manage Subscriptions</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_requests.php">Blood Requests</a>
        <a href="manage_donations.php" class="<?php echo ($current_page == 'manage_donations') ? 'active' : ''; ?>">Donation Requests</a>
        <a href="view_invoices.php">Invoices</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h1>Admin Overview</h1>
        
        <div class="card-container">
            <div class="card">
                <h3>Total Inventory</h3>
                <p id="totalInventory"><?php echo number_format($total_inventory, 0); ?> ml</p>       <!-- js -->
            </div>
            <div class="card">
                <h3>Pending Approvals</h3>
                <p id="pendingCount"><?php echo $pending_count; ?> Users</p>         <!-- js -->
            </div>
            <div class="card">
                <h3>Urgent Requests</h3>
                <p id="urgentCount"><?php echo $urgent_count; ?> Requests</p>        <!-- js -->
            </div>
        </div>

        <h3>Recent Blood Inventory</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Blood Group</th>
                    <th>Quantity (ml)</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($inventory_list) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($inventory_list)): ?>
                        <tr>
                            <td><?php echo $row['inventory_id']; ?></td>
                            <td><?php echo $row['bloodGroup']; ?></td>
                            <td><?php echo number_format($row['quantity_ml'], 2); ?></td>
                            <td><?php echo $row['expiry_date']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan='5' style='text-align:center;'>No inventory records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


<script>
    // Animated number counter function
    function animateCounter(elementId, target, suffix) {
        const el = document.getElementById(elementId);
        let current = 0;
        const duration = 1000; // 1 second
        const stepTime = 50;   // update every 50ms
        const steps = duration / stepTime;
        const increment = Math.ceil(target / steps);

        const timer = setInterval(function() {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = current + suffix;
        }, stepTime);
    }

    // Read values from PHP and animate
    const totalInventory = parseInt(document.getElementById("totalInventory").textContent);
    const pendingCount = parseInt(document.getElementById("pendingCount").textContent);
    const urgentCount = parseInt(document.getElementById("urgentCount").textContent);

    animateCounter("totalInventory", totalInventory, " ml");
    animateCounter("pendingCount", pendingCount, " Users");
    animateCounter("urgentCount", urgentCount, " Requests");
</script>




</body>
</html>