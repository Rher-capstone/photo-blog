<!--
    Array Structure:
    $blogs[]
        [array pair] 
            [table]
                [attributes array]
            [image pair]
                [blog/image directory]
                [directory file array]
                    [filename index]
    

    Example:
    $blogs [0] ['table'] ['title']
    
    $blogs: blog-image pair array
    [0]: First element of $blogs.
    ['table']: Pair selection. Table attributes may vary based on query.
    ['title']: Table Attribute

    Output will give: 'A for Art'
-->
<?php
@session_start();
include_once("includes/db-conn.php");

/**
 * Use Cases:
 *
 * Get All Public Blogs
 * Get Logged User Blogs
 * Get Other Users Public Blogs
 * Get Admin View?
 */
$attributes = 'blog_id, creator_email, title, description, event_date, creation_date, modification_date, privacy_filter, youtube_link';
$where = "WHERE privacy_filter = 'public'";
// Retrieve and sanitize filter inputs
$title = isset($_GET['title']) ? $conn->real_escape_string($_GET['title']) : '';
$startDate = isset($_GET['start_date']) ? $conn->real_escape_string($_GET['start_date']) : '';
$endDate = isset($_GET['end_date']) ? $conn->real_escape_string($_GET['end_date']) : '';
$sortOrder = isset($_GET['sort_order']) ? strtolower($_GET['sort_order']) : 'asc';

// Build WHERE clause for filters
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
function getBlogs($where) {
    global $attributes;
    if (!isset($_SESSION['blog_display'])) {
        $_SESSION['blog_display'] = 'public';
    }

    $display_type = $_SESSION['blog_display'];


    // If not logged or selecting user from public blog
    switch ($display_type) {
        // All public blogs
        case 'public':
            return "SELECT $attributes FROM blogs $where";

        // All logged in user blogs
        case 'self':
            $self = $_SESSION['current_user_email'];
            return "SELECT $attributes FROM blogs $where AND WHERE creator_email = '{$self}'";

        // All selected user blogs
        case 'select':
            if (isset($_GET['select_user'])) {
                $select_user = $_GET['select_user'];
            }
            return "SELECT $attributes FROM blogs $where AND WHERE creator_email = '{$select_user}' AND privacy_filter = 'public'";

        case 'default':
            $select_user = 'alice@example.com';
            return "SELECT $attributes FROM blogs WHERE $where";
    }

}

$sql = getBlogs($where);
//echo "<p>Generated SQL Query: $sql</p>";
$result = $conn->query($sql);

if (!$result) {
    // Log or display the error (for debugging)
    die("Error executing query: " . $conn->error);
}

// Array of Blog & Image data pairs
$blogs = array();

// Show rows if any are found. (If the query.result is > 0)
if ($result->num_rows > 0) {
    while($table_row = $result->fetch_assoc()) {
        // Image directory
        $blog_dir = "images/{$table_row['blog_id']}/";

        // Image array of directory.
        $img_names = [];

        // Check if directory is exists. If not, return an empty array.
        $scanDir = @scandir($blog_dir);
        if ($scanDir == true) {
            $img_names = array_values(array_diff(scandir($blog_dir), array('..', '.')));
        }

        // Bind directory and image array.
        $blog_images = array('dir' => $blog_dir, 'img_names' => $img_names);

        // Bind blog info and images
        $blog_pair = array('table' => $table_row, 'images' => $blog_images);

        // Add pair to collection
        $blogs[] = $blog_pair;
    }
}

$conn->close();
$_GET['blog_pairs'] = json_encode($blogs);

?>