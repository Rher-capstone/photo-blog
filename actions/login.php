<?php
session_start();
require_once('../includes/db-conn.php');

if (isset($_GET['email']) && isset($_GET['password'])) {
    $email = $_GET['email'];
    $password = $_GET['password'];

    // Prepare the query to get the password, first name, and last name
    $stmt = $conn->prepare("SELECT password, first_name, last_name, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if a user with that email exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password, $first_name, $last_name, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // If valid, assign the email, first name, last name, role, and user img to the session
            $_SESSION['current_user_email'] = $email;
            $_SESSION['current_user_first_name'] = $first_name;
            $_SESSION['current_user_last_name'] = $last_name;
            $_SESSION['current_user_role'] = $role;

            // Check if the server is local
            $isLocalServer = ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1');

            if ($isLocalServer) {
                // If on a local server, use the relative path to the default blank icon
                $_SESSION['user_img'] = "/photo_abcd_A/images/blankicon.jpg";
            } else {
                // Set the most recent profile photo as the user's profile image
                $userDir = $_SERVER['DOCUMENT_ROOT'] . "/images/users/" . $email;
                if (is_dir($userDir)) {
                    $files = array_diff(scandir($userDir), array('.', '..'));
                    
                    if (!empty($files)) {
                        // Find the most recent file (based on last modified time)
                        $mostRecentFile = null;
                        $mostRecentTime = 0;

                        foreach ($files as $file) {
                            $filePath = $userDir . "/" . $file;
                            // Get the last modified time of the file
                            $fileTime = filemtime($filePath);

                            // Update if this file is more recent
                            if ($fileTime > $mostRecentTime) {
                                $mostRecentFile = $file;
                                $mostRecentTime = $fileTime;
                            }
                        }

                        // If a file was found, set the session to point to it
                        if ($mostRecentFile) {
                            $_SESSION['user_img'] = "/images/users/" . $email . "/" . $mostRecentFile;
                        }
                    }
                }
            }

            // Redirect to the my-blogs page
            header("Location: ../my-blogs.php");
            exit();
        } else {
            // Incorrect password
            $_SESSION['login_error'] = 'Sorry, your username or password was incorrect. Please double-check your information. ';
            header("Location: ../index.php");
            exit();
        }
    } else {
        // No user found with that email
        $_SESSION['login_error'] = 'Sorry, your username or password was incorrect. Please double-check your information.';
        header("Location: ../index.php");
        exit();
    }

    $stmt->close();
} else {
    header("Location: ../index.php");
    exit();
}

$conn->close();
?>
