<?php
    $isLocal = ($_SERVER['SERVER_NAME'] == 'localhost');

    if ($isLocal) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "photos_abcd";
    } else {
        $servername = "localhost";
        $username = "icsbinco_photo_a_db_user";
        $password = "ZIE9aD9ZSZ";
        $dbname = "icsbinco_photo_a_db";
    }

    // Create database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // if connection error, die
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT value FROM preferences WHERE name = 'BLOG_MODE'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION["BLOG_MODE"] = $row['value'];
    }

?>
