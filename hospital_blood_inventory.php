<?php 
require_once 'hospital_blood_inventory_backend.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Inventory – BloodConnect</title>
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
            <li><a href="hospital_dashboard.php"><span class="nav-icon">🏠</span> Dashboard</a></li>
            <li><a href="hospital_blood_inventory.php" class="active"><span class="nav-icon">🩸</span> Blood Inventory</a></li>
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
        </ul>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php"><span>🔒</span> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <div class="topbar-title">Blood Inventory</div>
        <div class="topbar-right">
            <span class="topbar-badge">🔴 <?php echo $emergency_cases; ?> Emergency</span>
            <div class="topbar-avatar">
                <?php echo strtoupper(substr(trim($hospital_display_name), 0, 2)); ?>
            </div>
        </div>
    </div>

    <div class="page-body">

        <?php if (!empty($critical_messages)): ?>
            <div class="alert alert-danger">
                ⚠️ <div><strong><?php echo implode(" and ", $critical_messages); ?> are critically low.</strong> Initiate emergency procurement immediately.</div>
            </div>
        <?php endif; ?>

        <div class="cards" style="grid-template-columns:repeat(3,1fr); margin-bottom:24px;">
            <div class="card">
                <div class="card-icon">🩸</div>
                <h3>Total Bags</h3>
                <div class="card-value"><?php echo $total_combined_bags; ?></div>
                <div class="card-sub">All blood types combined</div>
            </div>
            <div class="card card-gold">
                <div class="card-icon">⚠️</div>
                <h3>Low Stock Groups</h3>
                <div class="card-value"><?php echo $low_stock_groups_count; ?></div>
                <div class="card-sub">Below minimum threshold</div>
            </div>
            <div class="card card-teal">
                <div class="card-icon">📅</div>
                <h3>Expiring Soon</h3>
                <div class="card-value">--</div>
                <div class="card-sub">Monitored by central bank</div>
            </div>
        </div>

        <div class="table-wrapper">
            <div style="padding:20px 22px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <h2 style="font-size:16px; font-weight:600;">Inventory by Blood Group</h2>
                    <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Live stock levels · Updated just now</p>
                </div>
                <div style="display:flex;gap:10px;">
                    <button class="btn btn-ghost btn-sm">⬇ Export</button>
                    <a href="hospital_blood_requests.php" class="btn btn-primary btn-sm">+ Request Delivery</a>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Blood Group</th>
                        <th>Available Bags</th>
                        <th>Min. Required</th>
                        <th>Stock Level</th>
                        <th>Expiry (nearest)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($inventory as $id => $data): 
                        // Compute dynamic rendering logic metrics
                        $count = $data['count'];
                        $min = $data['min'];
                        
                        if ($count == 0) {
                            $pct = 3;
                            $bar_class = 'fill-low';
                            $badge_class = 'badge-critical';
                            $status_text = 'Critical';
                        } elseif ($count < $min) {
                            $pct = round(($count / $min) * 100);
                            if ($pct > 100) $pct = 100;
                            
                            $bar_class = ($count <= ($min * 0.4)) ? 'fill-low' : 'fill-moderate';
                            $badge_class = ($count <= ($min * 0.4)) ? 'badge-critical' : 'badge-pending';
                            $status_text = ($count <= ($min * 0.4)) ? 'Critical' : 'Moderate';
                        } else {
                            $pct = 100;
                            $bar_class = 'fill-good';
                            $badge_class = 'badge-stable';
                            $status_text = 'Adequate';
                        }
                    ?>
                    <tr>
                        <td><span class="blood-chip"><?php echo htmlspecialchars($data['name']); ?></span> &nbsp; <?php echo htmlspecialchars($data['long']); ?></td>
                        <td><strong><?php echo $count; ?></strong></td>
                        <td><?php echo $min; ?></td>
                        <td>
                            <div class="stock-bar-wrap">
                                <div class="stock-bar" style="background:#eef2f5; border-radius:4px; height:8px; width:100%; overflow:hidden;">
                                    <div class="stock-bar-fill <?php echo $bar_class; ?>" style="width:<?php echo $pct; ?>%; height:100%;"></div>
                                </div>
                                <span class="stock-bar-label"><?php echo $count; ?></span>
                            </div>
                        </td>
                        <td>--</td>
                        <td><span class="badge <?php echo $badge_class; ?>"><?php echo $status_text; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
</body>
</html>