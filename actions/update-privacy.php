<?php
session_start();
include_once("../includes/db-conn.php");

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['current_user_email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Retrieve and validate the privacy status from the request
$data = json_decode(file_get_contents('php://input'), true);
$privacy = isset($data['privacy']) ? $data['privacy'] : null;

if (!in_array($privacy, ['public', 'private'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid privacy status']);
    exit;
}

$currentUserEmail = $_SESSION['current_user_email'];

// Update the privacy status for all blogs of the current user
$sql = "UPDATE blogs SET privacy_filter = ? WHERE creator_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $privacy, $currentUserEmail);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Privacy updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database update failed']);
}

$stmt->close();
$conn->close();
?>
