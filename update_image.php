<?php
// Include authentication check
require_once 'auth.php';

// Include database configuration
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $slug = $_POST['slug'] ?? '';
    $image_name = $_POST['image_name'] ?? '';
    
    if (empty($slug) || empty($image_name)) {
        header("Location: posts.php?error=Missing required fields");
        exit();
    }
    
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
        header("Location: posts.php?image_updated=1");
        exit();
    } catch(PDOException $e) {
        header("Location: posts.php?error=" . urlencode("Could not update image: " . $e->getMessage()));
        exit();
    }
} else {
    // If not a POST request, redirect to posts page
    header("Location: posts.php");
    exit();
} 