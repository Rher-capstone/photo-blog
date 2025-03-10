<?php 
session_start();
require_once('../includes/db-conn.php');
$bookTitle = (string)file_get_contents("php://input");
$self = $_SESSION['current_user_email'];
$primaryKey = "abook-$self-$bookTitle";

$sql = "DELETE FROM preferences WHERE name=?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('s', $primaryKey);
    if ($stmt->execute()) {
        
    } else {
        echo "Error: " . $stmt->error;
        
    }
}
$stmt->close();


?>