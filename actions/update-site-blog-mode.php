<?php
session_start();
include_once("../includes/db-conn.php");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['siteBlogMode']) && !empty($data['siteBlogMode'])) {
    $siteBlogMode = $data['siteBlogMode'];
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input: siteBlogMode is required']);
    exit;
}

$name = 'BLOG_MODE';

// SQL query to check if the "BLOG_MODE" preference already exists
$sql_check = "SELECT COUNT(*) FROM preferences WHERE name = ?";
$stmt_check = $conn->prepare($sql_check);

if ($stmt_check === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare check statement: ' . $conn->error]);
    exit;
}

// Bind the fixed name 'BLOG_MODE' to the query
$stmt_check->bind_param("s", $name);
$stmt_check->execute();
$stmt_check->bind_result($count);
$stmt_check->fetch();
$stmt_check->close();

// Prepare SQL query to update or insert the "BLOG_MODE" preference
if ($count > 0) {
    // If "BLOG_MODE" exists, update the value
    $sql = "UPDATE preferences SET value = ?, notes = 'User-defined blog mode preference' WHERE name = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare update statement: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("ss", $siteBlogMode, $name);

} else {
    // If "BLOG_MODE" does not exist, insert new row
    $sql = "INSERT INTO preferences (name, value, notes) VALUES (?, ?, 'User-defined blog mode preference')";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare insert statement: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ss", $name, $siteBlogMode);
}

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'BLOG_MODE preference updated or inserted successfully';
} else {
    $response['success'] = false;
    $response['message'] = "Database operation failed: " . $stmt->error;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
