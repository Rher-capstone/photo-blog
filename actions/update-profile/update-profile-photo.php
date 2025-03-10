<?php
session_start();

// Ensure the user is logged
if (!isset($_SESSION['current_user_email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$currentUserEmail = $_SESSION['current_user_email'];

// Define the base directory relative to the root of server
$baseDir = $_SERVER['DOCUMENT_ROOT'] . '/images/users/';
$uploadDir = $baseDir . $currentUserEmail;

// Check if the directory exists, if not, create it
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create user directory.']);
        exit;
    }
}

// Check if a file was uploaded
if (isset($_FILES['profile-photo']) && $_FILES['profile-photo']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile-photo']['tmp_name'];
    $fileName = $_FILES['profile-photo']['name'];
    $fileSize = $_FILES['profile-photo']['size'];
    $fileType = $_FILES['profile-photo']['type'];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Check if the file is a valid image (JPG, JPEG, PNG)
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileExt, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, and PNG are allowed.']);
        exit;
    }

    // Generate a file name using the timestamp
    $newFileName = time() . '.' . $fileExt;

    // Set the full path where the file will be saved
    $destPath = $uploadDir . '/' . $newFileName;

    // Move the uploaded file to the user's directory
    if (move_uploaded_file($fileTmpPath, $destPath)) {
        // Save the file path in the session for use in profile settings
        $_SESSION['user_img'] = '/images/users/' . $currentUserEmail . '/' . $newFileName;

        echo json_encode([
            'success' => true,
            'message' => 'Profile photo uploaded successfully!',
            'filePath' => $_SESSION['user_img']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move the uploaded file.']);
    }
} else {
    // Handle upload errors
    $uploadError = $_FILES['profile-photo']['error'];
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
    ];

    echo json_encode(['success' => false, 'message' => $errorMessages[$uploadError] ?? 'Unknown error during file upload.']);
}
?>
