<?php
// Include authentication check
require_once 'auth.php';

// Include database configuration
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'] ?? '';
    $excerpt = $_POST['excerpt'] ?? '';
    $content = $_POST['content'] ?? '';
    $image_name = $_POST['image_name'] ?? '';
    $tags_string = $_POST['tags'] ?? '';
    $published = isset($_POST['published']) ? 1 : 0;
    
    // Generate slug from title
    $slug = generateSlug($title);
    
    // Format the image URL
    $featured_image = "https://ik.imagekit.io/mywine/andiblog/tr:w-800,h-400/" . $image_name . ".jpg";
    
    // Format tags as JSON
    $tags = formatTags($tags_string);
    
    // Prepare the SQL statement
    $sql = "INSERT INTO blog_posts (
                title,
                slug,
                excerpt,
                content,
                featured_image,
                published_date,
                updated_date,
                tags,
                published
            ) VALUES (
                :title,
                :slug,
                :excerpt,
                :content,
                :featured_image,
                NOW(),
                NOW(),
                :tags,
                :published
            )";
    
    try {
        // Prepare and execute the statement
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':excerpt', $excerpt);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':featured_image', $featured_image);
        $stmt->bindParam(':tags', $tags);
        $stmt->bindParam(':published', $published);
        
        // Execute statement
        $stmt->execute();
        
        // Redirect back with success message
        header("Location: index.php?success=1");
        exit();
    } catch(PDOException $e) {
        die("ERROR: Could not execute $sql. " . $e->getMessage());
    }
}

/**
 * Generate a URL-friendly slug from a string
 * 
 * @param string $string The string to convert
 * @return string The slug
 */
function generateSlug($string) {
    // Replace non-alphanumeric characters with hyphens
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
    // Convert to lowercase
    $slug = strtolower($slug);
    // Remove leading/trailing hyphens
    $slug = trim($slug, '-');
    return $slug;
}

/**
 * Format tags as JSON array
 * 
 * @param string $tags_string Comma-separated tags
 * @return string JSON encoded array of tags
 */
function formatTags($tags_string) {
    // Split by comma and trim whitespace
    $tags_array = array_map('trim', explode(',', $tags_string));
    // Filter out empty tags
    $tags_array = array_filter($tags_array);
    // Return JSON encoded array
    return json_encode($tags_array);
} 