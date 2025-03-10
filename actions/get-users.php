<?php
session_start();
include_once("../includes/db-conn.php");


$attributes = 'email, first_name, last_name, password, active, role, created_time, modified_time, reset_token, token_expiration, token_created_time';

$sql = "SELECT $attributes FROM users";

$result = $conn->query($sql);

if (!$result) {
    // Log or display the error (for debugging)
    die("Error executing query: " . $conn->error);
}

$users = array();

// Process query results
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Add images array to the blog post
        $users[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($users);

$conn->close();

?>