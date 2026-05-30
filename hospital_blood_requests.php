<?php 
require_once 'hospital_patient_records_backend.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Requests – LifeBank Hospital</title>
    <link rel="stylesheet" href="hospital.css">
    <style>
        /* Tab Switcher Styling */
        .tab-container {
            display: flex;
            background-color: #f1f3f5;
            padding: 4px;
            border-radius: 8px;
            margin-bottom: 24px;
            width: fit-content;
        }
        .tab-btn {
            padding: 8px 18px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            background: none;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-family: Outfit, sans-serif;
            color: var(--text-muted, #6c757d);
        }
        .tab-btn.active {
            background-color: #fff;
            color: #8b0000; /* Matching your theme color */
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        /* Grid Layout for Form */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }
        .form-control {
            padding: 10px 14px;
            border: 1.5px solid var(--border-strong, #ccc);
            border-radius: 6px;
            font-family: Outfit, sans-serif;
            font-size: 14px;
            outline: none;
            width: 100%;
            box-sizing: border-box;
        }
        .form-control:focus {
            border-color: #8b0000;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">🩸</div>
        <h2>LifeBank</h2>
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
            <li><a href="hospital_patient_records.php"><span class="nav-icon">🧑‍⚕️</span> Patient Records</a></li>
            <li><a href="hospital_blood_requests.php" class="active"><span class="nav-icon">📋</span> Blood Requests</a></li>
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
        <div class="topbar-title">Blood Requests</div>
        <div class="topbar-right">
            <span class="topbar-badge">🔴 <?php echo $emergency_cases; ?> Emergency</span>
            <div class="topbar-avatar">
                <?php echo strtoupper(substr(trim($hospital_display_name), 0, 2)); ?>
            </div>
        </div>
    </div>

    <div class="page-body" style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 24px; align-items: start;">
        
        <div class="card" style="background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
            <div style="margin-bottom: 20px;">
                <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 4px;">Submit New Blood Request</h2>
                <p style="font-size: 13px; color: var(--text-muted, #6c757d);">Create an immediate clinical request or restock inventory storage.</p>
            </div>

            <div class="tab-container">
                <button type="button" class="tab-btn active" onclick="switchTab('patient')">Patient Specific</button>
                <button type="button" class="tab-btn" onclick="switchTab('bulk')">Bulk Procurement</button>
            </div>

            <form method="POST" action="hospital_blood_requests_backend.php">
                <input type="hidden" name="request_type" id="request_type" value="patient">

                <div class="form-grid">
                    <div class="form-group" id="patientNameGroup">
                        <label>Patient Full Name</label>
                        <input type="text" name="patient_name" class="form-control" placeholder="e.g. Ahmed Ali" required>
                    </div>

                    <div class="form-group">
                        <label>Blood Group Required</label>
                        <select name="blood_type" class="form-control" style="cursor:pointer;" required>
                            <option value="" disabled selected>— Select Blood Group —</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label id="quantityLabel">Number of Bags (Max 5 Bags)</label>
                        <input type="number" name="units_requested" class="form-control" placeholder="e.g. 2" min="1" required>
                    </div>

                    <div class="form-group" id="urgencyGroup">
                        <label>Urgency Level</label>
                        <select name="urgency" class="form-control" style="cursor:pointer;">
                            <option value="0">Routine Supply (Normal)</option>
                            <option value="1">Emergency Dispatch (Urgent)</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; border-top: 1px solid #eee; padding-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="background: #8b0000; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 500; cursor: pointer;">Submit Request</button>
                    <button type="reset" class="btn btn-ghost" style="padding: 10px 20px; border-radius: 6px; cursor: pointer; background: #f8f9fa; border: 1px solid #ddd;">Clear</button>
                </div>
            </form>
        </div>

        <div class="card" style="background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
            <div style="margin-bottom: 20px;">
                <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 4px;">Request Queue</h2>
                <p style="font-size: 13px; color: var(--text-muted, #6c757d);">Recent submissions awaiting review or completion.</p>
            </div>
            
            <?php if (mysqli_num_rows($patients_result) > 0): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                        <thead>
                            <tr style="border-bottom: 2px solid #eee; color: #333; font-weight: 600;">
                                <th style="padding: 10px 8px;">Patient/Type</th>
                                <th style="padding: 10px 8px; text-align: center;">Blood</th>
                                <th style="padding: 10px 8px; text-align: center;">Bags</th>
                                <th style="padding: 10px 8px; text-align: right;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($patients_result)): ?>
                                <tr style="border-bottom: 1px solid #f1f3f5;">
                                    <td style="padding: 12px 8px;">
                                        <div style="font-weight: 500; color: #333;">
                                            <?php echo htmlspecialchars($row['patient_name']); ?>
                                        </div>
                                        <div style="font-size: 11px; color: #999;">
                                            <?php echo date('d M, h:i A', strtotime($row['request_date'])); ?>
                                        </div>
                                    </td>
                                    <td style="padding: 12px 8px; text-align: center;">
                                        <span style="background: #fff5f5; color: #e60000; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 12px; border: 1px solid #ffe3e3;">
                                            <?php echo htmlspecialchars($row['bloodGroup']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px 8px; text-align: center; font-weight: 500; color: #555;">
                                        <?php echo htmlspecialchars($row['units_requested']); ?>
                                    </td>
                                    <td style="padding: 12px 8px; text-align: right;">
                                        <?php 
                                        $status = $row['status'];
                                        $badge_color = '#6c757d';
                                        $bg_color = '#f8f9fa';
                                        
                                        if ($status === 'Pending') { $badge_color = '#fd7e14'; $bg_color = '#fff9db'; }
                                        elseif ($status === 'Approved' || $status === 'Fulfilled') { $badge_color = '#2b8a3e'; $bg_color = '#ebfbee'; }
                                        elseif ($status === 'Rejected') { $badge_color = '#c92a2a'; $bg_color = '#fff5f5'; }
                                        ?>
                                        <span style="color: <?php echo $badge_color; ?>; background: <?php echo $bg_color; ?>; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block;">
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="border: 1px dashed #ddd; border-radius: 8px; padding: 24px; text-align: center; color: #999; font-size: 14px;">
                    No requests logged yet for this hospital.
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
function switchTab(type) {
    const tabs = document.querySelectorAll('.tab-btn');
    const patientNameGroup = document.getElementById('patientNameGroup');
    const urgencyGroup = document.getElementById('urgencyGroup');
    const quantityLabel = document.getElementById('quantityLabel');
    const requestTypeInput = document.getElementById('request_type');
    const nameInput = patientNameGroup.querySelector('input');

    // Update active tab styling class
    tabs.forEach(tab => tab.classList.remove('active'));
    if (type === 'patient') {
        tabs[0].classList.add('active');
        
        // Show fields
        patientNameGroup.style.display = 'flex';
        urgencyGroup.style.display = 'flex';
        quantityLabel.textContent = 'Number of Bags (Max 5 Bags)';
        nameInput.required = true;
        requestTypeInput.value = 'patient';
    } else {
        tabs[1].classList.add('active');
        
        // Hide irrelevant elements for bulk inventory
        patientNameGroup.style.display = 'none';
        urgencyGroup.style.display = 'none';
        quantityLabel.textContent = 'Number of Bags (Bulk Order)';
        nameInput.required = false;
        requestTypeInput.value = 'bulk';
    }
}
</script>

</body>
</html>