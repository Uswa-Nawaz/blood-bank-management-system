<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connect.php';

// Authentication Guard
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Hospital') {
    header("Location: login.html");
    exit();
}

$hospital_id = $_SESSION['user_id'];

// 1. Fetch official hospital profile name
$name_query = "SELECT fullName FROM users WHERE userId = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $name_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$name_result = mysqli_stmt_get_result($stmt);
$user_data = mysqli_fetch_assoc($name_result);
$hospital_display_name = $user_data['fullName'] ?? "Hospital Partner";

// 2. Capture and sanitize active filters from GET request parameters
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_blood = isset($_GET['blood_type']) ? trim($_GET['blood_type']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';

// 3. Construct dynamic search & filter base query string
$query = "SELECT r.request_id, r.patient_name, r.units_requested, r.request_date, r.status, r.is_urgent, t.bloodGroup 
          FROM blood_requests r
          INNER JOIN bloodtypes t ON r.blood_type_id = t.bloodTypeId
          WHERE r.acceptor_id = ?";

$params = [$hospital_id];
$types = "i";

// Append search conditions dynamically if applied
if ($search_term !== '') {
    $query .= " AND (r.patient_name LIKE ? OR r.request_id LIKE ?)";
    $like_term = "%" . $search_term . "%";
    $params[] = $like_term;
    $params[] = $like_term;
    $types .= "ss";
}

// Append blood type filter condition if applied
if ($filter_blood !== '') {
    $query .= " AND t.bloodGroup = ?";
    $params[] = $filter_blood;
    $types .= "s";
}

// Append status filter condition if applied
if ($filter_status !== '') {
    if ($filter_status === 'Critical') {
        $query .= " AND r.status = 'Pending' AND r.is_urgent = 1";
    } elseif ($filter_status === 'Pending') {
        $query .= " AND r.status = 'Pending' AND r.is_urgent = 0";
    } elseif ($filter_status === 'Stable') {
        $query .= " AND r.status IN ('Approved', 'Fulfilled')";
    } elseif ($filter_status === 'Discharged') {
        $query .= " AND r.status = 'Rejected'";
    }
}

// Apply consistent ordering
$query .= " ORDER BY r.request_date DESC, r.request_id DESC";

$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$patients_result = mysqli_stmt_get_result($stmt);
$total_records = mysqli_num_rows($patients_result);

// 4. Gather emergency badge counters
$emergency_query = "SELECT COUNT(*) AS emergency_count FROM blood_requests WHERE acceptor_id = ? AND status = 'Pending' AND is_urgent = 1";
$stmt = mysqli_prepare($conn, $emergency_query);
mysqli_stmt_bind_param($stmt, "i", $hospital_id);
mysqli_stmt_execute($stmt);
$emergency_result = mysqli_stmt_get_result($stmt);
$emergency_cases = mysqli_fetch_assoc($emergency_result)['emergency_count'] ?? 0;