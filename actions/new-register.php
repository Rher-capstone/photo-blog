<?php
session_start();
include_once "../includes/db-conn.php";

if (isset($_POST['new-register'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $retypePassword = $_POST['retype-password'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];

    // Check if the passwords match
    if ($password !== $retypePassword) {
        $_SESSION['error'] = 'Passwords do not match.';
        header("Location: ../index.php");
        exit();
    }

    // Check if email already exists in the database
    $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($emailCount);
        $stmt->fetch();

        // If email already exists, set session error and redirect
        if ($emailCount > 0) {
            $_SESSION['error'] = 'This email is already in use. Please choose a different one.';
            header("Location: ../index.php");
            exit();
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = 'Database error. Please try again later.';
        header("Location: ../index.php");
        exit();
    }

    // Hash the password
    $hashed_pw = password_hash($password, PASSWORD_DEFAULT);

    // Create SQL query to insert the new user
    $sql = "INSERT INTO users (email, password, first_name, last_name) VALUES (?,?,?,?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssss", $email, $hashed_pw, $fname, $lname);

        if ($stmt->execute()) {
            $_SESSION['current_user_email'] = $email;
            $_SESSION['current_user_first_name'] = $fname;
            $_SESSION['current_user_last_name'] = $lname;
            $_SESSION['current_user_role'] = "blogger";

            // Default image setup
            $isLocalServer = ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1');
            if ($isLocalServer) {
                $_SESSION['user_img'] = "/photo_abcd_A/images/blankicon.jpg";
            } else {
                // For remote server, handle user-specific folder creation
                $currentUserEmail = $_SESSION['current_user_email'];
                $baseDir = $_SERVER['DOCUMENT_ROOT'] . '/images/users/';
                $uploadDir = $baseDir . $currentUserEmail;

                // Ensure the user-specific directory exists
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Path for default image
                $newImagePath = $uploadDir . '/blankicon.jpg';

                if (!file_exists($newImagePath)) {
                    $defaultImagePath = $_SERVER['DOCUMENT_ROOT'] . '/images/blankicon.jpg';
                    if (file_exists($defaultImagePath)) {
                        copy($defaultImagePath, $newImagePath);
                    }
                }

                // Set the path to the user's profile image
                $_SESSION['user_img'] = '/images/users/' . $currentUserEmail . '/blankicon.jpg';
            }

            // Close connection and redirect after successful registration
            $conn->close();
            header("Location: ../index.php");
            exit();
        } else {
            // If execution fails, redirect with an error
            $_SESSION['error'] = 'Registration failed. Please try again later.';
            header("Location: ../index.php");
            exit();
        }

        $stmt->close();
    } else {
        // Prepare failed, redirect with error
        $_SESSION['error'] = 'Database error. Please try again later.';
        header("Location: ../index.php");
        exit();
    }
}
?>
