<?php 
require_once 'hospital_patient_records_backend.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records – BloodConnect</title>
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
            <li><a href="hospital_blood_inventory.php"><span class="nav-icon">🩸</span> Blood Inventory</a></li>
        </ul>
        <div class="nav-section-label">Clinical</div>
        <ul>
            <li><a href="hospital_patient_records.php" class="active"><span class="nav-icon">🧑‍⚕️</span> Patient Records</a></li>
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
        <div class="topbar-title">Patient Records</div>
        <div class="topbar-right">
            <span class="topbar-badge">🔴 <?php echo $emergency_cases; ?> Emergency</span>
            <div class="topbar-avatar">
                <?php echo strtoupper(substr(trim($hospital_display_name), 0, 2)); ?>
            </div>
        </div>
    </div>

    <div class="page-body">

        <div class="form-card" style="padding:18px 22px; margin-bottom:20px;">
            <form method="GET" action="hospital_patient_records.php" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; width:100%;">
                
                <input type="text" name="search" value="<?php echo htmlspecialchars($search_term ?? ''); ?>" placeholder="🔍 Search by name or ID..." style="flex:1; min-width:200px; padding:9px 14px; border:1.5px solid var(--border-strong); border-radius:6px; font-family:Outfit,sans-serif; font-size:14px; outline:none;">
                
                <select name="blood_type" style="padding:9px 14px; border:1.5px solid var(--border-strong); border-radius:6px; font-family:Outfit,sans-serif; font-size:14px; outline:none; cursor:pointer;">
                    <option value="">All Blood Groups</option>
                    <?php
                    $bg_options = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                    foreach ($bg_options as $bg) {
                        $selected = (isset($filter_blood) && $filter_blood === $bg) ? 'selected' : '';
                        echo "<option value=\"$bg\" $selected>$bg</option>";
                    }
                    ?>
                </select>
                
                <select name="status" style="padding:9px 14px; border:1.5px solid var(--border-strong); border-radius:6px; font-family:Outfit,sans-serif; font-size:14px; outline:none; cursor:pointer;">
                    <option value="">All Statuses</option>
                    <option value="Critical" <?php if(($filter_status ?? '') === 'Critical') echo 'selected'; ?>>Critical</option>
                    <option value="Stable" <?php if(($filter_status ?? '') === 'Stable') echo 'selected'; ?>>Stable</option>
                    <option value="Pending" <?php if(($filter_status ?? '') === 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Discharged" <?php if(($filter_status ?? '') === 'Discharged') echo 'selected'; ?>>Discharged</option>
                </select>
                
                <button type="submit" class="btn btn-ghost" style="padding:9px 16px;">Filter</button>
                
                <?php if (!empty($search_term) || !empty($filter_blood) || !empty($filter_status)): ?>
                    <a href="hospital_patient_records.php" class="btn btn-link" style="font-size:13px; color:var(--text-muted); text-decoration:none;">Clear Filters</a>
                <?php endif; ?>
                
                <a href="hospital_blood_requests.php" class="btn btn-primary" style="margin-left:auto; text-decoration:none;">+ Add Patient Request</a>
            </form>
        </div>

        <div class="table-wrapper">
            <div style="padding:18px 22px; border-bottom:1px solid var(--border);">
                <h2 style="font-size:16px; font-weight:600;">All Logged Patients</h2>
                <p style="font-size:13px; color:var(--text-muted); margin-top:2px;">Showing <?php echo $total_records; ?> case logs matching active metrics</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Name</th>
                        <th>Blood Group</th>
                        <th>Request Date</th>
                        <th>Bags Used/Requested</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_records === 0): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:40px 20px; color:var(--text-muted);">
                                No patient cases recorded matching this criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        while ($row = mysqli_fetch_assoc($patients_result)): 
                            $pt_id = "PT-" . str_pad($row['request_id'], 4, '0', STR_PAD_LEFT);
                            
                            if ($row['status'] === 'Pending' && $row['is_urgent'] == 1) {
                                $badge_class = 'badge-critical';
                                $status_text = 'Critical';
                            } elseif ($row['status'] === 'Pending') {
                                $badge_class = 'badge-pending';
                                $status_text = 'Pending';
                            } elseif ($row['status'] === 'Rejected') {
                                $badge_class = 'badge-discharged';
                                $status_text = 'Discharged';
                            } else {
                                $badge_class = 'badge-stable';
                                $status_text = 'Stable';
                            }
                        ?>
                        <tr>
                            <td class="text-sm text-muted"><?php echo $pt_id; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['patient_name']); ?></strong></td>
                            <td><span class="blood-chip"><?php echo htmlspecialchars($row['bloodGroup']); ?></span></td>
                            <td><?php echo date('d M Y', strtotime($row['request_date'])); ?></td>
                            <td><strong><?php echo (int)$row['units_requested']; ?> Bags</strong></td>
                            <td><span class="badge <?php echo $badge_class; ?>"><?php echo $status_text; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
</body>
</html>