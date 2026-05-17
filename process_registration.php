<?php
// Establish connection to database
include 'db_connect.php';

//isset(...), This function checks if a variable exists and is not empty.
if (isset($_POST['register_submit']))
    {
    
        // Capture the data from register.html
        $name     = $_POST['fullname'];
        $pass     = $_POST['password'];
        $phone    = $_POST['phone'];
        $role     = $_POST['role'];       // Either 'Donor' or 'Acceptor'
        $bloodGrp = $_POST['blood_group']; // The string like 'A+', 'B+', etc.

        //When the user clicks "Submit," PHP grabs the choice from the HTML and stores it in $bloodGrp variable.
        $type_query = "SELECT bloodTypeId FROM bloodtypes WHERE bloodGroup = '$bloodGrp'";
        //This give the  sqlcommand to $conn and save the result in $result
        $result = mysqli_query($conn, $type_query);
        
        if ($row = mysqli_fetch_assoc($result)) 
        {
            $bloodID = $row['bloodTypeId'];

            // Insert into the 'users' table
            // isApproved defaults to 0 so the Admin must verify them later
            $sql = "INSERT INTO users (fullName, password, phone, role, bloodTypeId, isApproved) 
                    VALUES ('$name', '$pass', '$phone', '$role', '$bloodID', 0)";

            if (mysqli_query($conn, $sql))
            {
                // Instead of echoing HTML here, we use a Javascript alert and redirect
                // This keeps this file as pure "Backend Logic"
                echo "<script>
                        alert('Registration Successful! Your account is pending Admin approval.');
                        window.location.href='login.html';
                    </script>";
                exit();
            }
            else
            {
                echo "Database Error: " . mysqli_error($conn);
            }
        }
        else
        {
            echo "<script>alert('Error: Invalid blood group selected.'); window.history.back();</script>";
            exit();
        }
    }
?>