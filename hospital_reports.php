<?php 
require_once 'hospital_reports_backend.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports – BloodConnect</title>
    <link rel="stylesheet" href="hospital.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">🩸</div>
            <h2>BloodConnect</h2>
            <p style="font-size: 0.85rem; opacity: 0.8; color: #fff;"><?php echo htmlspecialchars($hospital_display_name); ?></p>
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
            <li><a href="hospital_reports.php" class="active"><span>📊</span> Reports</a></li>
            <li><a href="hospital_invoices.php"><span>🧾</span> Invoices</a></li>
            <li><a href="hospital_settings.php"><span>⚙️</span> Settings</a></li>
        </ul>
    </nav>
</div>

<div class="main-content">
    <div class="topbar">
        <div class="topbar-title">Reports Dashboard</div>
        <div class="topbar-right"><div class="topbar-avatar">MA</div></div>
    </div>

    <div class="page-body">

        <div class="cards" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div class="card">
                <div class="card-icon">🩸</div>
                <h3>Bags Requested</h3>
                <div class="card-value"><?php echo $total_requested; ?></div>
                <div class="card-sub">Total overall demands</div>
            </div>
            <div class="card card-teal">
                <div class="card-icon">🧑‍⚕️</div>
                <h3>Patients Monitored</h3>
                <div class="card-value"><?php echo $pending_patients; ?></div>
                <div class="card-sub">Awaiting central approval</div>
            </div>
            <div class="card card-gold">
                <div class="card-icon">💉</div>
                <h3>Transfusions Done</h3>
                <div class="card-value"><?php echo $total_used; ?></div>
                <div class="card-sub">Total units successfully fulfilled</div>
            </div>
        </div>

        <div class="table-wrapper" style="margin-top: 30px;">
            <div style="padding: 18px 22px; border-bottom: 1px solid #eee;">
                <h2 style="font-size: 16px; font-weight: 600;">Hospital Request & Usage Summary</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Blood Group</th>
                        <th>Total Units Requested</th>
                        <th>Total Units Transfused (Used)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report_data as $id => $metrics): ?>
                    <tr>
                        <td><span class="blood-chip"><?php echo htmlspecialchars($metrics['name']); ?></span></td>
                        <td><strong><?php echo $metrics['requested']; ?> Units</strong></td>
                        <td><?php echo $metrics['used']; ?> Units</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>