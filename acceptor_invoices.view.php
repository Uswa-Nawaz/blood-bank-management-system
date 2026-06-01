<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing & Invoices - Acceptor Panel</title>
    <link rel="stylesheet" href="acceptor.css">
</head>
<body>

<div class="sidebar">
    <h2>Acceptor Panel</h2>
    <ul>
        <li><a href="acceptor_dashboard.php">Dashboard</a></li>
        <li><a href="acceptor_profile.php">My Profile</a></li>
        <li><a href="acceptor_invoices.php" class="active">Billing/Invoices</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="topbar">
        <h1>Billing Statements & Invoices</h1>
    </div>

    <div class="table-section margin-top-box">
        <h2>Historical Billing Statements</h2>
        <table>
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Date Generated</th>
                    <th>Type</th>
                    <th>Patient Detail</th>
                    <th>Units</th>
                    <th>Processing Fee</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="invoicesBody">     <!-- js -->
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><strong>INV-<?php echo $row['invoice_id']; ?></strong></td>
                            <td><?php echo date("d-M-Y", strtotime($row['invoice_date'])); ?></td>
                            <td><span class="type-badge"><?php echo htmlspecialchars($row['invoice_type']); ?></span></td>
                            <td><strong><?php echo htmlspecialchars($row['patient_name'] ?? 'N/A'); ?></strong></td>
                            <td><?php echo intval($row['units_requested']); ?> Bags</td>
                            <td>Rs. <?php echo number_format($row['processing_fee'] ?? 0, 2); ?></td>
                            <td><strong class="text-danger">Rs. <?php echo number_format($row['total_amount'], 2); ?></strong></td>
                            <td>
                                <?php if (strtolower($row['payment_status']) == 'paid'): ?>
                                    <span class="status-lbl paid">Paid</span>
                                <?php else: ?>
                                    <span class="status-lbl unpaid">Unpaid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="no-records" style="text-align: center; padding: 30px;">
                            No billing records or invoices found for your account.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<script>
    // Highlight rows on hover for easier reading
    const rows = document.querySelectorAll("#invoicesBody tr");

    rows.forEach(function(row) {
        row.addEventListener("mouseover", function() {
            row.style.backgroundColor = "#fff0cc";
            row.style.borderLeft = "4px solid #e74c3c";
            row.style.cursor = "pointer";
        });

        row.addEventListener("mouseout", function() {
            row.style.backgroundColor = "";
            row.style.borderLeft = "";
            row.style.cursor = "";
        });
    });
</script>


</body>
</html>