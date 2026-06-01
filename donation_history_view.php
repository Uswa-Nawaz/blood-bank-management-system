<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History</title>
    <link rel="stylesheet" href="donor.css">
</head>
<body>

<div class="sidebar">
    <h2>Donor Panel</h2>
    <a href="donor_dashboard.php">Dashboard</a>
    <a href="donor_profile.php">My Profile</a>
    <a href="donation_history.php" class="active">Donation History</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">

    <h1>Donation History</h1>

    <div class="table-section">

        <!-- Total donation count -->
<p id="donationCount" style="font-weight:bold; margin-bottom:10px;"></p>    <!-- js for Adding a paragraph above the table to show the count -->
<table>
            <thead>
                <tr>
                    <th>Donation ID</th>
                    <th>Date</th>
                    <th>Blood Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="donationBody">       <!-- js for Show total donation count on top -->
                <?php if (mysqli_num_rows($history_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($history_result)): 
                        $status_class = strtolower($row['status']);
                    ?>
                        <tr>
                            <td>#<?php echo $row['donationId']; ?></td> <td><?php echo date('d M Y', strtotime($row['requestedDate'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['bloodGroup']); ?></strong></td>
                            <td>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; color: #888; padding: 20px;">
                            No donation history records found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

</div>

<script>
    // Count total donation rows
    const rows = document.querySelectorAll("#donationBody tr");
    const total = rows.length;

    // Display count
    document.getElementById("donationCount").textContent = "Total Donations: " + total;
</script>



</body>
</html>