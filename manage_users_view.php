<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Overview</a>
        <a href="manage_inventory.php">Inventory</a>
        <a href="pending_approvals.php">User Approvals (<?php echo $pending_count; ?>)</a>
        <a href="manage_subscriptions.php" class="active">Manage Subscriptions</a>
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
                 <p class="stat-number" id="donorCount"><?php echo $donor_count; ?></p>     <!-- js -->
            </div>
            <div class="card">
                <h3>Accepted Acceptors</h3>
                <p class="stat-number" id="acceptorCount"><?php echo $acceptor_count; ?></p>       <!-- js -->
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



<script>
    // Animated number counter function
    function animateCounter(elementId, target) {
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
            el.textContent = current;
        }, stepTime);
    }

    // Read values from PHP and animate
    const donorCount = parseInt(document.getElementById("donorCount").textContent);
    const acceptorCount = parseInt(document.getElementById("acceptorCount").textContent);

    animateCounter("donorCount", donorCount);
    animateCounter("acceptorCount", acceptorCount);
</script>



</body>
</html>