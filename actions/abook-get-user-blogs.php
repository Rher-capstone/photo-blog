<?php 
session_start();
require_once('../includes/db-conn.php');

$self = $_SESSION['current_user_email'];
$sql = "SELECT blog_id, creator_email, title, description, event_date, creation_date, modification_date, privacy_filter FROM blogs WHERE creator_email = '{$self}'";

$result = $conn->query($sql);

if (!$result) {
    // Log or display the error (for debugging)
    die("Error executing query: " . $conn->error);
}

// Array of Blog & Image data pairs
$blogs = array();

// Show rows if any are found. (If the query.result is > 0)
if ($result->num_rows > 0) {
    foreach($result as $row) {
        // Relative directory for scandir.
        $rel_dir = "../images/{$row['blog_id']}/";

        $blog_files;

        // Get name and extension of image file (after filtering unrelated entries).
        // DOES NOT SORT OUT INVALID FILE EXTENSIONS
        $scanDir = @scandir($rel_dir);

        if ($scanDir != false) {
            $blog_files = array_values(array_diff($scanDir, array('..', '.')));
        } else {
            $blog_files = [];
        }

        // Create key-value pair.
        // Example: blog['images'] = ['example1.jpg', 'example2.png', 'example3.gif', ...]
        $blog_images = array('images' => $blog_files);

        // Merge blog key-value pair to blog row.
        (array)$row = array_merge((array)$row, $blog_images);

        // Add blog row to array
        $blogs[] = $row;
    }
}

$conn->close();
echo json_encode($blogs);
header('Content-Type: application/json');

?>