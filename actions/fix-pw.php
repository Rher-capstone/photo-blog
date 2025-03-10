<?php

// Do not run this script all the time, only when you need to. Only run it once to fix 
// passwords for the sample accounts
// sample accounts plaintext passwords:
// alice: hashed_password1
// bob: hashed_password2
// carol : hashed_password3

require_once('../includes/db-conn.php'); // Adjust the path as necessary


$query = "SELECT email, password FROM users WHERE first_name IN ('Alice','Bob','Carol')";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $plain_password = $row['password'];
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
        
        // Update the database with the hashed password
        $update_query = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update_query->bind_param('ss', $hashed_password, $email);
        $update_query->execute();
    }
}

echo "Passwords updated successfully.";

// Close the database connection
$conn->close();
?>
