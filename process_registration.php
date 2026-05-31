<?php
// Establish connection to database
include 'db_connect.php';

if (isset($_POST['register_submit'])) {
    
    // Check if passwords match before anything else
    $plain_password = $_POST['password'];
$confirmPass = $_POST['confirm_password'];

    if ($plain_password !== $confirmPass) {
        echo "<script>alert('Error: Passwords do not match! Please try again.'); window.history.back();</script>";
        exit();
    }
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
    // Capture remaining variables safely if password check passes
    $name  = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role  = mysqli_real_escape_string($conn, $_POST['role']); 

    
    $planID  = 1; // Default for individual accounts

    if ($role === 'Hospital') {
        if (isset($_POST['plan_id'])) {
            $planID = intval($_POST['plan_id']);
        } else {
            $planID = 2; 
        }
        
        $stmt = mysqli_prepare($conn, "INSERT INTO users 
(fullName, password, phone, role, bloodTypeId, isApproved, plan_id) 
VALUES (?, ?, ?, ?, NULL, 0, ?)");

mysqli_stmt_bind_param($stmt, "ssssi", 
$name, 
$hashed_password, 
$phone, 
$role, 
$planID
);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>
            alert('Registration Successful! Your account is pending Admin approval.');
            window.location.href='login.html';
          </script>";
    exit();
} else {
    echo mysqli_error($conn);
}
                
        

    } else {
        $bloodGrp = mysqli_real_escape_string($conn, $_POST['blood_group']);

        $stmt = mysqli_prepare($conn, "SELECT bloodTypeId FROM bloodtypes WHERE bloodGroup = ?");
mysqli_stmt_bind_param($stmt, "s", $bloodGrp);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $bloodID = intval($row['bloodTypeId']);

            $stmt = mysqli_prepare($conn, "INSERT INTO users 
(fullName, password, phone, role, bloodTypeId, isApproved, plan_id) 
VALUES (?, ?, ?, ?, ?, 0, ?)");

mysqli_stmt_bind_param($stmt, "ssssii", 
$name, 
$hashed_password, 
$phone, 
$role, 
$bloodID,
$planID
);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>
            alert('Registration Successful! Your account is pending Admin approval.');
            window.location.href='login.html';
          </script>";
    exit();
} else {
    echo mysqli_error($conn);
}
                    
            
        } else {
            echo "<script>alert('Error: Invalid blood group selection.'); window.history.back();</script>";
            exit();
        }
    }
}


?>