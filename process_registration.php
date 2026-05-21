<?php
// Establish connection to database
include 'db_connect.php';

if (isset($_POST['register_submit'])) {
    
    // Capture basic shared inputs safely
    $name  = mysqli_real_escape_string($conn, $_POST['fullname']);
    $pass  = mysqli_real_escape_string($conn, $_POST['password']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $role  = mysqli_real_escape_string($conn, $_POST['role']); 

    // Initialize relational foreign keys
    $bloodID = "NULL";
    $planID  = 1; // Default to '1' (None) for individual accounts

    // Execution Logic Routing Split
    if ($role === 'Hospital') {
        if (isset($_POST['plan_id'])) {
            $planID = intval($_POST['plan_id']);
        } else {
            $planID = 2; // Default plan fallback protection
        }
        
        $sql = "INSERT INTO users (fullName, password, phone, role, bloodTypeId, isApproved, plan_id) 
                VALUES ('$name', '$pass', '$phone', '$role', NULL, 0, $planID)";
                
        executeRegistrationQuery($conn, $sql);

    } else {
        // Individual Path (Donor or Acceptor processing)
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