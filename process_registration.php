<?php
// 1. Establish connection to your database
include 'db_connect.php';

if (isset($_POST['register_submit'])) {
    
    // 2. Capture the data from your new register.html
    $name     = $_POST['fullname'];
    $pass     = $_POST['password'];
    $phone    = $_POST['phone'];
    $role     = $_POST['role'];       // Either 'Donor' or 'Acceptor'
    $bloodGrp = $_POST['blood_group']; // The string like 'A+', 'B+', etc.

    // 3. Logic Gate: Map the Blood Group string to the ID in your database
    // This follows your preference for logic-first problem solving
    $type_query = "SELECT bloodTypeId FROM bloodtypes WHERE bloodGroup = '$bloodGrp'";
    $result = mysqli_query($conn, $type_query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $bloodID = $row['bloodTypeId'];

        // 4. Insert into the 'users' table
        // isApproved defaults to 0 so the Admin must verify them later
        $sql = "INSERT INTO users (fullName, password, phone, role, bloodTypeId, isApproved) 
                VALUES ('$name', '$pass', '$phone', '$role', '$bloodID', 0)";

        if (mysqli_query($conn, $sql)) {
            // Instead of echoing HTML here, we use a Javascript alert and redirect
            // This keeps this file as pure "Backend Logic"
            echo "<script>
                    alert('Registration Successful! Your account is pending Admin approval.');
                    window.location.href='login.html';
                  </script>";
            exit();
        } else {
            echo "Database Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Error: Invalid blood group selected.'); window.history.back();</script>";
        exit();
    }
}
?>