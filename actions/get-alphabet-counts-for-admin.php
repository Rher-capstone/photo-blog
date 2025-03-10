<?php
session_start();
include_once("../includes/db-conn.php");

$base_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/photo_abcd_A/';

if (!isset($_SESSION['current_user_email']) || !isset($_SESSION['current_user_role']) || $_SESSION['current_user_role'] !== 'admin') {
    header('Location: ' . $base_url . 'index.php');
    exit();
}

// Retrieve the email from the GET request
$email = isset($_GET['email']) ? trim($_GET['email'], '"') : '';

$sql = "
    SELECT LEFT(title, 1) AS Letter, COUNT(*) AS LetterCount 
    FROM blogs 
    WHERE creator_email = ?
    GROUP BY LEFT(title, 1)
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error executing query: " . $conn->error);
}

// Array to hold all letters
$alphabetCounts = array_fill_keys(range('A', 'Z'), 0);

while ($row = $result->fetch_assoc()) {
    // Update counts based on the result
    $alphabetCounts[$row['Letter']] = (int)$row['LetterCount'];
}

$blogs = [];
foreach ($alphabetCounts as $letter => $count) {
    $blogs[] = ['Letter' => $letter, 'LetterCount' => $count];
}

header('Content-Type: application/json');
echo json_encode($blogs);

$stmt->close();
$conn->close();
?>