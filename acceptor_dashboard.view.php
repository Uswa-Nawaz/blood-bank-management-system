<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceptor Dashboard - Blood Bank</title>
    <link rel="stylesheet" href="acceptor.css">
</head>
<body>

<div class="sidebar">
    <h2>Acceptor Panel</h2>
    <ul>
        <li><a href="acceptor_dashboard.php" class="active">Dashboard</a></li>
        <li><a href="acceptor_profile.php">My Profile</a></li>
        <li><a href="acceptor_request.php">Request Blood</a></li>
        <li><a href="acceptor_invoices.php">Billing/Invoices</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="topbar">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?></h1>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Blood Group</h3>
            <p class="highlight"><?php echo htmlspecialchars($user_blood_group); ?></p>
        </div>

        <div class="card">
            <h3>Pending Requests</h3>
            <p><?php echo $active_count; ?></p>
        </div>

        <div class="card">
            <h3>Fulfilled Requests</h3>
            <p><?php echo $fulfilled_count; ?></p>
        </div>

        <div class="card">
            <h3>Pending Balance</h3>
            <p class="balance">Rs. <?php echo number_format($unpaid_amount, 2); ?></p>
        </div>
    </div>

    <div class="form-section full-width-form">
        <h2>Submit New Blood Request</h2>
        <form action="process_blood_request.php" method="POST" class="request-grid-form">
            
            <div class="form-group">
                <label for="patient_name">Patient Name</label>
                <input type="text" id="patient_name" name="patient_name" placeholder="Enter patient's full name" required>
            </div>
            <div class="form-group">
                <label for="blood_group">Blood Group Needed</label>
                <select id="blood_group" name="blood_group_id" required>
                    <option value="" disabled selected>Select Blood Type</option>
                    <option value="1">A+</option>
                    <option value="2">A-</option>
                    <option value="3">B+</option>
                    <option value="4">B-</option>
                    <option value="5">AB+</option>
                    <option value="6">AB-</option>
                    <option value="7">O+</option>
                    <option value="8">O-</option>
                </select>
            </div>

            <div class="form-group">
                <label for="bags_needed">Number of Bags (Max 5 Bags)</label>
                <input type="number" id="bags_needed" name="units_requested" min="1" max="5" placeholder="e.g. 2" required>
            </div>
            <div class="form-group">
                <label for="hospital_name">Hospital Name / Location</label>
                <input type="text" id="hospital_name" name="hospital_name" placeholder="e.g. Mayo Hospital, Lahore" required>
            </div>

            <div class="form-group">
                <label for="urgency_level">Urgency Level</label>
                <select id="urgency_level" name="is_urgent" required>
                    <option value="0">Normal (Routine Supply)</option>
                    <option value="1">Critical / Urgent Emergency</option>
                </select>
            </div>
            <div class="form-group btn-container">
                <button type="submit" name="submit_request_btn" class="submit-btn">Submit Blood Request</button>
            </div>

        </form>
    </div>

<div class="table-section margin-top-box">
        <h2>Recent Blood Requests</h2>
        <table>
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Patient Name</th>
                    <th>Hospital / Location</th>
                    <th>Units</th>
                    <th>Urgency</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($history_result && mysqli_num_rows($history_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($history_result)): ?>
                        <tr>
                            <td>REQ-<?php echo $row['request_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['patient_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                            <td><?php echo $row['units_requested']; ?> Bags</td>
                            <td>
                                <span class="badge <?php echo ($row['is_urgent'] == 1) ? 'urgent' : 'normal'; ?>">
                                    <?php echo ($row['is_urgent'] == 1) ? 'URGENT' : 'Normal'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-lbl <?php echo strtolower($row['status']); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-records">No recent requests found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>