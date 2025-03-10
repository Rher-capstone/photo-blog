<?php
session_start();
include_once("includes/db-conn.php");
$CURRENT_PAGE = "Settings";

$base_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/photo_abcd_A/';

if (!isset($_SESSION['current_user_email'])) {
    header('Location: ' . $base_url . 'index.php');
    exit();
}

if (isset($_SESSION['current_user_email'])) {
    $userImgDir = "images/users/".$_SESSION['current_user_email'];
    $userImg = @scandir($userImgDir);
    if ($userImg != false) {
        $userImg = $userImgDir."/".array_values(array_diff($userImg, array('..', '.')))[0];
    } else {
        $userImg = "images/blankicon.jpg";
    }
    $widget_name = $_SESSION['current_user_first_name'];

} else {
    $userImg = "images/blankicon.jpg";
    $widget_name = "Guest";
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("includes/head-tag-contents.php"); ?>
        <link rel="stylesheet" href="css/settings.css">
    </head>

    <body>
        <?php include("includes/top-bar.php");?>
        <h2>Settings</h2>
        <?php include("includes/user-settings.php");?>
        <?php include("includes/settings-modals.php");?>
    </body>
    <footer>
        <?php include("includes/footer.php");?>
    </footer>
</html>