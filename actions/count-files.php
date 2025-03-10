<?php
/* 
    This file is used by the index/blogs section to count the number of photo files in a post.
*/
$blog_id = $_GET['blog_id'];
$directory = "../images/$blog_id";

if (is_dir($directory)) {
    $files = array_diff(scandir($directory), array('..', '.'));
    echo json_encode(array('fileCount' => count($files)));
} else {
    echo json_encode(array('fileCount' => 0));
}
?>