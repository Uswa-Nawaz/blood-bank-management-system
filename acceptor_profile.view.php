<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Acceptor Panel</title>
    <link rel="stylesheet" href="acceptor.css">
</head>
<body>

<div class="sidebar">
    <h2>Acceptor Panel</h2>
    <ul>
        <li><a href="acceptor_dashboard.php">Dashboard</a></li>
        <li><a href="acceptor_profile.php" class="active">My Profile</a></li>
        <li><a href="acceptor_invoices.php">Billing/Invoices</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="main-content">
<div class="topbar">
        <h1>Account Profile Management</h1>
    </div>

    <?php if (isset($_GET['update_status']) && $_GET['update_status'] == 'success'): ?>
        <div class="alert-box success-alert">
            ✓ Your profile details have been successfully updated!
        </div>
    <?php elseif (isset($_GET['update_status']) && $_GET['update_status'] == 'error'): ?>
        <div class="alert-box error-alert">
            ⚠ An error occurred while updating your record. Please try again.
        </div>
    <?php endif; ?>
    <div class="profile-overview-card">
        <h2>Personal Details Overview</h2>
        <div class="overview-grid">
            <div class="overview-item">
                <span class="label-title">Full Name:</span>
                <span class="label-value"><?php echo htmlspecialchars($user_data['fullName']); ?></span>
            </div>
            <div class="overview-item">
                <span class="label-title">Phone Contact:</span>
                <span class="label-value"><?php echo htmlspecialchars($user_data['phone']); ?></span>
            </div>
            <div class="overview-item">
                <span class="label-title">Account Role:</span>
                <span class="label-value badge-role"><?php echo htmlspecialchars($user_data['role']); ?></span>
            </div>
            <div class="overview-item">
                <span class="label-title">Blood Group:</span>
                <span class="label-value text-danger"><strong><?php echo htmlspecialchars($display_blood_group); ?></strong></span>
            </div>
        </div>
    </div>

    <div class="form-section full-width-form">
        <h2>Update Profile Credentials</h2>
        <form action="update_acceptor_profile.php" method="POST" class="request-grid-form">
            
            <div class="form-group">
                <label for="edit_name">Full Name</label>
                <input type="text" id="edit_name" name="updated_name" value="<?php echo htmlspecialchars($user_data['fullName']); ?>" required>
            </div>
            <div class="form-group">
                <label for="edit_phone">Phone Number</label>
                <input type="text" id="edit_phone" name="updated_phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="edit_blood">Blood Group (Contact Admin to Alter)</label>
                <select id="edit_blood" disabled>
                    <option selected><?php echo htmlspecialchars($display_blood_group); ?></option>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_password">New Password (Leave blank to keep current)</label>
                <input type="password" id="edit_password" name="updated_password" placeholder="Enter a secure new password">
            </div>

            <div class="form-group">
                </div>
            <div class="form-group btn-container">
                <button type="submit" name="submit_profile_update_btn" class="submit-btn">Save Changes</button>
            </div>

        </form>
    </div>
</div>

</body>
</html>