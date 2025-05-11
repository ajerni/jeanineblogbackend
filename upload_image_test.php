<?php
// Include authentication check
require_once 'auth.php';

// This is a debug version with extra logging
error_log("Upload test started");

// Set content type for AJAX responses
header('Content-Type: application/json');

// Debug uploaded files
error_log("FILES: " . print_r($_FILES, true));
error_log("POST: " . print_r($_POST, true));

// Check if file was uploaded
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    error_log("File upload detected");
    
    // Return success for testing
    echo json_encode([
        'success' => true,
        'image_name' => $_POST['image_name'] ?? 'test',
        'image_url' => 'https://ik.imagekit.io/jeaniblog/blog/tr:w-800,h-400/' . ($_POST['image_name'] ?? 'test') . '.jpg',
        'message' => 'Image uploaded successfully (test mode)'
    ]);
} else {
    error_log("No file upload detected");
    // Check for specific error
    $error = "No file uploaded or upload error occurred";
    if (isset($_FILES['image'])) {
        error_log("File array exists but error: " . $_FILES['image']['error']);
        switch ($_FILES['image']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $error = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $error = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $error = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $error = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $error = "A PHP extension stopped the file upload";
                break;
            default:
                $error = "Unknown upload error: " . $_FILES['image']['error'];
                break;
        }
    } else {
        error_log("File array doesn't exist");
    }
    
    echo json_encode([
        'success' => false, 
        'message' => $error,
        'post_data' => $_POST,
        'files_data' => $_FILES
    ]);
} 