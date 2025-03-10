<?php 
session_start();
require_once('../includes/db-conn.php');

$titleStr = (string)file_get_contents("php://input");
$email = $_SESSION['current_user_email'];

$newPrimaryKey = "abook-$email-$titleStr";
$value = "title:$titleStr/A:/B:/C:/D:/E:/F:/G:/H:/I:/J:/K:/L:/M:/N:/O:/P:/Q:/R:/S:/T:/U:/V:/W:/X:/Y:/Z:";

$sql = "INSERT INTO preferences (name, value) VALUES (?, ?)";
//echo $value;

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ss", $newPrimaryKey, $value);
    if ($stmt->execute()) {
        //$stmt->close();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

?>