<?php
// Establish connection to database
include 'db_connect.php';

if (isset($_POST['register_submit'])) {
    
    // Check if passwords match before anything else
    $pass  = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPass = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if ($pass !== $confirmPass) {
        echo "<script>alert('Error: Passwords do not match! Please try again.'); window.history.back();</script>";
        exit();
    }
    
    // Capture remaining variables safely if password check passes
    $name  = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role  = mysqli_real_escape_string($conn, $_POST['role']); 

    $bloodID = "NULL";
    $planID  = 1; // Default for individual accounts

    if ($role === 'Hospital') {
        if (isset($_POST['plan_id'])) {
            $planID = intval($_POST['plan_id']);
        } else {
            $planID = 2; 
        }
        
        $sql = "INSERT INTO users (fullName, password, phone, role, bloodTypeId, isApproved, plan_id) 
                VALUES ('$name', '$pass', '$phone', '$role', NULL, 0, $planID)";
                
        executeRegistrationQuery($conn, $sql);

    } else {
        $bloodGrp = mysqli_real_escape_string($conn, $_POST['blood_group']);

        $type_query = "SELECT bloodTypeId FROM bloodtypes WHERE bloodGroup = '$bloodGrp'";
        $result = mysqli_query($conn, $type_query);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $bloodID = intval($row['bloodTypeId']);

            $sql = "INSERT INTO users (fullName, password, phone, role, bloodTypeId, isApproved, plan_id) 
                    VALUES ('$name', '$pass', '$phone', '$role', $bloodID, 0, $planID)";
                    
            executeRegistrationQuery($conn, $sql);
        } else {
            echo "<script>alert('Error: Invalid blood group selection.'); window.history.back();</script>";
            exit();
        }
    }
}

function executeRegistrationQuery($connectionMatrix, $queryExecutionString) {
    if (mysqli_query($connectionMatrix, $queryExecutionString)) {
        echo "<script>
                alert('Registration Successful! Your account is pending Admin approval.');
                window.location.href='login.html';
              </script>";
        exit();
    } else {
        echo "Database Transaction Error: " . mysqli_error($connectionMatrix);
    }
}
?>