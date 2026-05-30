<?php
session_start(); 
include 'db_connect.php';

if (isset($_POST['login_submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['fullname']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $sql = "SELECT * FROM users WHERE fullName = '$name' AND password = '$pass' AND role = '$role'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Security check: Block unapproved non-admin users
        if ($user['role'] !== 'Admin' && $user['isApproved'] == 0) {
            echo "<script>alert('Your account is pending Admin approval!'); window.location.href='login.html';</script>";
            exit();
        }

        // Initialize Session Variables
        $_SESSION['user_id'] = $user['userId'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['fullName'];

        // Role-Based Routing Matrix
        if ($role == 'Donor') {
            header("Location: donor_dashboard.php");
        } 
        elseif ($role == 'Acceptor') {
            header("Location: acceptor_dashboard.php");
        } 
        elseif ($role == 'Hospital') {
            header("Location: hospital_dashboard.php"); 
        }
        elseif ($role == 'Admin') {
            header("Location: admin_dashboard.php");
        } 
        else {
            echo "Role not recognized.";
        }
        exit();    
    } 
    else {
        echo "<script>alert('Invalid Credentials or Wrong Role Selected!'); window.location.href='login.html';</script>";
    }
}
?>