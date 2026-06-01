<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Blood Unit - Admin</title>
    <link rel="stylesheet" href="donor.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Overview</a>
        <a href="manage_inventory.php" class="active">Inventory</a>
        <a href="pending_approvals.php">User Approvals (<?php echo $pending_count; ?>)</a>
        <a href="manage_subscriptions.php">Manage Subscriptions</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_requests.php">Blood Requests</a>
        <a href="manage_donations.php" class="<?php echo ($current_page == 'manage_donations') ? 'active' : ''; ?>">Donation Requests</a>
        <a href="view_invoices.php">Invoices</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="content-header">
            <h1>Edit Blood Unit #<?php echo $id; ?></h1>
            <a href="manage_inventory.php" class="link-back">← Back to Stock</a>
        </div>
        
        <div class="form-container">
            <form action="edit_inventory_action.php" method="POST">
                <!-- Hidden input to pass the ID to the action script -->
                <input type="hidden" name="inventory_id" value="<?php echo $data['inventory_id']; ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Blood Group</label>
                        <select name="blood_type_id" required class="form-control">
                            <?php while($type = mysqli_fetch_assoc($blood_types)): ?>
                                <option value="<?php echo $type['bloodTypeId']; ?>" 
                                    <?php echo ($type['bloodTypeId'] == $data['blood_type_id']) ? 'selected' : ''; ?>>
                                    <?php echo $type['bloodGroup']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" required class="form-control">
                            <option value="Available" <?php echo ($data['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                            <option value="Reserved" <?php echo ($data['status'] == 'Reserved') ? 'selected' : ''; ?>>Reserved</option>
                            <option value="Expired" <?php echo ($data['status'] == 'Expired') ? 'selected' : ''; ?>>Expired</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Quantity (ml)</label>
                    <input type="number" name="quantity_ml" value="<?php echo $data['quantity_ml']; ?>" min="100" max="1000" required class="form-control" id="quantity">
                    
<!-- js warning message -->
<p id="qtyWarning" style="color:red; font-size:13px; margin-top:5px; display:none;">
    ⚠️ Quantity must be between 100ml and 1000ml.
</p>         <!-- js -->


                </div>

                <div class="form-group">
                    <label>Storage Location</label>
                    <select name="storage_location" required class="form-control">
                        <?php 
                        $locations = ["Main Fridge - Shelf A", "Main Fridge - Shelf B", "Emergency Cooler 1", "Lab Storage 02"];
                        foreach($locations as $loc): ?>
                            <option value="<?php echo $loc; ?>" <?php echo ($data['storage_location'] == $loc) ? 'selected' : ''; ?>>
                                <?php echo $loc; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" name="submit_edit" class="btn-update">
                    Update Inventory Record
                </button>
            </form>
        </div>
    </div>



<script>
    // Warn if quantity is out of range
    const quantityInput = document.getElementById("quantity");
    const qtyWarning = document.getElementById("qtyWarning");

    quantityInput.addEventListener("input", function() {
        const val = parseInt(quantityInput.value);

        if (val < 100 || val > 1000 || isNaN(val)) {
            // Show warning
            qtyWarning.style.display = "block";
            quantityInput.style.borderColor = "red";
        } else {
            // Hide warning
            qtyWarning.style.display = "none";
            quantityInput.style.borderColor = "";
        }
    });
</script>



</body>
</html>