<?php
// Start session and include database connection
session_start();
include_once "../includes/db-conn.php";

// Check if files and blog ID were provided
if (isset($_FILES['photos']) && isset($_POST['blog_id'])) {
    $photos = $_FILES['photos'];
    $blogId = $_POST['blog_id'];

    // Debugging output to verify received variables
    error_log("Received photos: " . print_r($photos, true));
    error_log("Received blog ID: " . $blogId);

    // Define the target directory for this blog's images
    $targetDir = "../images/{$blogId}/";

    // Ensure the directory exists or create it with proper permissions
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true); // Create with appropriate permissions
    }

    $maxWidth = 500; // Max width for resizing
    $maxHeight = 500; // Max height for resizing
    $uploadedFiles = [];
    $errors = [];

    // Loop through each uploaded file
    foreach ($photos['tmp_name'] as $key => $tmpName) {
        if ($photos['error'][$key] == 0) {
            $originalName = $photos['name'][$key];
            $finalPath = $targetDir . '/' . $originalName;

            // Determine the file type
            $fileType = mime_content_type($tmpName);

            if ($fileType === "image/gif") {
                // Skip resizing for GIFs, move directly
                if (move_uploaded_file($tmpName, $finalPath)) {
                    $uploadedFiles[] = $originalName;
                    error_log("Uploaded GIF: " . $originalName);
                } else {
                    $errors[] = [
                        'file' => $originalName,
                        'error' => 'Failed to upload GIF.'
                    ];
                    error_log("Failed to upload GIF: " . $originalName);
                }
            } else {
                // Resize non-GIF images
                try {
                    resizeImage($tmpName, $finalPath, $maxWidth, $maxHeight);
                    $uploadedFiles[] = $originalName;
                    error_log("Resized and uploaded image: " . $originalName);
                } catch (Exception $e) {
                    $errors[] = [
                        'file' => $originalName,
                        'error' => 'Failed to resize image: ' . $e->getMessage()
                    ];
                    error_log("Failed to resize image: " . $originalName . ". Error: " . $e->getMessage());
                }
            }
        } else {
            // Handle upload error
            $errors[] = [
                'file' => $photos['name'][$key],
                'error' => 'Upload error code: ' . $photos['error'][$key]
            ];
            error_log("Error uploading file: " . $photos['name'][$key] . " - Error code: " . $photos['error'][$key]);
        }
    }

    // Return response with success and failure details
    echo json_encode([
        'success' => count($uploadedFiles) > 0,
        'uploaded' => $uploadedFiles,
        'errors' => $errors,
        'message' => count($uploadedFiles) > 0
            ? 'Photos uploaded successfully.'
            : 'Failed to upload photos.'
    ]);
} else {
    // If the files or blog ID are not provided
    error_log("Invalid request: No files or blog ID provided");
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request: No files or blog ID provided',
        'files' => $_FILES,
        'post' => $_POST,
    ]);
}

// Function to resize an image
function resizeImage($sourcePath, $targetPath, $maxWidth, $maxHeight) {
    // Get original image dimensions and type
    list($origWidth, $origHeight, $imageType) = getimagesize($sourcePath);

    // Calculate new dimensions while maintaining aspect ratio
    $aspectRatio = $origWidth / $origHeight;
    if ($maxWidth / $maxHeight > $aspectRatio) {
        $newWidth = $maxHeight * $aspectRatio;
        $newHeight = $maxHeight;
    } else {
        $newWidth = $maxWidth;
        $newHeight = $maxWidth / $aspectRatio;
    }

    // Create a new image from the source
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        default:
            throw new Exception("Unsupported image type.");
    }

    // Create a new blank image with the new dimensions
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Preserve transparency for PNG
    if ($imageType == IMAGETYPE_PNG) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
    }

    // Copy and resize the old image into the new image
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    // Save the new image to the target path
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($newImage, $targetPath, 85); // Adjust quality as needed
            break;
        case IMAGETYPE_PNG:
            imagepng($newImage, $targetPath, 8); // Compression level (0-9)
            break;
    }

    // Free memory
    imagedestroy($sourceImage);
    imagedestroy($newImage);
}
?>






