<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Edit Profile Details</h1>
        <a href="donor_profile.php" style="text-decoration: none; color: #992828; font-weight: bold;">← Back to Profile</a>
    </div>

    <div class="table-section" style="text-align: left; max-width: 600px; padding: 30px;">
         <form action="update_profile_action.php" method="POST" id="editProfileForm">         <!-- js -->
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Full Name</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($user_data['fullName']); ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Phone Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold;">Update Password (Leave blank to keep unchanged)</label>
                <input type="password" name="password" placeholder="Enter new password" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>

            <button type="submit" class="link-back" style="border: none; cursor: pointer; width: 100%; text-align: center; font-size: 15px; font-weight: bold; padding: 14px;">
                Save Changes
            </button>

        </form>
    </div>
</div>


<script>
    // Confirmation popup before saving changes
    document.getElementById("editProfileForm").addEventListener("submit", function(e) {
        const confirmed = confirm("Are you sure you want to save these changes?");
        
        if (!confirmed) {
            e.preventDefault(); // Stop form from submitting if user clicks Cancel
        }
    });
</script>


</body>
</html>