<?php
session_start();
include_once("../includes/db-conn.php");

// Init search values
$title = isset($_GET['title']) ? $_GET['title'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc'; // Default to 'asc'

// Sanitize the sortOrder input to handle both title and event date sorting
switch (strtolower($sortOrder)) {
    case 'desc':
        $orderBy = 'title DESC';
        break;
    case 'date_asc':
        $orderBy = 'event_date ASC';
        break;
    case 'date_des':
        $orderBy = 'event_date DESC';
        break;
    default:
        $orderBy = 'title ASC'; // Default to alphabetical sorting by title in ascending order
}
$attributes = 'blog_id, creator_email, title, description, event_date, creation_date, modification_date, privacy_filter';
$where = "WHERE privacy_filter = 'public'";


// Apply search filters based on the user's input
// Title/Description sort
if (!empty($title)) {
    $searchTerm = $conn->real_escape_string($title);
    $where .= " AND (title LIKE '%" . $searchTerm . "%' OR description LIKE '%" . $searchTerm . "%')"; // Match words in title or description
}
// Creation Date range sort
if (!empty($startDate)) {
    $where .= " AND creation_date >= '" . $conn->real_escape_string($startDate) . "'";
}
if (!empty($endDate)) {
    $where .= " AND creation_date <= '" . $conn->real_escape_string($endDate) . "'";
}

// Modify SQL query to add ORDER BY clause for sorting alphabetically by title
$sql = "SELECT $attributes FROM blogs $where ORDER BY $orderBy";

$result = $conn->query($sql);

if (!$result) {
    // Log or display the error (for debugging)
    die("Error executing query: " . $conn->error);
}

$blogPosts = array();

// Process query results
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Get the images for the blog post
        $blog_id = $row['blog_id'];
        $imageDir = "../images/$blog_id/";
        $images = array();
        $scanDir = @scandir($imageDir);
        $blog_files;

        if ($scanDir != false) {
            $blog_files = array_values(array_diff($scanDir, array('..', '.')));
        } else {
            $blog_files = [];
        }

        // Add images array to the blog post
        $blog_images = array('images' => $blog_files);
        $row['images'] = $images;
        (array)$row = array_merge((array)$row, $blog_images);
        $blogPosts[] = $row;

    }
}else {
    // Send a response indicating no blogs were found
    echo json_encode(['message' => 'No matching blogs found']);
    exit;
}

header('Content-Type: application/json');
echo json_encode($blogPosts);

$conn->close();

?>