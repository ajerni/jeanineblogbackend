<?php
// Include authentication check
require_once 'auth.php';

// Include database configuration
require_once 'config.php';

// Check if slug is provided
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    
    try {
        // First, get the current status
        $query = "SELECT published FROM blog_posts WHERE slug = :slug";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $post = $stmt->fetch();
            $newStatus = $post['published'] ? 0 : 1; // Toggle the status
            
            // Update the status
            $updateQuery = "UPDATE blog_posts SET published = :status WHERE slug = :slug";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(':status', $newStatus);
            $updateStmt->bindParam(':slug', $slug);
            $updateStmt->execute();
            
            // Redirect back to posts page
            header("Location: posts.php?status_changed=1");
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