<?php
// Include authentication check
require_once 'auth.php';

// Include database configuration
require_once 'config.php';

// For debugging
error_log("Upload Started");
error_log("FILES: " . print_r($_FILES, true));
error_log("POST: " . print_r($_POST, true));

// Check if it's an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
error_log("Is AJAX: " . ($isAjax ? 'yes' : 'no'));

// Set content type for AJAX responses
if ($isAjax) {
    header('Content-Type: application/json');
}

// Check if file was uploaded
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    error_log("File upload detected");
    
    $image_name = $_POST['image_name'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $returnUrl = $_POST['return_url'] ?? 'index.php';
    
    error_log("Image name: $image_name, Slug: $slug, Return URL: $returnUrl");
    
    // Validate image_name
    if (empty($image_name)) {
        error_log("Image name is empty");
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Image name is required']);
            exit();
        } else {
            header("Location: $returnUrl?error=Image name is required");
            exit();
        }
    }
    
    // Get the file
    $image = $_FILES['image'];
    error_log("Image filename: " . $image['name'] . ", type: " . $image['type'] . ", size: " . $image['size']);
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/jpg'];
    if (!in_array($image['type'], $allowedTypes)) {
        error_log("Invalid file type: " . $image['type']);
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Only JPG/JPEG images are allowed']);
            exit();
        } else {
            header("Location: $returnUrl?error=Only JPG/JPEG images are allowed");
            exit();
        }
    }
    
    // Upload to ImageKit
    $ch = curl_init();
    
    // Prepare the cURL request
    curl_setopt($ch, CURLOPT_URL, 'https://upload.imagekit.io/api/v1/files/upload');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    
    // Set headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Authorization: Basic cHJpdmF0ZVsfadfsddddddddTaDhybS93dGs9Og==' // your imagekit key
    ]);
    
    // Create a CURLFile object
    $cfile = new CURLFile(
        $image['tmp_name'],
        $image['type'],
        $image_name . '.jpg'
    );
    
    // Set form data
    $data = [
        'file' => $cfile,
        'fileName' => $image_name . '.jpg',
        'useUniqueFileName' => 'false',
        'folder' => 'blog'
    ];
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    
    // Execute the cURL request
    error_log("Sending cURL request to ImageKit");
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    error_log("ImageKit Response - HTTP Code: $httpCode");
    if (!empty($curlError)) {
        error_log("cURL Error: $curlError");
    }
    if ($response) {
        error_log("Response: " . substr($response, 0, 1000));
    }
    
    curl_close($ch);
    
    // Process the response
    if ($httpCode == 200) {
        error_log("Upload successful");
        $responseData = json_decode($response, true);
        $imageUrl = "https://ik.imagekit.io/jeaniblog/blog/tr:w-800,h-400/" . $image_name . ".jpg";
        
        // If slug is provided, update the post's featured image
        if (!empty($slug)) {
            error_log("Updating post with slug: $slug");
            // Format the image URL
            $featured_image = $imageUrl;
            
            // Prepare the SQL statement
            $sql = "UPDATE jeanine_blog_posts SET featured_image = :featured_image, updated_date = NOW() WHERE slug = :slug";
            
            try {
                // Prepare and execute the statement
                $stmt = $pdo->prepare($sql);
                
                // Bind parameters
                $stmt->bindParam(':featured_image', $featured_image);
                $stmt->bindParam(':slug', $slug);
                
                // Execute statement
                $stmt->execute();
                error_log("Database update successful");
                
                if ($isAjax) {
                    echo json_encode([
                        'success' => true, 
                        'image_name' => $image_name,
                        'image_url' => $imageUrl
                    ]);
                    exit();
                } else {
                    // Redirect back with success message
                    // Add JavaScript to store uploaded name in localStorage
                    $storeKey = "lastUploadedImageName_" . $slug;
                    $script = "<script>
                            localStorage.setItem('" . htmlspecialchars($storeKey) . "', '" . htmlspecialchars($image_name) . "');
                            window.location.href = '" . htmlspecialchars($returnUrl) . "?image_uploaded=1';
                        </script>";
                    echo $script;
                    exit();
                }
            } catch(PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                if ($isAjax) {
                    echo json_encode(['success' => false, 'message' => "Could not update image: " . $e->getMessage()]);
                    exit();
                } else {
                    header("Location: $returnUrl?error=" . urlencode("Could not update image: " . $e->getMessage()));
                    exit();
                }
            }
        } else {
            error_log("No slug provided, just returning success");
            // Just return success
            if ($isAjax) {
                echo json_encode([
                    'success' => true, 
                    'image_name' => $image_name,
                    'image_url' => $imageUrl
                ]);
                exit();
            } else {
                // Redirect back with success message
                // Add JavaScript to store uploaded name in localStorage
                $storeKey = !empty($slug) ? "lastUploadedImageName_" . $slug : "lastUploadedImageName";
                $script = "<script>
                        localStorage.setItem('" . htmlspecialchars($storeKey) . "', '" . htmlspecialchars($image_name) . "');
                        window.location.href = '" . htmlspecialchars($returnUrl) . "?image_uploaded=1';
                    </script>";
                echo $script;
                exit();
            }
        }
    } else {
        error_log("Upload failed with HTTP code: $httpCode");
        // Handle error
        $error = "Failed to upload image to ImageKit. Status code: $httpCode. ";
        if (!empty($curlError)) {
            $error .= "cURL error: $curlError. ";
        }
        
        if ($response) {
            $responseData = json_decode($response, true);
            if (isset($responseData['message'])) {
                $error .= $responseData['message'];
            } else {
                $error .= "Raw response: " . substr($response, 0, 100);
            }
        }
        
        error_log("Error message: $error");
        
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => $error]);
            exit();
        } else {
            header("Location: $returnUrl?error=" . urlencode($error));
            exit();
        }
    }
} else {
    error_log("No file upload detected or file upload error");
    // If no file was uploaded or there was an error
    $error = "No file uploaded or upload error occurred";
    if (isset($_FILES['image'])) {
        $errorCode = $_FILES['image']['error'];
        error_log("File array exists but error code: $errorCode");
        
        switch ($errorCode) {
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
                $error = "Unknown upload error code: $errorCode";
                break;
        }
    } else {
        error_log("File array doesn't exist");
    }
    
    error_log("Error message: $error");
    
    if ($isAjax) {
        echo json_encode([
            'success' => false, 
            'message' => $error,
            'post_data' => $_POST,
            'files_data' => isset($_FILES) ? $_FILES : 'No files'
        ]);
        exit();
    } else {
        $returnUrl = $_POST['return_url'] ?? 'index.php';
        header("Location: $returnUrl?error=" . urlencode($error));
        exit();
    }
} 