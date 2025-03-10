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
$firstName = $data['firstName'];

// Prepare SQL query to update the first name
$sql = "UPDATE users SET first_name=? WHERE email=?";
$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param("ss", $firstName, $_SESSION['current_user_email']);

$response = [];
if ($stmt->execute()) {
    // Update the session with the new first name
    $_SESSION['current_user_first_name'] = $firstName;

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
