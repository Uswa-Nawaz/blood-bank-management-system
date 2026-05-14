<?php
session_start();
include 'db_connect.php';

/**
 * 1. SECURITY GATE
 */
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Donor') {
    header("Location: login.html");
    exit();
}

// Use the session ID from the login process
$current_user_id = $_SESSION['user_id']; 

/** 
 * 2. BUSINESS LOGIC (Fetching Donor Specifics)
 */

// Fetch User Name and Blood Group
$user_query = "SELECT u.fullName, b.bloodGroup 
               FROM users u 
               LEFT JOIN bloodtypes b ON u.bloodTypeId = b.bloodTypeId 
               WHERE u.userId = $current_user_id";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Count Total Completed Donations
$count_query = "SELECT COUNT(*) as total FROM donationrequests 
                WHERE donorId = $current_user_id AND status = 'Completed'";
$count_result = mysqli_query($conn, $count_query);
$total_donations = mysqli_fetch_assoc($count_result)['total'];

// Find Next Eligible Date (Logic: Last donation date + 3 months)
$date_query = "SELECT MAX(requestedDate) as last_date FROM donationrequests 
               WHERE donorId = $current_user_id AND status = 'Completed'";
$date_result = mysqli_query($conn, $date_query);
$last_donation = mysqli_fetch_assoc($date_result)['last_date'];

$next_eligible = $last_donation ? date('Y-m-d', strtotime($last_donation . ' + 3 months')) : 'Eligible Now';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Donor Portal</h2>
        <a href="#">Request Donation</a>
        <a href="#">Donation History</a>
        <a href="#">Profile Settings</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
        <!-- Dynamic Welcome Message -->
        <h1>Welcome, <?php echo $user_data['fullName']; ?></h1>
        
        <div class="card-container">
            <div class="card">
                <h3>Blood Type</h3>
                <p><?php echo $user_data['bloodGroup'] ?? 'Not Assigned'; ?></p>
            </div>
            <div class="card">
                <h3>Total Donations</h3>
                <p><?php echo $total_donations; ?> Times</p>
            </div>
            <div class="card">
                <h3>Next Eligible Date</h3>
                <p><?php echo $next_eligible; ?></p>
            </div>
        </div>

        <h3>Your Donation History</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Blood Type</th>
                    <th>Status</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetching history from donationrequests table
                $history_sql = "SELECT d.requestedDate, b.bloodGroup, d.status, d.actualQuantityCollected 
                                FROM donationrequests d
                                JOIN bloodtypes b ON d.bloodTypeId = b.bloodTypeId
                                WHERE d.donorId = $current_user_id
                                ORDER BY d.requestedDate DESC";
                
                $history_result = mysqli_query($conn, $history_sql);

                if (mysqli_num_rows($history_result) > 0) {
                    while($row = mysqli_fetch_assoc($history_result)) {
                        echo "<tr>";
                        echo "<td>" . $row['requestedDate'] . "</td>";
                        echo "<td>" . $row['bloodGroup'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>" . number_format($row['actualQuantityCollected'], 2) . " ml</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center;'>No history found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>