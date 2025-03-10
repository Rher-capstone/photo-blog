<?php
session_start();
include_once("includes/db-conn.php");
$CURRENT_PAGE = "My Blogs";

if (!isset($_SESSION['current_user_email']) || !isset($_SESSION['current_user_role'])) {
    header('Location: ' . $base_url . 'index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("includes/head-tag-contents.php");?>
        <link href="css/sorting.css" rel="stylesheet" type="text/css">
        <link href="css/my-blogs-grid.css" rel="stylesheet" type="text/css">
        
    </head>

    <body>
        <?php include("includes/top-bar.php"); ?>
        <?php include("includes/new-blog-modal.php"); ?>

        <h2>My Blogs</h2>
        <?php include("actions/my-blogs-grid.php"); ?>
        

        <?php include("includes/footer.php"); ?>
        <script>
            const actionType = 'get-my-blogs'; // Set action to fetch my blogs

            // Remove view dropdown (not used in my-blogs page)
            document.getElementById("viewOptionContainer").remove();
        </script>
    </body>
</html>
