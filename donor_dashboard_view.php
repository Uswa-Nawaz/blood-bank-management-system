<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="donor.css">
</head>
<body>

    <div class="sidebar">
        <h2>Donor Panel</h2>
        <a href="donor_dashboard.php" class="active">Dashboard</a>
        <a href="donor_profile.php">My Profile</a>
        <a href="donation_history.php">Donation History</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">

        <h1>Welcome, <?php echo htmlspecialchars($user_data['fullName']); ?></h1>

        <div class="card-container">

            <div class="card">
                <h3>Total Donations</h3>
                <p class="stat-number"><?php echo $total_donations; ?></p>
            </div>

            <div class="card">
                <h3>Pending Requests</h3>
                <p class="stat-number"><?php echo $pending_requests; ?></p>
            </div>

            <div class="card">
                <h3>Blood Group</h3>
                <p class="stat-number"><?php echo htmlspecialchars($user_data['bloodGroup'] ?? 'N/A'); ?></p>
            </div>

        </div>

        <div class="table-section" style="text-align: left;">
            <h2>Recent Donation Activity</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($history_result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($history_result)): ?>
                            <tr>
                                <td><?php echo date('d M, Y', strtotime($row['requestedDate'])); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($row['status']); ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" style="text-align: center; color: #888;">No recent donation activity found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>