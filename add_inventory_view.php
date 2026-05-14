<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Blood Unit - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Overview</a>
        <a href="manage_inventory.php" class="active">Inventory</a>
        <a href="pending_approvals.php">User Approvals (<?php echo $pending_count; ?>)</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_requests.php">Blood Requests</a>
        <a href="view_invoices.php">Invoices</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="content-header">
            <h1>Add New Blood Unit</h1>
            <a href="manage_inventory.php" class="link-back">← Back to Stock</a>
        </div>

        <div class="form-container">
            <form action="add_inventory_action.php" method="POST">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Blood Group</label>
                        <select name="blood_type_id" required class="form-control">
                            <option value="">Select Group</option>
                            <?php while($type = mysqli_fetch_assoc($blood_types)): ?>
                                <option value="<?php echo $type['bloodTypeId']; ?>"><?php echo $type['bloodGroup']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Component Type</label>
                        <select name="component_type" required class="form-control">
                            <option value="Whole Blood">Whole Blood</option>
                            <option value="Plasma">Plasma</option>
                            <option value="Platelets">Platelets</option>
                            <option value="RBC">RBC</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Quantity (ml)</label>
                        <input type="number" name="quantity_ml" min="100" max="1000" required placeholder="e.g. 450" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Storage Location</label>
                        <select name="storage_location" required class="form-control">
                            <option value="Main Fridge - Shelf A">Main Fridge - Shelf A</option>
                            <option value="Main Fridge - Shelf B">Main Fridge - Shelf B</option>
                            <option value="Emergency Cooler 1">Emergency Cooler 1</option>
                            <option value="Lab Storage 02">Lab Storage 02</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" min="<?php echo $today; ?>" required class="form-control">
                </div>

                <div class="form-group">
                    <label>Donor ID (Optional Reference)</label>
                    <input type="text" name="donor_ref" placeholder="Enter Donor User ID" class="form-control">
                </div>

                <button type="submit" name="submit_add" class="btn-submit">
                    Confirm and Add to Inventory
                </button>
            </form>
        </div>
    </div>
</body>
</html>