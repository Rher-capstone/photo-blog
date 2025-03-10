<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("includes/db-conn.php");

// This file is the default page of the website. It serves as a home page and displays the public blogs.
$CURRENT_PAGE = "Index";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("includes/head-tag-contents.php"); ?>
    <link href="css/blogs.css" rel="stylesheet" type="text/css">
    <link href="css/blog-grid.css" rel="stylesheet" type="text/css">
    <link href="css/sorting.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php include("includes/top-bar.php"); ?>
<h2>Home</h2>
<?php include("includes/blogs.php"); ?>
<?php include("includes/footer.php"); ?>

<script>
    // Show alert after the page and all resources are fully loaded
    window.onload = function () {
        <?php
        if (isset($_SESSION['login_error'])) {
            echo "alert('" . htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8') . "');";
            unset($_SESSION['login_error']); // Clear the session variable after displaying it
        }
        ?>
    };
    const actionType = 'get-blogs';

    // Videos, Mixed, Photos
    const blogMode = "<?= $_SESSION['BLOG_MODE']?>";
    console.log(blogMode);

    // Sort Bar Delete Logic
    document.getElementById("blogFunctionsContainer").remove();

    if (blogMode == "Videos" || blogMode == "Mixed") {
        document.getElementById("viewOptionContainer").style.display = "none";
    }

</script>
</body>
</html>
