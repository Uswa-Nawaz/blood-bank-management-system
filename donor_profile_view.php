<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="donor.css">
</head>
<body>

<div class="sidebar">
    <h2>Donor Panel</h2>
    <a href="donor_dashboard.php">Dashboard</a>
    <a href="donor_profile.php" class="active">My Profile</a>
    <a href="donation_history.php">Donation History</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h1>My Profile</h1>

    <div class="table-section" style="text-align: left;">
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($donor['fullName']); ?></p>
        <br>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($donor['phone']); ?></p>
        <br>
        <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($donor['bloodGroup'] ?? 'Not Assigned'); ?></p>
        <br>
        <a href="edit_profile.php" class="link-back" style="display: inline-block; text-decoration: none; text-align: center;" id="editProfileBtn">Edit Profile</a>        <!-- js -->
    </div>
</div>


<script>
    // Confirmation popup when clicking Edit Profile
    document.getElementById("editProfileBtn").addEventListener("click", function(e) {
        const confirmed = confirm("Are you sure you want to edit your profile?");

        if (!confirmed) {
            e.preventDefault(); // Stay on page if user clicks Cancel
        }
    });
</script>




</body>
</html>