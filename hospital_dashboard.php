<?php 
// Connect to the backend processor logic file
require_once 'hospital_dashboard_backend.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – BloodConnect</title>
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
        <div class="nav-section-label"></div>
        <ul>
            <li><a href="hospital_dashboard.php" class="active"><span class="nav-icon">🏠</span> Dashboard</a></li>
            <li><a href="hospital_blood_inventory.php"><span class="nav-icon">🩸</span> Blood Inventory</a></li>
            </ul>
                <div class="nav-section-label">Clinical</div>
            <ul>
            <li><a href="hospital_patient_records.php"><span class="nav-icon">🧑‍⚕️</span> Patient Records</a></li>
            <li><a href="hospital_blood_requests.php"><span class="nav-icon">📋</span> Blood Requests</a></li>
            </ul>
                <div class="nav-section-label">Admin</div>
            <ul>
            <li><a href="hospital_reports.php"><span class="nav-icon">📊</span> Reports</a></li>
            <li><a href="hospital_invoices.php"><span class="nav-icon">🧾</span> Invoices</a></li>
            <li><a href="hospital_settings.php"><span class="nav-icon">⚙️</span> Settings</a></li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php"><span>🔒</span> Logout</a>
    </div>
</div>

<div class="main-content">
<div class="topbar">
    <div>
        <div class="topbar-title">
            Welcome, <span style="color: var(--primary); font-weight: 600;"><?php echo htmlspecialchars($hospital_display_name); ?></span>
        </div>
    </div>
    <div class="topbar-right">
        <div class="topbar-date" id="liveDate"></div>
        <span class="topbar-badge">🔴 <?php echo $emergency_cases; ?> Emergency</span>
        <div class="topbar-avatar">
            <?php echo strtoupper(substr(trim($hospital_display_name), 0, 2)); ?>
        </div>
    </div>
</div>

    <div class="page-body">

        <?php if (!empty($critical_warnings)): ?>
            <div class="alert alert-danger">
                ⚠️ <div><strong>Critical Stock Warning:</strong> <?php echo implode(" ", $critical_warnings); ?> Immediate replenishment recommended.</div>
            </div>
        <?php endif; ?>

        <div class="cards">
            <div class="card">
                <div class="card-icon">🩸</div>
                <h3>Total Blood Bags</h3>
                <div class="card-value"><?php echo $total_bags; ?></div>
                <div class="card-sub">In local facility fridges</div>
            </div>
            <div class="card card-teal">
                <div class="card-icon">🧑‍⚕️</div>
                <h3>Active Patients</h3>
                <div class="card-value"><?php echo $active_patients; ?></div>
                <div class="card-sub">With active orders</div>
            </div>
            <div class="card card-gold">
                <div class="card-icon">📋</div>
                <h3>Pending Requests</h3>
                <div class="card-value"><?php echo $pending_requests; ?></div>
                <div class="card-sub">Awaiting bank approval</div>
            </div>
            <div class="card card-blue">
                <div class="card-icon">🚨</div>
                <h3>Emergency Cases</h3>
                <div class="card-value"><?php echo $emergency_cases; ?></div>
                <div class="card-sub">Require immediate dispatch</div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-top:4px;">

            <div class="table-wrapper">
                <div style="padding:20px 22px; border-bottom:1px solid var(--border);">
                    <div class="section-header" style="margin-bottom:0;">
                        <div>
                            <h2>Blood Stock Overview</h2>
                            <p>Current inventory levels by group</p>
                        </div>
                        <a href="hospital_blood_inventory.php" class="btn btn-ghost btn-sm">View All</a>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Bags</th>
                            <th>Level Bar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($stock_levels as $id => $data): ?>
                        <tr>
                            <td><span class="blood-chip"><?php echo htmlspecialchars($data['name']); ?></span></td>
                            <td><strong><?php echo $data['count']; ?> Units</strong></td>
                            <td>
                                <div class="progress-bar" style="background:#eef2f5; border-radius:4px; height:8px; width:100%; overflow:hidden;">
                                    <div class="progress-fill <?php echo $data['class']; ?>" style="width: <?php echo $data['pct']; ?>%; height:100%;"></div>
                                </div>
                            </td>
                            <td><span class="badge <?php echo $data['badge']; ?>"><?php echo htmlspecialchars($data['txt']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-wrapper">
                <div style="padding:20px 22px; border-bottom:1px solid var(--border);">
                    <div class="section-header" style="margin-bottom:0;">
                        <div>
                            <h2>Recent Blood Requests</h2>
                            <p>Latest submissions</p>
                        </div>
                        <a href="hospital_blood_requests.php" class="btn btn-ghost btn-sm">View All</a>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Group</th>
                            <th>Bags</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($recent_result) > 0): ?>
                            <?php 
                            $typeMap = [1=>'A+', 2=>'A-', 3=>'B+', 4=>'B-', 5=>'O+', 6=>'O-', 7=>'AB+', 8=>'AB-'];
                            
                            while($req = mysqli_fetch_assoc($recent_result)): 
                                $badgeClass = 'badge-pending';
                                if($req['status'] == 'Approved' || $req['status'] == 'Fulfilled') $badgeClass = 'badge-approved';
                                if($req['status'] == 'Partially Fulfilled') $badgeClass = 'badge-moderate';
                                
                                $typeName = $typeMap[$req['blood_type_id']] ?? 'Unknown';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($req['patient_name']); ?></td>
                                <td><span class="blood-chip"><?php echo $typeName; ?></span></td>
                                <td><?php echo htmlspecialchars($req['units_requested']); ?> Bags</td>
                                <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($req['status']); ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:30px;" class="text-muted">No recent requests recorded.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    const d = new Date();
    document.getElementById('liveDate').textContent = d.toLocaleDateString('en-GB',{weekday:'short',day:'numeric',month:'long',year:'numeric'});
</script>
</body>
</html>