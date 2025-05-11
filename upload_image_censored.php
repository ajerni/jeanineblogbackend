<?php
// Include authentication check
require_once 'auth.php';

// Include database configuration
require_once 'config.php';

// Check if file was uploaded
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $image_name = $_POST['image_name'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $returnUrl = $_POST['return_url'] ?? 'index.php';
    
    // Validate image_name
    if (empty($image_name)) {
        header("Location: $returnUrl?error=Image name is required");
        exit();
    }
    
    // Get the file
    $image = $_FILES['image'];
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/jpg'];
    if (!in_array($image['type'], $allowedTypes)) {
        header("Location: $returnUrl?error=Only JPG/JPEG images are allowed");
        exit();
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
        'Authorization: Basic cHJpdmF0ZsdfadsfdddddT3JMWlRTaDhybS93dGs9Og==' // put you own from imagekit
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
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    // Process the response
    if ($httpCode == 200) {
        $responseData = json_decode($response, true);
        
        // If slug is provided, update the post's featured image
        if (!empty($slug)) {
            // Format the image URL
            $featured_image = "https://ik.imagekit.io/jeaniblog/blog/tr:w-800,h-400/" . $image_name . ".jpg";
            
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
                
                // Redirect back with success message
                header("Location: $returnUrl?image_uploaded=1");
                exit();
            } catch(PDOException $e) {
                header("Location: $returnUrl?error=" . urlencode("Could not update image: " . $e->getMessage()));
                exit();
            }
        } else {
            // Just redirect with success message
            header("Location: $returnUrl?image_uploaded=1");
            exit();
        }
    } else {
        // Handle error
        $error = "Failed to upload image to ImageKit. ";
        if ($response) {
            $responseData = json_decode($response, true);
            if (isset($responseData['message'])) {
                $error .= $responseData['message'];
            }
        }
        
        header("Location: $returnUrl?error=" . urlencode($error));
        exit();
    }
} else {
    // If no file was uploaded or there was an error
    $error = "No file uploaded or upload error occurred";
    if (isset($_FILES['image']) && $_FILES['image']['error'] > 0) {
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
                $error = "Unknown upload error";
                break;
        }
    }
    
    $returnUrl = $_POST['return_url'] ?? 'index.php';
    header("Location: $returnUrl?error=" . urlencode($error));
    exit();
} 