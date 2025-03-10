<?php
session_start();
include_once("../../includes/db-conn.php");

// Ensure the session is valid
if (!isset($_SESSION['current_user_email'])) {
    $response['success'] = false;
    $response['message'] = 'User is not logged in.';
    echo json_encode($response);
    exit();
}

// Get the JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

// Validate and sanitize input
if (!isset($data['password']) || empty($data['password'])) {
    $response['success'] = false;
    $response['message'] = 'Password cannot be empty.';
    echo json_encode($response);
    exit();
}

// Hash the password using PASSWORD_DEFAULT
$password = password_hash($data['password'], PASSWORD_DEFAULT);

// Prepare SQL query to update the password
$sql = "UPDATE users SET password=? WHERE email=?";
$stmt = $conn->prepare($sql);

// Check for SQL prepare error
if ($stmt === false) {
    $response['success'] = false;
    $response['message'] = 'SQL prepare error: ' . $conn->error;
    echo json_encode($response);
    exit();
}

// Bind parameters
$stmt->bind_param("ss", $password, $_SESSION['current_user_email']);

$response = [];
if ($stmt->execute()) {
    // Optionally, you can update the session with a flag that the password was changed
    // $_SESSION['password_updated'] = true;

    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['message'] = "Database update failed: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
