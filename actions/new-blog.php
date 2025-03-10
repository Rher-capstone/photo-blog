<?php
session_start();
include_once "../includes/db-conn.php";

if (isset($_POST['create-new-blog'])) {
    $email = $_SESSION['current_user_email'];
    $title = $_POST['title'] ?? ''; // Avoid undefined variable
    $desc = $_POST['desc'];
    $eventDate = $_POST['event-date'];
    $visibility = getVisibility();
    $youtube_link = $_POST['youtube_link'];

    // Validate the title of the blog
    if (!preg_match('/^[a-zA-Z0-9].*/', $title)) {
        echo "<script>
            alert('Title must start with a letter or number only.');
            window.history.back();
        </script>";
        exit;
    }

    // Insert blog details into the database
    $sql = 'INSERT INTO blogs (creator_email, title, description, event_date, privacy_filter, youtube_link) VALUE (?,?,?,?,?,?)';

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssssss', $email, $title, $desc, $eventDate, $visibility, $youtube_link);

        if ($stmt->execute()) {
            // Blog created successfully
            $blog_id = $stmt->insert_id;
            $blog_dir = '../images/' . $blog_id;
            mkdir($blog_dir, 0755, true); // Create blog directory

            // Handle multiple file uploads
            if (isset($_FILES['new-blog-images'])) {
                $files = $_FILES['new-blog-images'];
                $maxWidth = 500; // Max width for resizing
                $maxHeight = 500; // Max height for resizing

                foreach ($files['tmp_name'] as $key => $tmp_name) {
                    if ($files['error'][$key] == 0) {
                        $originalName = $files['name'][$key];
                        $finalPath = $blog_dir . '/' . $originalName;

                        // Determine the file type
                        $fileType = mime_content_type($tmp_name);

                        if ($fileType === "image/gif") {
                            // Skip resizing for GIFs, move directly
                            if (move_uploaded_file($tmp_name, $finalPath)) {
                                echo "Uploaded GIF: " . $originalName . "<br>";
                            } else {
                                echo "Failed to upload GIF: " . $originalName . "<br>";
                            }
                        } else {
                            // Resize non-GIF images
                            try {
                                resizeImage($tmp_name, $finalPath, $maxWidth, $maxHeight);
                            } catch (Exception $e) {
                                echo "Failed to resize image: " . $originalName . ". Error: " . $e->getMessage() . "<br>";
                            }
                        }
                    } else {
                        header('Location: ../index.php');
                        echo "Error uploading file: " . $files['name'][$key] . "<br>";
                        exit;
                    }
                }
            }

            // Redirect to index after processing
            header('Location: ../index.php');
            exit;
        } else {
            echo 'Error: ' . $stmt->error;
        }
        $stmt->close();
    }
}

function getVisibility() {
    return isset($_POST['visibility']) ? 'public' : 'private';
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


