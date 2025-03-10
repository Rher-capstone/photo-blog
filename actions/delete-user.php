<?php
session_start();
include_once("../includes/db-conn.php");

// Get the JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$deleteUser = $data['deleteUser'];
$deleteUserBlogs = $data['deleteUserBlogs'];


// Delete the user from the database if requested
if ($deleteUser === 'yes') {
    $sql = "DELETE FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);

    // Delete blogs if requested
    if ($deleteUserBlogs === 'yes') {
        $deleteBlogsSql = "DELETE FROM blogs WHERE creator_email=?";
        $deleteBlogsStmt = $conn->prepare($deleteBlogsSql);
        $deleteBlogsStmt->bind_param("s", $email);
        $deleteBlogsStmt->execute();
        $deleteBlogsStmt->close();
    }

    $response = [];
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = "User deleted successfully.";
        } else {
            $response['success'] = false;
            $response['message'] = "No user found with the specified email.";
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
