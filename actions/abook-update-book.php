<?php 
session_start();
require_once('../includes/db-conn.php');
$updateStr = explode("|", (string)file_get_contents("php://input"));
$self = $_SESSION['current_user_email'];
$oldTitle = $updateStr[0];
$book = $updateStr[1];

$oldName = "abook-$self-$oldTitle";
$newTitle = explode("/A", $book)[0];
$newTitle = explode(":", $newTitle)[1];
$newPrimaryKey = "abook-$self-$newTitle";


// UPDATE alphabet_book SET value WHERE key = creator_email && value like $titleStr;
$sql = "UPDATE preferences SET name=?, value=? WHERE name=? && value like CONCAT('%', ? ,'%')";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ssss", $newPrimaryKey, $book, $oldName, $oldTitle);
    if ($stmt->execute()) {
        
    } else {
        echo "Error: " . $stmt->error;
    }
}

$stmt->close();

?>