<?php
// Include authentication check
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Backend</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .container { max-width: 800px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Blog Backend</h1>
            <div>
                <a href="posts.php" class="btn btn-outline-primary me-2">Manage Posts</a>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
        
        <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">
            Post added successfully!
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h2>Add New Blog Post</h2>
            </div>
            <div class="card-body">
                <form action="save_post.php" method="post">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Excerpt</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="2" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="6" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image_name" class="form-label">Image Name</label>
                        <input type="text" class="form-control" id="image_name" name="image_name" placeholder="e.g. apfel" required>
                        <div class="form-text">Will be used in: https://ik.imagekit.io/mywine/andiblog/tr:w-800,h-400/[image_name].jpg</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags" placeholder="Tag1, Tag2, Tag3" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="published" name="published" checked>
                        <label class="form-check-label" for="published">Publish immediately</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Post</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 