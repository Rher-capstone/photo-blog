<?php
session_start();
include_once("../includes/db-conn.php");

// Get the JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

$blogId = $data['blogId'];
$creatorEmail = $data['creatorEmail'];
$title = $data['title'];
$description = $data['description'];
$deleteBlog = $data['deleteBlog'];


// Delete the blog from the database if requested
if ($deleteBlog === 'yes') {
    $sql = "DELETE FROM blogs WHERE blog_id=? AND creator_email=? AND title=? AND description=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $blogId, $creatorEmail, $title, $description);

    $response = [];
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = "Blog deleted successfully.";
        } else {
            $response['success'] = false;
            $response['message'] = "No blog found with the specified blog details.";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Database deletion failed: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
