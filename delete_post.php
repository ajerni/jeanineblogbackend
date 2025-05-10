<?php
// Include authentication check
require_once 'auth.php';

// Include database configuration
require_once 'config.php';

// Check if slug is provided
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    
    try {
        // Check if the post exists
        $checkQuery = "SELECT slug FROM blog_posts WHERE slug = :slug";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->bindParam(':slug', $slug);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            // Delete the post
            $deleteQuery = "DELETE FROM blog_posts WHERE slug = :slug";
            $deleteStmt = $pdo->prepare($deleteQuery);
            $deleteStmt->bindParam(':slug', $slug);
            $deleteStmt->execute();
            
            // Redirect back to posts page
            header("Location: posts.php?deleted=1");
            exit();
        } else {
            // Post not found
            header("Location: posts.php?error=post_not_found");
            exit();
        }
    } catch(PDOException $e) {
        die("ERROR: Could not execute query. " . $e->getMessage());
    }
} else {
    // No slug provided
    header("Location: posts.php?error=no_slug");
    exit();
} 