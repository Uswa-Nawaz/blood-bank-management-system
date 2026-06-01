<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connect to your database
$conn = mysqli_connect("localhost", "root", "", "bloodsystem");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Authentication Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Hospital') {
    header("Location: login.html");
    exit();
}

$hospital_id = $_SESSION['user_id'];

// --- 1. CALCULATE TOP METRIC CARDS ---
// Bags This Month (Sum units from fulfilled requests for this hospital during the CURRENT calendar month)
$bags_query = "SELECT SUM(r.units_requested) AS total_bags 
               FROM blood_requests r 
               WHERE r.acceptor_id = ? 
               AND r.status = 'Fulfilled'
               AND MONTH(r.request_date) = MONTH(CURRENT_DATE())
               AND YEAR(r.request_date) = YEAR(CURRENT_DATE())";
$stmt_bags = mysqli_prepare($conn, $bags_query);
mysqli_stmt_bind_param($stmt_bags, "i", $hospital_id);
mysqli_stmt_execute($stmt_bags);
$total_bags_count = mysqli_stmt_get_result($stmt_bags)->fetch_assoc()['total_bags'] ?? 0;

// Total Spent (Sum up total_amount of all PAID invoices for this hospital)
$spent_query = "SELECT SUM(i.total_amount) AS total_spent 
                FROM invoices i
                LEFT JOIN blood_requests r ON i.request_id = r.request_id
                WHERE (i.user_id = ? OR r.acceptor_id = ?) 
                AND i.payment_status = 'Paid'";
$stmt_spent = mysqli_prepare($conn, $spent_query);
mysqli_stmt_bind_param($stmt_spent, "ii", $hospital_id, $hospital_id);
mysqli_stmt_execute($stmt_spent);
$total_money_spent = mysqli_stmt_get_result($stmt_spent)->fetch_assoc()['total_spent'] ?? 0.00;


// --- 2. FETCH THE PROCUREMENT TABLE LEDGER LOGS ---
$ledger_query = "SELECT 
                    i.invoice_id, 
                    i.invoice_date, 
                    i.invoice_type, 
                    i.total_amount, 
                    i.processing_fee, 
                    i.payment_status,
                    r.patient_name, 
                    r.units_requested
                 FROM invoices i
                 LEFT JOIN blood_requests r ON i.request_id = r.request_id
                 WHERE i.user_id = ? 
                    OR r.acceptor_id = ?
                 ORDER BY i.invoice_date DESC";
$stmt_ledger = mysqli_prepare($conn, $ledger_query);
mysqli_stmt_bind_param($stmt_ledger, "ii", $hospital_id, $hospital_id);
mysqli_stmt_execute($stmt_ledger);
$ledger_result = mysqli_stmt_get_result($stmt_ledger);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History – LifeBank Hospital</title>
    <link rel="stylesheet" href="hospital.css">
    <style>
        /* Small helper styles for the new subscription layout */
        .badge-included {
            color: #721c24; 
            background: #f8d7da; 
            padding: 3px 8px; 
            border-radius: 4px; 
            font-size: 11px; 
            font-weight: 600;
        }
        .text-free {
            color: #adb5bd;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">🩸</div>
        <h2>LifeBank Hospital</h2>
        <p>Blood Bank System</p>
    </div>
    <nav>
        <ul>
            <li><a href="hospital_dashboard.php"><span>🏠</span> Dashboard</a></li>
            <li><a href="hospital_blood_inventory.php"><span>🩸</span> Blood Inventory</a></li>
        </ul>
        <div class="nav-section-label">Clinical</div>
        <ul>
            <li><a href="hospital_patient_records.php"><span class="nav-icon">🧑‍⚕️</span> Patient Records</a></li>
            <li><a href="hospital_blood_requests.php"><span class="nav-icon">📋</span> Blood Requests</a></li>
        </ul>
        <div class="nav-section-label">Admin</div>
        <ul>
            <li><a href="hospital_reports.php"><span>📊</span> Reports</a></li>
            <li><a href="hospital_invoices.php" class="active"><span>🧾</span> Invoices</a></li>
        </ul>
    </nav>
    <div class="sidebar-footer"><a href="logout.php">🔒 Logout</a></div>
</div>

<div class="main-content">
    <div class="topbar">
        <div class="topbar-title">Billing Statements & Invoices</div>
        <div class="topbar-right"><div class="topbar-avatar">MA</div></div>
    </div>

    <div class="page-body">
        <div class="cards" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 24px;">
            <div class="card">
                <div class="card-icon">🩸</div>
                <h3>Bags Transfused This Month</h3>
                <div class="card-value" id="totalBagsCount"><?php echo $total_bags_count; ?></div>        <!-- js -->
                <div class="card-sub">Total fulfilled volumes</div>
            </div>
            <div class="card card-gold">
                <div class="card-icon">💰</div>
                <h3>Total Settled Expenses</h3>
                <div class="card-value" id="totalMoneySpent">PKR <?php echo number_format($total_money_spent, 2); ?></div>     <!-- js -->
                <div class="card-sub">Paid historical invoices</div>
            </div>
        </div>

        <div class="table-wrapper">
            <div style="padding:18px 22px; border-bottom:1px solid #eee;">
                <h2 style="font-size:16px; font-weight:600;">Purchase Ledger</h2>
                <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">All procurement records</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Date Generated</th>
                        <th>Type</th>
                        <th>Patient/Detail</th>
                        <th>Units</th>
                        <th>Processing Fee</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($ledger_result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($ledger_result)): ?>
                            <tr>
                                <td><strong>INV-<?php echo $row['invoice_id']; ?></strong></td>
                                <td><?php echo date("d-M-Y", strtotime($row['invoice_date'])); ?></td>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background: #e3f2fd; color: #1976d2;">
                                        <?php echo htmlspecialchars($row['invoice_type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong>
                                        <?php echo htmlspecialchars($row['patient_name'] ?? 'Subscription Overhead'); ?>
                                    </strong>
                                </td>
                                <td><?php echo $row['units_requested'] ? $row['units_requested'] . ' Bags' : '—'; ?></td>
                                <td>Rs. <?php echo number_format($row['processing_fee'] ?? 0, 2); ?></td>
                                
                                <td>
                                    <?php if (floatval($row['total_amount']) == 0.00): ?>
                                        <span class="text-free">Included in Plan</span>
                                    <?php else: ?>
                                        <strong>Rs. <?php echo number_format($row['total_amount'], 2); ?></strong>
                                    <?php endif; ?>
                                </td>
                                
<td>
    <?php 
    // If the amount is 0, it's effectively settled by the plan
    if (floatval($row['total_amount']) == 0.00): ?>
        <span style="color: #1976d2; background: #e3f2fd; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600;">Settled</span>
    <?php elseif (strtolower($row['payment_status']) === 'paid'): ?>
        <span style="color: #2b8a3e; background: #ebfbee; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600;">Paid</span>
    <?php else: ?>
        <span style="color: #fd7e14; background: #fff9db; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600;">Unpaid</span>
    <?php endif; ?>
</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 30px; color: #6c757d;">
                                No billing records or invoices found for your account.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<script>
    // Animated number counter function
    function animateCounter(elementId, target, prefix, suffix) {
        const el = document.getElementById(elementId);
        if (!el || isNaN(target)) return;

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
            el.textContent = prefix + current.toLocaleString() + suffix;
        }, stepTime);
    }

    // Read values from PHP and animate
    const totalBags = parseInt(document.getElementById("totalBagsCount").textContent);
    const totalSpent = parseInt(document.getElementById("totalMoneySpent").textContent.replace(/[^0-9]/g, ""));

    animateCounter("totalBagsCount", totalBags, "", "");
    animateCounter("totalMoneySpent", totalSpent, "PKR ", ".00");
</script>



</body>
</html>