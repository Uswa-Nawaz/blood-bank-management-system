<?php
session_start(); 
include 'db_connect.php';

if (isset($_POST['login_submit'])) {
    $name = trim($_POST['fullname']);
$pass = $_POST['password'];
$role = trim($_POST['role']);

    $stmt = mysqli_prepare($conn, 
"SELECT userId, fullName, password, role, isApproved 
FROM users 
WHERE fullName = ? AND role = ?");

mysqli_stmt_bind_param($stmt, "ss", $name, $role);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {

    // Verify hashed password
    if (!password_verify($pass, $user['password'])) {
        echo "<script>alert('Invalid Credentials or Wrong Role Selected!'); window.location.href='login.html';</script>";
        exit();
    }

        // Security check: Block unapproved non-admin users
        if ($user['role'] !== 'Admin' && $user['isApproved'] == 0) {
            echo "<script>alert('Your account is pending Admin approval!'); window.location.href='login.html';</script>";
            exit();
        }

        // Initialize Session Variables
        session_regenerate_id(true);
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
            header("Location: hospital_dashboard.php"); // Safely linked
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