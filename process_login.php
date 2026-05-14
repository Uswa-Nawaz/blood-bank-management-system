<?php
session_start(); // Starts a session to remember the user
include 'db_connect.php';

if (isset($_POST['login_submit'])) 
    {
        $name = $_POST['fullname'];
        $pass = $_POST['password'];
        $role = $_POST['role'];

        // 1. Check if user exists with matching name, password, and role
        // 2. Also check if isApproved is 1 (Admin has approved them)
        $sql = "SELECT * FROM users WHERE fullName = '$name' AND password = '$pass' AND role = '$role'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) 
        {
            $user = mysqli_fetch_assoc($result);

            // If they are NOT an Admin AND they are NOT approved, stop them.
        if ($user['role'] !== 'Admin' && $user['isApproved'] == 0) {
            echo "<script>alert('Your account is pending Admin approval!'); window.location.href='login.html';</script>";
            exit();
        }

            // Store user data in session so the dashboard knows who is logged in
            $_SESSION['user_id'] = $user['userId'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['fullName'];
            // ------------------------------

                // Check for Admin Approval
            // Redirect based on role
            if ($role == 'Donor')
            {
                header("Location: donor_dashboard.php");
            } 
            elseif ($role == 'Acceptor')
            {
                header("Location: acceptor_dashboard.php");
            } 
            elseif ($role == 'Admin') 
            {
                header("Location: admin_dashboard.php");
            } 
            else
            {
                // Safety net for unexpected roles
                echo "Role not recognized.";
            }
            exit(); // Always use exit after a header redirect     
        } 
        else
        {
            echo "<script>alert('Invalid Credentials or Wrong Role Selected!'); window.location.href='login.html';</script>";
        }
    }
?>