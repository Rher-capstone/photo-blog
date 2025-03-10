<?php
session_start();
include_once("../includes/db-conn.php");

// Init search values
$title = isset($_GET['title']) ? $_GET['title'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc'; // Default to 'asc'
$creatorId = $_GET['creator_id'];

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

// Dynamically get the logged-in user's email
$where = "WHERE creator_email = '$creatorId'";

// Apply search filters based on the user's input
// Title/Description filter
if (!empty($title)) {
    $searchTerm = $conn->real_escape_string($title);
    $where .= " AND (title LIKE '%" . $searchTerm . "%' OR description LIKE '%" . $searchTerm . "%')"; // Match words in title or description
}

// Creation Date range filter
if (!empty($startDate)) {
    $where .= " AND creation_date >= '" . $conn->real_escape_string($startDate) . "'";
}
if (!empty($endDate)) {
    $endDate = $conn->real_escape_string($endDate) . ' 23:59:59';
    $where .= " AND creation_date <= '" . $endDate . "'";
}

// Modify SQL query to dynamically add ORDER BY clause based on user input (title or event date)
$sql = "SELECT $attributes FROM blogs $where ORDER BY $orderBy";

$result = $conn->query($sql);

if (!$result) {
    // Log or display the error (for debugging)
    die("Error executing query: " . $conn->error);
}

$blogPosts = array();

// Process query results
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get the images for the blog post
        $blog_id = $row['blog_id'];
        $imageDir = "../images/$blog_id/";
        $images = array();

        if (is_dir($imageDir)) {
            // Scan for image files in the directory
            $imageFiles = scandir($imageDir);
            foreach ($imageFiles as $file) {
                // Only include jpg and png files
                if (preg_match('/\.(jpg|jpeg|png)$/i', $file)) {
                    $images[] = $file;
                }
                
            }
        }

        // Add images array to the blog post
        $row['images'] = $images;
        $blogPosts[] = $row;
    }
    
} else {
    // Send a response indicating no blogs were found
    echo json_encode(['message' => 'No matching blogs found']);
    exit;
}

header('Content-Type: application/json');
echo json_encode($blogPosts);

$conn->close();
?>