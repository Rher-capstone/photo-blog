<?php
session_start();
include_once("../includes/db-conn.php");

// Get the JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

// Validate and sanitize input
$email = $data['email'];
$firstName = $data['firstName'];
$lastName = $data['lastName'];
$password = password_hash($data['password'], PASSWORD_DEFAULT); // Hash the password for security
$active = $data['active'];
$role = $data['role'];

// Update the user in the database
$sql = "UPDATE users SET first_name=?, last_name=?, password=?, active=?, role=? WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $firstName, $lastName, $password, $active, $role, $email);

$response = [];
if ($stmt->execute()) {
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
