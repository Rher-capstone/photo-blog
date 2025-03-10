<?php
session_start();

$userEmail = isset($_GET['email']) ? $_GET['email'] : '';

// Define the base directory relative to the root of the server
$baseDir = $_SERVER['DOCUMENT_ROOT'] . '/images/users/';
$uploadDir = $baseDir . $userEmail;

// Check if the directory exists
if (!file_exists($uploadDir)) {
    $imageUrl = (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false)
        ? 'images/blankicon.jpg' 
        : 'https://' . $_SERVER['HTTP_HOST'] . '/images/blankicon.jpg';

    echo json_encode(['image' => $imageUrl]);
    exit;
}

// Detect if we are running on a local server or a remote server
$isLocalServer = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;

// List all files in the user's directory
$files = array_diff(scandir($uploadDir), array('..', '.'));

// Check if there are any images
if (empty($files)) {
    // If no files found, return default image
    $imageUrl = $isLocalServer
        ? 'images/blankicon.jpg'
        : 'https://' . $_SERVER['HTTP_HOST'] . '/images/blankicon.jpg'; 

    echo json_encode(['image' => $imageUrl]);
    exit;
}

// Sort files by modification time
usort($files, function($a, $b) use ($uploadDir) {
    return filemtime($uploadDir . '/' . $b) - filemtime($uploadDir . '/' . $a);
});

// Get the newest file
$newestFile = reset($files);

// Construct the image URL based on the environment
$imageUrl = $isLocalServer
    ? 'images/users/' . urlencode($userEmail) . '/' . $newestFile
    : 'https://' . $_SERVER['HTTP_HOST'] . '/images/users/' . urlencode($userEmail) . '/' . $newestFile;

echo json_encode(['image' => $imageUrl]);
?>
