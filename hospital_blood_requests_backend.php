<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connect.php';

// 1. Authentication & Authorization Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Hospital') {
    header("Location: login.html");
    exit();
}

$hospital_id = $_SESSION['user_id'];

// Check if request arrived via standard POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. Extract and Sanitize Base Form Values
    $request_type    = isset($_POST['request_type']) ? trim($_POST['request_type']) : 'patient';
    $blood_group_str = isset($_POST['blood_type']) ? trim($_POST['blood_type']) : '';
    $units_requested = isset($_POST['units_requested']) ? (int)$_POST['units_requested'] : 0;

    // Validate structural requirements
    if (empty($blood_group_str) || $units_requested <= 0) {
        header("Location: hospital_blood_requests.php?error=invalid_data");
        exit();
    }

    // 3. Dynamic ID Translation via 'bloodtypes' Table Lookups
    $type_query = "SELECT bloodTypeId FROM bloodtypes WHERE bloodGroup = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $type_query);
    mysqli_stmt_bind_param($stmt, "s", $blood_group_str);
    mysqli_stmt_execute($stmt);
    $type_result = mysqli_stmt_get_result($stmt);
    $blood_type_row = mysqli_fetch_assoc($type_result);

    if (!$blood_type_row) {
        header("Location: hospital_blood_requests.php?error=unknown_blood_type");
        exit();
    }
    
    $blood_type_id = (int)$blood_type_row['bloodTypeId'];

    // 4. Evaluate Tab Branch Contexts
    if ($request_type === 'bulk') {
        // Enforce safe backend hardcoded overrides for inventory procurement logs
        $final_patient_name = "BULK STOCK";
        $final_is_urgent    = 0; 
    } else {
        // Parse clinical inputs targeted for true patient care paths
        $raw_name = isset($_POST['patient_name']) ? trim($_POST['patient_name']) : '';
        $final_patient_name = ($raw_name !== '') ? $raw_name : "Unknown Patient";
        $final_is_urgent    = isset($_POST['urgency']) && $_POST['urgency'] === '1' ? 1 : 0;
    }

    // Default status state configuration for incoming entries
    $initial_status = "Pending"; 
    $current_date   = date('Y-m-d H:i:s');

    // 5. Build and Bind Secured Insertion Statement
    $insert_query = "INSERT INTO blood_requests (acceptor_id, blood_type_id, patient_name, units_requested, is_urgent, status, request_date) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param(
        $stmt, 
        "iisiiss", 
        $hospital_id, 
        $blood_type_id, 
        $final_patient_name, 
        $units_requested, 
        $final_is_urgent, 
        $initial_status, 
        $current_date
    );

    // 6. Execute Transaction and Route Client Feedback View states
    if (mysqli_stmt_execute($stmt)) {
        // Redirect back to the records panel to visually display the newly added entry
        header("Location: hospital_patient_records.php?success=request_logged");
        exit();
    } else {
        header("Location: hospital_blood_requests.php?error=execution_failed");
        exit();
    }
} else {
    // Gracefully handle erratic direct URL navigation attempts
    header("Location: hospital_blood_requests.php");
    exit();
}