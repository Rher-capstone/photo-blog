<?php
session_start();
include_once("../includes/db-conn.php");

// Get the JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

// Validate and sanitize input
$blogId = $data['blogId'];
$creatorEmail = $data['creatorEmail'];
$title = $data['title'];
$description = $data['description'];
$eventDate = $data['eventDate'];
$creationDate = $data['creationDate'];
$modificationDate = $data['modificationDate'];
$privacyFilter = $data['privacyFilter'];
$youtubeLink = $data['youtubeLink'];

// Update the blog in the database
$sql = "UPDATE blogs SET creator_email=?, title=?, description=?, event_date=?, creation_date=?, modification_date=?, privacy_filter=?, youtube_link=? WHERE blog_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssi", $creatorEmail, $title, $description, $eventDate, $creationDate, $modificationDate, $privacyFilter, $youtubeLink, $blogId);

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
