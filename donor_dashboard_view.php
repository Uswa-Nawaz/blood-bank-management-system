<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="donor.css">
</head>
<body>

    <div class="sidebar">
        <h2>Donor Panel</h2>
        <a href="donor_dashboard.php" class="active">Dashboard</a>
        <a href="donor_profile.php">My Profile</a>
        <a href="donation_history.php">Donation History</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- this to make sure user know that their request is submitted -->

    <div class="main-content">

    <?php if (isset($_GET['msg'])): ?>
            <div style="width: 100%; margin-bottom: 25px; box-sizing: border-box;">
                <?php if ($_GET['msg'] === 'request_submitted'): ?>
                    <div id="alertBanner" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; border-radius: 6px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; box-sizing: border-box; width: 100%;">
                        <span>🟢 Donation request submitted successfully! It is now pending admin approval.</span>
                        <button onclick="document.getElementById('alertBanner').style.display='none';" style="background: none; border: none; color: #155724; font-size: 20px; cursor: pointer; font-weight: bold; line-height: 1;">&times;</button>
                    </div>
                <?php elseif ($_GET['msg'] === 'cancelled'): ?>
                    <div id="alertBanner" style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 15px; border-radius: 6px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; box-sizing: border-box; width: 100%;">
                        <span>⚠️ Donation request has been successfully cancelled.</span>
                        <button onclick="document.getElementById('alertBanner').style.display='none';" style="background: none; border: none; color: #856404; font-size: 20px; cursor: pointer; font-weight: bold; line-height: 1;">&times;</button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <h1>Welcome, <?php echo htmlspecialchars($user_data['fullName']); ?></h1>

        <div class="card-container">

            <div class="card">
                <h3>Total Donations</h3>
                <p class="stat-number"><?php echo $total_donations; ?></p>
            </div>

            <div class="card">
                <h3>Pending Requests</h3>
                <p class="stat-number"><?php echo $pending_requests; ?></p>
            </div>

            <div class="card">
                <h3>Blood Group</h3>
                <p class="stat-number"><?php echo htmlspecialchars($user_data['bloodGroup'] ?? 'N/A'); ?></p>
            </div>

        </div>

        <div class="form-section" style="background: #fff; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #ddd; text-align: left;">
    <h2 style="margin-top: 0; color: #333;">Schedule a Blood Donation</h2>
    <p style="color: #666; margin-bottom: 20px; font-size: 14px;">Please select your target appointment date and the estimated volume you intend to provide.</p>
    
    <form action="submit_donation_action.php" method="POST">
        
        <?php if (isset($user_data['bloodTypeId'])): ?>
            <input type="hidden" name="blood_type_id" value="<?php echo htmlspecialchars($user_data['bloodTypeId']); ?>">
        <?php else: ?>
            <input type="hidden" name="blood_type_id" value="0">
        <?php endif; ?>

        <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px;">
            
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #444;">Intended Donation Date</label>
                <input type="date" name="appointment_date" required 
                       min="<?php echo date('Y-m-d'); ?>" 
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>

            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #444;">Estimated Target Volume</label>
                <select name="quantity_ml" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background-color: #fff;">
                    <option value="450">1 Standard Unit (~450 ml)</option>
                    <option value="500">Double Unit (~500 ml)</option>
                    <option value="250">Pediatric / Half Unit (~250 ml)</option>
                </select>
            </div>

        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: #555; cursor: pointer;">
                <input type="checkbox" required style="margin-top: 3px;">
                <span>I attest that I am currently feeling healthy, am free of cold or flu symptoms, and meet the basic temporary eligibility guidelines for blood center access.</span>
            </label>
        </div>

        <button type="submit" name="submit_donation" style="background: #bc1c24; color: white; padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 14px;">
            Submit Donation Appointment
        </button>
    </form>
</div>

<div class="table-section" style="text-align: left;">
            <h2>Recent Donation Activity</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th> </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($history_result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($history_result)): ?>
                            <tr>
                                <td><?php echo date('d M, Y', strtotime($row['requestedDate'])); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($row['status']); ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'Pending'): ?>
                                        <a href="cancel_donation_action.php?id=<?php echo $row['donationId']; ?>" 
                                           onclick="return confirm('Are you sure you want to cancel this donation request?');"
                                           style="color: #bc1c24; text-decoration: none; font-weight: bold; font-size: 14px;">
                                           Cancel
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #aaa;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: #888;">No recent donation activity found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

</div>

</body>
</html>